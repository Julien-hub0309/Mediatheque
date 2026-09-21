document.addEventListener('DOMContentLoaded', () => {

    // ---------- Année dynamique dans le footer ----------
    const yearEl = document.getElementById('currentYear');
    if (yearEl) {
        yearEl.textContent = new Date().getFullYear();
    }

    // ---------- Menu mobile ----------
    const menuToggle = document.querySelector('.menu-toggle');
    const navList = document.querySelector('.nav-list');

    if (menuToggle && navList) {
        menuToggle.addEventListener('click', () => {
            const isActive = navList.classList.toggle('active');
            menuToggle.setAttribute('aria-expanded', isActive);
        });

        // Ferme le menu quand on clique sur un lien
        navList.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', () => {
                navList.classList.remove('active');
                menuToggle.setAttribute('aria-expanded', 'false');
            });
        });

        // Ferme le menu si on clique en dehors
        document.addEventListener('click', (event) => {
            const clickedOutside = !navList.contains(event.target) && !menuToggle.contains(event.target);
            if (clickedOutside && navList.classList.contains('active')) {
                navList.classList.remove('active');
                menuToggle.setAttribute('aria-expanded', 'false');
            }
        });
    }

    // ---------- Filtrage instantané des tableaux (sans rechargement) ----------
    // Complète la recherche côté serveur : dès que l'utilisateur tape,
    // les lignes du tableau visible sont filtrées en direct.
    const searchInput = document.getElementById('search-input');
    const dataTable = document.getElementById('data-table');

    if (searchInput && dataTable) {
        const rows = Array.from(dataTable.querySelectorAll('tbody tr'));

        searchInput.addEventListener('input', () => {
            const keyword = searchInput.value.trim().toLowerCase();

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                const matches = keyword === '' || text.includes(keyword);
                row.classList.toggle('row-hidden', !matches);
            });
        });
    }

    // ---------- Confirmation avant suppression ----------
    document.querySelectorAll('.btn-delete').forEach(link => {
        link.addEventListener('click', (event) => {
            const message = link.getAttribute('data-confirm') || 'Confirmer la suppression ?';
            if (!window.confirm(message)) {
                event.preventDefault();
            }
        });
    });

    // ---------- Disparition automatique des messages flash ----------
    const flash = document.querySelector('.flash');
    if (flash) {
        setTimeout(() => {
            flash.style.transition = 'opacity 0.4s ease';
            flash.style.opacity = '0';
            setTimeout(() => flash.remove(), 400);
        }, 5000);
    }

    // ---------- Cohérence des dates sur le formulaire d'emprunt ----------
    const dateEmprunt = document.getElementById('date_emprunt');
    const dateRetourPrevue = document.getElementById('date_retour_prevue');

    if (dateEmprunt && dateRetourPrevue) {
        const syncMinDate = () => {
            if (dateEmprunt.value) {
                dateRetourPrevue.min = dateEmprunt.value;
                if (dateRetourPrevue.value && dateRetourPrevue.value < dateEmprunt.value) {
                    dateRetourPrevue.value = dateEmprunt.value;
                }
            }
        };
        dateEmprunt.addEventListener('change', syncMinDate);
        syncMinDate();
    }
});
