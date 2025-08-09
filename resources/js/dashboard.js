import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

const echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true
});

// Assume you have a map instance (e.g., from Google Maps, Leaflet, etc.)
// and a way to store driver markers, for example, in an object.
const driverMarkers = {};

echo.join('drivers.online')
    .here((drivers) => {
        // This is called when you first join the channel
        // You can use this to initialize the map with online drivers
        console.log('Currently online drivers:', drivers);
    })
    .joining((driver) => {
        // This is called when a new driver joins the channel
        console.log('Driver joined:', driver);
    })
    .leaving((driver) => {
        // This is called when a driver leaves the channel
        console.log('Driver left:', driver);
        // You can remove the driver's marker from the map here
        if (driverMarkers[driver.id]) {
            driverMarkers[driver.id].setMap(null);
            delete driverMarkers[driver.id];
        }
    })
    .listen('DriverLocationUpdated', (e) => {
        console.log('DriverLocationUpdated event received:', e);
        const { driverId, location } = e;

        // Check if a marker for this driver already exists
        if (driverMarkers[driverId]) {
            // Update the existing marker's position
            driverMarkers[driverId].setPosition(new google.maps.LatLng(location.latitude, location.longitude));
        } else {
            // Create a new marker for the driver
            driverMarkers[driverId] = new google.maps.Marker({
                position: new google.maps.LatLng(location.latitude, location.longitude),
                map: map, // your map instance
                title: `Driver ${driverId}`
            });
        }
    })
    .error((error) => {
        console.error('Pusher error:', error);
    });
