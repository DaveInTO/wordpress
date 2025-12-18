<?php
/*
Plugin Name: Yummy Contact With Push Notifications
Description: Handles dynamic contact and form submissions via AWS SNS, email, and optional file uploads.
Version: 1.2
Author: Dave Minogue
 */

use Aws\Sns\SnsClient;
use Aws\Exception\AwsException;
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

//Set Up DB
register_activation_hook(__FILE__, 'yummy_contact_install');

function yummy_contact_install() {
    global $wpdb;

    $table = $wpdb->prefix . 'yummy_push_subscriptions';
    $charset = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        endpoint TEXT NOT NULL,
        p256dh VARCHAR(255) NOT NULL,
        auth VARCHAR(255) NOT NULL,
        created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY endpoint (endpoint(191))
    ) $charset;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
}

add_action('wp_ajax_save_push_subscription', 'yummy_save_push_subscription');

function yummy_save_push_subscription() {
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Unauthorized', 403);
    }

    $sub = json_decode(file_get_contents('php://input'), true);
    if (!$sub || empty($sub['endpoint'])) {
        wp_send_json_error('Invalid subscription');
    }

    global $wpdb;
    $table = $wpdb->prefix . 'yummy_push_subscriptions';

    $wpdb->replace($table, [
        'endpoint' => $sub['endpoint'],
        'p256dh'   => $sub['keys']['p256dh'],
        'auth'     => $sub['keys']['auth'],
        'user_id'  => get_current_user_id(),
    ], ['%s','%s','%s','%d']);

    wp_send_json_success();
}

if (!class_exists('Aws\Sns\SnsClient')) {
	require_once __DIR__ . '/vendor/autoload.php';
}

/**
 * Register unified REST API endpoint
 */
add_action('rest_api_init', function() {
	register_rest_route('contact/v1', '/send', [
		'methods' => 'POST',
		'callback' => 'yummy_contact_handle_form',
		'permission_callback' => '__return_true',
	]);
});


function yummy_send_web_push($title, $body, $url = '') {
    global $wpdb;

    $subs = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}yummy_push_subscriptions");
    if (!$subs) return;

    $auth = [
        'VAPID' => [
            'subject' => 'mailto:admin@yoursite.com',
            'publicKey' => get_option('yummy_vapid_public'),
            'privateKey' => get_option('yummy_vapid_private'),
        ]
    ];

    $webPush = new WebPush($auth);

    foreach ($subs as $sub) {
        $subscription = Subscription::create([
            'endpoint' => $sub->endpoint,
            'publicKey' => $sub->p256dh,
            'authToken' => $sub->auth,
        ]);

        $payload = json_encode([
            'title' => $title,
            'body'  => $body,
            'url'   => $url,
        ]);

        $webPush->queueNotification($subscription, $payload);
    }

    // Actually send all queued notifications
    foreach ($webPush->flush() as $report) {
        if (!$report->isSuccess()) {
            error_log('Push failed: ' . $report->getEndpoint());
        } else {
            error_log('Push sent: ' . $report->getEndpoint());
        }
    }
}
/**
 * Handle incoming form (JSON or multipart)
 */
