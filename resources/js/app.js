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

    const budgetForm = document.querySelector('[data-budget-form]');

    if (budgetForm) {
        const currency = new Intl.NumberFormat('es-AR', {
            style: 'currency',
            currency: 'ARS',
        });
        const totalElement = budgetForm.querySelector('[data-budget-total]');

        const updateBudgetTotal = () => {
            let total = 0;

            budgetForm.querySelectorAll('[data-budget-item]').forEach((item) => {
                const toggle = item.querySelector('[data-budget-toggle]');
                const quantity = item.querySelector('[data-budget-quantity]');
                const selected = toggle?.checked ?? false;

                if (quantity) {
                    quantity.disabled = !selected;
                }

                if (selected) {
                    total += Number(item.dataset.unitPrice ?? 0) * Number(quantity?.value ?? 1);
                }
            });

            if (totalElement) {
                totalElement.textContent = currency.format(total);
            }
        };

        budgetForm.querySelectorAll('[data-budget-toggle], [data-budget-quantity]').forEach((input) => {
            input.addEventListener('change', updateBudgetTotal);
            input.addEventListener('input', updateBudgetTotal);
        });

        updateBudgetTotal();
    }

    document.querySelectorAll('[data-repair-part]').forEach((item) => {
        const toggle = item.querySelector('[data-repair-part-toggle]');
        const quantity = item.querySelector('[data-repair-part-quantity]');

        const updateAvailability = () => {
            if (quantity) {
                quantity.disabled = !toggle?.checked || toggle.disabled;
            }
        };

        toggle?.addEventListener('change', updateAvailability);
        updateAvailability();
    });

    document.querySelector('[data-print-report]')?.addEventListener('click', () => {
        window.print();
    });
});
