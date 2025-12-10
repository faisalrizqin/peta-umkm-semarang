// assets/js/script.js

function toggleSidebar() {
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main-content');

    if (window.innerWidth <= 768) {
        sidebar.classList.toggle('active');
    }
}

// Close sidebar when clicking outside on mobile
document.addEventListener('click', function (event) {
    const sidebar = document.querySelector('.sidebar');
    const toggleBtn = document.querySelector('.toggle-sidebar');

    if (window.innerWidth <= 768 && sidebar) {
        if (!sidebar.contains(event.target) && event.target !== toggleBtn) {
            sidebar.classList.remove('active');
        }
    }
});

// Prevent sidebar from closing when clicking inside it
document.addEventListener('DOMContentLoaded', function () {
    const sidebar = document.querySelector('.sidebar');
    if (sidebar) {
        sidebar.addEventListener('click', function (event) {
            event.stopPropagation();
        });
    }
});