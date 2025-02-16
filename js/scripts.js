document.addEventListener('DOMContentLoaded', function () {
    // Evitar reenvío de formularios
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
});