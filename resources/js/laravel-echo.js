import Echo from "laravel-echo";
window.Echo = new Echo({
    broadcaster: 'socket.io'
})