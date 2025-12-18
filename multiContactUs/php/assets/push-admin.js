document.addEventListener('DOMContentLoaded', async () => {
    const btn = document.getElementById('enable-push-btn');
    const status = document.getElementById('push-status');

    if (!btn || !YUMMY_PUSH?.vapidPublic) return;

    let isSubscribed = false;
    let registration = null;

    if ('serviceWorker' in navigator && 'PushManager' in window) {
        try {
            registration = await navigator.serviceWorker.register(
                '/wp-content/plugins/contact-us/assets/sw.js'
            );
            const sub = await registration.pushManager.getSubscription();
            if (sub) {
                isSubscribed = true;
            }
        } catch (err) {
            console.error('Service worker or push check failed:', err);
        }
    } else {
        status.textContent = 'Push not supported in this browser.';
        btn.disabled = true;
        return;
    }

    if (isSubscribed) {
        btn.textContent = 'Push Enabled ✅';
        status.textContent = 'This device will now receive admin notifications.';
    } else {
        btn.textContent = 'Enable Push Notifications';
    }

    // Attach click handler regardless of subscription state
    btn.addEventListener('click', async () => {
        btn.disabled = true;
        btn.textContent = 'Enabling…';
        status.textContent = '';

        try {
            if (!registration) {
                registration = await navigator.serviceWorker.register(
                    '/wp-content/plugins/contact-us/assets/sw.js'
                );
            }

            const permission = await Notification.requestPermission();
            if (permission !== 'granted') {
                throw new Error('Notification permission denied');
            }

            const sub = await registration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: YUMMY_PUSH.vapidPublic
            });

            const res = await fetch(
                YUMMY_PUSH.ajaxUrl + '?action=save_push_subscription',
                {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(sub)
                }
            );

            if (!res.ok) {
                throw new Error('Failed to save subscription');
            }

            btn.textContent = 'Push Enabled ✅';
            status.textContent = 'This device will now receive admin notifications.';
        } catch (err) {
            console.error(err);
            btn.disabled = false;
            btn.textContent = 'Enable Push Notifications';
            status.textContent = err.message;
            status.style.color = 'red';
        }
    });
});
async function subscribeToPush() {
  if (!('serviceWorker' in navigator)) return;
  if (!window.YUMMY_PUSH) return; // make sure localized data exists

  try {
    const reg = await navigator.serviceWorker.register(
      '/wp-content/plugins/yummy-contact/assets/sw.js'
    );

    const sub = await reg.pushManager.subscribe({
      userVisibleOnly: true,
      applicationServerKey: urlBase64ToUint8Array(YUMMY_PUSH.vapidPublic)
    });

    await fetch(`${YUMMY_PUSH.ajaxUrl}?action=save_push_subscription`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(sub)
    });

    console.log('Push subscription successful');
    document.getElementById('push-status').innerText = 'Push Enabled ✅';
  } catch (err) {
    console.error('Push subscription failed:', err);
    document.getElementById('push-status').innerText = 'Push subscription failed';
  }
}

