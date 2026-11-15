import 'bootstrap-icons/font/bootstrap-icons.css';
import * as bootstrap from 'bootstrap';
import './bootstrap';

window.bootstrap = bootstrap;

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach((element) => {
        new bootstrap.Tooltip(element);
    });

    // Samakan arah chevron grup sidebar dengan state buka/tutup
    document.querySelectorAll('.admin-nav-group').forEach((group) => {
        const panel = group.querySelector('.collapse');

        if (!panel) {
            return;
        }

        panel.addEventListener('show.bs.collapse', () => group.classList.add('is-open'));
        panel.addEventListener('hide.bs.collapse', () => group.classList.remove('is-open'));
    });
});