function yummy_contact_handle_form($request) {
	$email = '';
	$formType = '';
	$params = $request->get_json_params();

	// If multipart/form-data (file uploads)
	if (empty($params)) {
		$params = $request->get_body_params();
	}

	// Sanitize all incoming fields dynamically
	$clean = [];
	foreach ($params as $k => $v) {
		$clean[$k] = is_string($v) ? sanitize_text_field($v) : $v;
	}
	$attachments = [];
if (!empty($_FILES)) {
    foreach ($_FILES as $file) {

        // Multiple files
        if (is_array($file['tmp_name'])) {
            foreach ($file['tmp_name'] as $i => $tmp_name) {
                if (
                    !empty($tmp_name) &&
                    $file['error'][$i] === UPLOAD_ERR_OK &&
                    is_uploaded_file($tmp_name)
                ) {
                    $original_name = sanitize_file_name($file['name'][$i]);

                    // Create a temp file with the correct filename
                    $new_path = wp_tempnam($original_name);
                    copy($tmp_name, $new_path);

                    $attachments[] = $new_path;
                    $clean['attachment'][] = $original_name;
                }
            }
        }
        // Single file
        else {
            if (
                !empty($file['tmp_name']) &&
                $file['error'] === UPLOAD_ERR_OK &&
                is_uploaded_file($file['tmp_name'])
            ) {
                $original_name = sanitize_file_name($file['name']);

                $new_path = wp_tempnam($original_name);
                copy($file['tmp_name'], $new_path);

                $attachments[] = $new_path;
                $clean['attachment'][] = $original_name;
            }
        }
    }
}
	// Load admin-configured email & phone
	$admin_email = get_option('yummy_contact_email', get_bloginfo('admin_email'));
	$admin_phone = get_option('yummy_contact_phone', '16474007782');

	$subject = !empty($clean['subject']) ? sanitize_text_field($clean['subject']) : 'New Contact Form Submission';

	// Build pretty HTML email
	$body = '
<table style="width:100%; max-width:600px; margin:0 auto; border-collapse: collapse; font-family: Arial, sans-serif; border:1px solid #ddd; border-radius:8px; overflow:hidden;">
    <tr style="background:#fff;">
	<td style="text-align:center; padding:20px; background:#f8f8f8;">
	    <img src="https://www.yummycatering.ca/wp-content/uploads/2018/03/Text-Only-Transparent-300x44.png" alt="Yummy Catering" style="max-width:200px; height:auto;">
	</td>
    </tr>
    <tr>
	<td style="padding:20px;">
	    <h2 style="color:#333; font-size:20px; margin-bottom:15px;">' . esc_html($subject) . '</h2>
	    <table style="width:100%; border-collapse: collapse;">
';

	foreach ($clean as $key => $value) {
		if ($key === 'subject') continue; // Already used as email subject
		if ($key === 'email') {$email = $value;} 
		if ($key === 'formType') {$formType = $value;continue;} 
		$body .= '<tr>';
		$body .= '<td style="padding:8px; font-weight:bold; border:1px solid #eee; background:#f9f9f9;">' . esc_html($key) . '</td>';
		$body .= '<td style="padding:8px; border:1px solid #eee;">' . esc_html(is_array($value) ? implode(', ', $value) : $value) . '</td>';
		$body .= '</tr>';
	}

	$body .= '
	    </table>
	</td>
    </tr>
    <tr style="background:#f8f8f8;">
	<td style="text-align:center; padding:10px; color:#999; font-size:12px;">
	    &copy; ' . date('Y') . ' Yummy Catering. All rights reserved.
	</td>
    </tr>
</table>
';

	$headers = ['Content-Type: text/html; charset=UTF-8'];

	// Send email first

	// Send email first
	wp_mail($admin_email, $subject, $body, $headers, $attachments);
	wp_mail('daveminogue@yahoo.com', $subject, $body, $headers, $attachments);


	yummy_send_web_push(
		"{$formType} Form Submission from {$email}",
		'A new contact form submission was received',
		admin_url('admin.php')
	);
	// SNS Topic
	$topicArn = 'arn:aws:sns:us-east-1:141341305899:ContactUS';
	$result = null;
	try {
		$sns = new Aws\Sns\SnsClient([
			'version' => 'latest',
			'region'  => 'ca-central-1',
			'credentials' => [
				'key'    => defined('AWS_ACCESS_KEY_ID') ? AWS_ACCESS_KEY_ID : '',
				'secret' => defined('AWS_SECRET_ACCESS_KEY') ? AWS_SECRET_ACCESS_KEY : '',
			],
		]);
		$sns->SetSMSAttributes([
			'attributes' => [
				'DefaultSenderID' => 'YCatering',
				'DefaultSMSType' => 'Transactional'
			]
		]);
		// Build message
		$message = json_encode([
			'data'      => $clean,
			'timestamp' => current_time('mysql'),
			'email'     => $admin_email,
			'phone'     => $admin_phone,
		], JSON_PRETTY_PRINT);

		// Publish to SNS
		$result = $sns->publish([
			'PhoneNumber' => "+".$admin_phone,
			'Message'  => "{$formType} Form Submission from {$email}",
		]);

	} catch (\Throwable $e) {
		// Convert any PHP Error to Exception for PHP 8
		if (!$e instanceof \Exception) {
			$e = new \Exception($e->getMessage(), $e->getCode(), null);
		}
		return new WP_Error('sns_error', $e->getMessage(), ['status' => 500]);
	}
	catch (AwsException $e) {
		echo "Error sending SMS: " . $e->getMessage() . "\n";
	}

	return rest_ensure_response(['success' => true,'status'=>$result->toArray()]);
}
/**
 * Admin Settings
 */
