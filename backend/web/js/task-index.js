(function () {

    // PJAX-safe event binding
    $(document).on('change', '.task-status', function () {

        let status = $(this).val();
        let id     = $(this).data('id');

        $.post(window.TASK_STATUS_URL, {
            id: id,
            status: status,
            _csrf: yii.getCsrfToken()
        })
        .done(function (response) {
            alert(response.message);
        })
        .fail(function () {
            alert('Server error occurred ❌');
        });

    });

})();
