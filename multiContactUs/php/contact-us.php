<?php
/*
Plugin Name: Yummy Contact AWS Integration
Description: Handles dynamic contact and form submissions via AWS SNS, email, and optional file uploads.
Version: 1.2
Author: Your Company
 */

use Aws\Sns\SnsClient;
use Aws\Exception\AwsException;

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

/**
 * Handle incoming form (JSON or multipart)
 */
function yummy_contact_handle_form($request) {
    $params = $request->get_json_params();

    // Sanitize all incoming fields dynamically
    $clean = [];
    foreach ($params as $k => $v) {
        $clean[$k] = is_string($v) ? sanitize_text_field($v) : $v;
    }

    // Load admin-configured email & phone
    $admin_email = get_option('yummy_contact_email', get_bloginfo('admin_email'));
    $admin_phone = get_option('yummy_contact_phone', '');

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
    wp_mail($admin_email, $subject, $body, $headers);

    // SNS Topic
    $topicArn = 'arn:aws:sns:us-east-1:141341305899:ContactUS';
$result = null;
    try {
        $sns = new Aws\Sns\SnsClient([
            'version' => 'latest',
            'region'  => 'us-east-1',
            'credentials' => [
                'key'    => defined('AWS_ACCESS_KEY_ID') ? AWS_ACCESS_KEY_ID : '',
                'secret' => defined('AWS_SECRET_ACCESS_KEY') ? AWS_SECRET_ACCESS_KEY : '',
            ],
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
 'PhoneNumber' => "+16474007782",
            'Message'  => $message,
            'Subject'  => 'New Contact Form Submission',
        ]);

    } catch (\Throwable $e) {
        // Convert any PHP Error to Exception for PHP 8
        if (!$e instanceof \Exception) {
            $e = new \Exception($e->getMessage(), $e->getCode(), null);
        }
        return new WP_Error('sns_error', $e->getMessage(), ['status' => 500]);
    }

    return rest_ensure_response(['success' => true,'status'=>$result]);
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
	</form>
    </div>
<?php
}

add_action('admin_init', function() {
	register_setting('yummy_contact_settings_group', 'yummy_contact_email');
	register_setting('yummy_contact_settings_group', 'yummy_contact_phone');

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
});
add_shortcode('contact_us_app', 'yummy_contact_shortcode');

function yummy_contact_shortcode($atts = [], $content = null) {
	ob_start();
?>
    <div id="contact-us-app"></div>
<?php
	return ob_get_clean();
}





add_action('wp_enqueue_scripts', function() {
	if (!is_singular()) return; // only on single pages/posts

	global $post;
	if ($post && has_shortcode($post->post_content, 'contact_us_app')) {
		$plugin_url = plugin_dir_url(__FILE__);
		wp_enqueue_style('contact-us-css', $plugin_url . 'assets/contact-us.css', [], '1.0');
		wp_enqueue_script('contact-us-js', $plugin_url . 'assets/index.js', [], '1.0', true);
	}
});

