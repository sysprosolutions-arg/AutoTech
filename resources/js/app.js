import * as bootstrap from 'bootstrap';

window.bootstrap = bootstrap;

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-password-toggle]').forEach((button) => {
        button.addEventListener('click', () => {
            const inputId = button.getAttribute('aria-controls');
            const input = document.getElementById(inputId);

            if (!input) {
                return;
            }

            const isVisible = input.type === 'text';
            input.type = isVisible ? 'password' : 'text';
            button.setAttribute('aria-pressed', String(!isVisible));
            button.setAttribute('aria-label', isVisible ? 'Mostrar contraseña' : 'Ocultar contraseña');

            const icon = button.querySelector('i');
            icon?.classList.toggle('bi-eye', isVisible);
            icon?.classList.toggle('bi-eye-slash', !isVisible);
        });
    });

    const sidebar = document.querySelector('[data-app-sidebar]');
    const sidebarToggle = document.querySelector('[data-sidebar-toggle]');

    sidebarToggle?.addEventListener('click', () => {
        sidebar?.classList.toggle('is-open');
        document.body.classList.toggle('sidebar-open');
    });
});
