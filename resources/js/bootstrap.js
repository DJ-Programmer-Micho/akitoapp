/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */


// Ensure that Echo is used after it has been initialized
// document.addEventListener('DOMContentLoaded', function () {
//     // Check if Echo is defined
//     if (window.Echo) {
//         window.Echo.private('orders')
//             .listen('EventOrderStatusUpdated', (e) => {
//                 // Update the notification count here
//                 const badgeElement = document.querySelector('.topbar-badge');
//                 if (badgeElement) {
//                     let notificationCount = parseInt(badgeElement.innerText) || 0;
//                     notificationCount++;
//                     badgeElement.innerText = notificationCount;
//                     // showOrderUpdateNotification(message);
//                     console.log(e);
//                     // showOrderUpdateNotification(e);
//                 }
//             });
//     } else {
//         console.error('Echo is not initialized');
//     }
// });


import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// console.log('Ita a me Mario');
import Echo from '@ably/laravel-echo';
import * as Ably from 'ably';

window.Ably = Ably; // Exposing Ably to the global window object
window.Echo = new Echo({
    broadcaster: 'ably',
    key: import.meta.env.VITE_ABLY_KEY, // Ensure this key is set in your .env file
    disable_public_channels: import.meta.env.VITE_ABLY_DISABLE_PUBLIC_CHANNELS,
    token_expiry: import.meta.env.VITE_ABLY_TOKEN_EXPIRY
});

window.Echo.connector.ably.connection.on(stateChange => {
    if (stateChange.current === 'connected') {
        console.log('connected to ably server');
    }
});

document.addEventListener('DOMContentLoaded', function () {
    // console.log('Ita a me Luigi');
    // Check if Echo is defined
    if (window.Echo) {
        // const driverId = window.Laravel.driver_id; // Get the driver ID from a global variable or similar method
        // console.log('Ita a me Waligi');
        // console.log('asd', driverId);
        // Listening to the private channel 'orders'
        // window.Echo.channel('AdminChannel').subscribed(() => {
        //     // console.log('Subscribed to AdminChannel');
        // }).listen('EventOrderStatusUpdated', (event) => {
        //     // console.log('Received EventOrderStatusUpdated event:', event);
        // });
        
        // // Listening to a DriverChannel with a specific driver ID
        // window.Echo.channel(`DriverChannel.${driverId}`).subscribed(() => {
        //     // console.log(`Subscribed to DriverChannel.${driverId}`);
        // }).listen('EventOrderStatusUpdated', (event) => {
        //     // console.log('Received EventOrderStatusUpdated event for driver:', event);
        // });
    } else {
        console.error('Echo is not initialized');
    }
});
/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo';

// import Pusher from 'pusher-js';
// window.Pusher = Pusher;

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: import.meta.env.VITE_PUSHER_APP_KEY,
//     cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'mt1',
//     wsHost: import.meta.env.VITE_PUSHER_HOST ? import.meta.env.VITE_PUSHER_HOST : `ws-${import.meta.env.VITE_PUSHER_APP_CLUSTER}.pusher.com`,
//     wsPort: import.meta.env.VITE_PUSHER_PORT ?? 80,
//     wssPort: import.meta.env.VITE_PUSHER_PORT ?? 443,
//     forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? 'https') === 'https',
//     enabledTransports: ['ws', 'wss'],
// });