add_action('admin_menu', function() {
	add_options_page(
		'Yummy Contact Settings',
		'Yummy Contact',
		'manage_options',
		'yummy-contact-settings',
		'yummy_contact_settings_page'
	);
});

function yummy_contact_settings_page() {
?>
    <div class="wrap">
	<h1>Yummy Contact Settings</h1>
	<form method="post" action="options.php">
<?php
	settings_fields('yummy_contact_settings_group');
	do_settings_sections('yummy_contact_settings');
	submit_button();
?>
<h2>Push Notifications</h2>

<p>
    Enable push notifications on <strong>this device/browser</strong>
    to receive admin alerts when a contact form is submitted.
</p>

<button type="button" class="button button-primary" id="enable-push-btn">
    Enable Push Notifications
</button>

<p id="push-status" style="margin-top:10px;"></p>
	</form>
    </div>
<?php
}

add_action('admin_init', function() {
	register_setting('yummy_contact_settings_group', 'yummy_contact_email');
	register_setting('yummy_contact_settings_group', 'yummy_contact_phone');
	register_setting('yummy_contact_settings_group', 'yummy_vapid_public');
	register_setting('yummy_contact_settings_group', 'yummy_vapid_private');

	add_settings_section(
		'yummy_contact_main_section',
		'Notification Settings',
		function() {
			echo '<p>Configure the default email and phone number used for Yummy Contact notifications.</p>';
		},
		'yummy_contact_settings'
	);

	add_settings_field(
		'yummy_contact_email_field',
		'Notification Email',
		function() {
			$value = esc_attr(get_option('yummy_contact_email', get_bloginfo('admin_email')));
			echo '<input type="email" name="yummy_contact_email" value="' . $value . '" class="regular-text" />';
		},
		'yummy_contact_settings',
		'yummy_contact_main_section'
	);

	add_settings_field(
		'yummy_contact_phone_field',
		'Notification Phone',
		function() {
			$value = esc_attr(get_option('yummy_contact_phone', ''));
			echo '<input type="text" name="yummy_contact_phone" value="' . $value . '" class="regular-text" />';
		},
		'yummy_contact_settings',
		'yummy_contact_main_section'
	);

	add_settings_field(
		'yummy_contact_public_vapid_field',
		'Public VAPID',
		function() {
			$value = esc_attr(get_option('yummy_vapid_public', ''));
			echo '<input type="text" name="yummy_vapid_public" value="' . $value . '" class="regular-text" />';
		},
		'yummy_contact_settings',
		'yummy_contact_main_section'
	);
	add_settings_field(
		'yummy_contact_private_vapid_field',
		'Private VAPID',
		function() {
			$value = esc_attr(get_option('yummy_vapid_private', ''));
			echo '<input type="password" name="yummy_vapid_private" value="' . $value . '" class="regular-text" />';
		},
		'yummy_contact_settings',
		'yummy_contact_main_section'
	);
});
add_shortcode('contact_us_app', 'yummy_contact_shortcode');
add_action('admin_enqueue_scripts', function($hook) {
    if ($hook !== 'settings_page_yummy-contact-settings') {
        return;
    }

    wp_enqueue_script(
        'yummy-push-admin',
        plugin_dir_url(__FILE__) . 'assets/push-admin.js',
        [],
        '1.0',
        true
    );

    wp_localize_script(
        'yummy-push-admin',
        'YUMMY_PUSH',
        [
            'vapidPublic' => get_option('yummy_vapid_public'),
            'ajaxUrl'     => admin_url('admin-ajax.php'),
        ]
    );
});

function yummy_contact_shortcode($atts = [], $content = null) {
	ob_start();
?>
    <div id="contact-us-app"></div>
<?php
	return ob_get_clean();
}


add_action('wp_enqueue_scripts', function() {
    if (!is_page('contact-us')) return; // target page slug or ID directly

    $plugin_url = plugin_dir_url(__FILE__);
    wp_enqueue_style('contact-us-css', $plugin_url . 'assets/contact-us.css', [], '1.0');
    wp_enqueue_script('contact-us-js', $plugin_url . 'assets/index.js', [], '1.0', true);

    // Only localize push for admins
    if (is_user_logged_in() && current_user_can('manage_options')) {
        wp_localize_script('contact-us-js', 'YUMMY_PUSH', [
            'vapidPublic' => get_option('yummy_vapid_public'),
            'ajaxUrl' => admin_url('admin-ajax.php'),
        ]);
    }
},20);

