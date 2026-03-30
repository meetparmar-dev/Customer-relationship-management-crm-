// backend/web/js/searchable-dropdown.js

function initSearchDropdown(inputId, dropdownId, hiddenId) {
    const input    = document.getElementById(inputId);
    const dropdown = document.getElementById(dropdownId);
    const hidden   = document.getElementById(hiddenId);

    if (!input || !dropdown || !hidden) return;

    const items = dropdown.querySelectorAll('.search-item');

    function show() {
        dropdown.classList.add('show');
    }

    function hide() {
        dropdown.classList.remove('show');
    }

    input.addEventListener('focus', show);
    input.addEventListener('click', show);

    input.addEventListener('input', () => {
        const val = input.value.toLowerCase().trim();
        hidden.value = '';

        items.forEach(item => {
            const name = item.dataset.name.toLowerCase();
            item.style.display = name.includes(val) ? 'block' : 'none';
        });

        show();
    });

    items.forEach(item => {
        item.addEventListener('click', () => {
            input.value  = item.dataset.name;
            hidden.value = item.dataset.id;
            hide();
        });
    });

    document.addEventListener('click', e => {
        if (!input.contains(e.target) && !dropdown.contains(e.target)) {
            hide();
        }
    });
}
