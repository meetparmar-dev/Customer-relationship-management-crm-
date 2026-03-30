function initSearch(inputId, listId, itemClass, hiddenId) {
    const input  = document.getElementById(inputId);
    const list   = document.getElementById(listId);
    const hidden = document.getElementById(hiddenId);

    if (!input || !list || !hidden) {
        return;
    }

    input.addEventListener('keyup', function () {
        const value = this.value.toLowerCase();

        if (!value) {
            list.style.display = 'none';
            hidden.value = '';
            return;
        }

        list.style.display = 'block';

        document.querySelectorAll('.' + itemClass).forEach(item => {
            item.style.display = item.textContent.toLowerCase().includes(value)
                ? 'block'
                : 'none';
        });
    });

    document.querySelectorAll('.' + itemClass).forEach(item => {
        item.addEventListener('click', function () {
            input.value  = this.textContent.trim();
            hidden.value = this.dataset.id;
            list.style.display = 'none';
        });
    });

    document.addEventListener('click', function (e) {
        if (!e.target.closest('#' + inputId) && !e.target.closest('#' + listId)) {
            list.style.display = 'none';
        }
    });
}

/* INIT AFTER DOM LOAD */
document.addEventListener('DOMContentLoaded', function () {
    initSearch('projectSearch', 'projectList', 'project-item', 'project_id');
    initSearch('userSearch', 'userList', 'user-item', 'assigned_to');
});
