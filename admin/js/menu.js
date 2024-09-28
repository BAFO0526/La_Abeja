document.addEventListener('DOMContentLoaded', function() {
    const menuToggleButton = document.querySelector('.navbar-toggler');
    const menuCollapse = document.querySelector('#sidebar');
    // Cerrar el menú si se hace clic fuera de él
    document.addEventListener('click', function(event) {
        if (menuCollapse.classList.contains('show') && !menuCollapse.contains(event.target) && !menuToggleButton.contains(event.target)) {
            menuCollapse.classList.remove('show'); // Quita la clase 'show' para cerrar el menú
        }
    });
});
