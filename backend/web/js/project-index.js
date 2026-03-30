(function () {

    // PJAX-safe event
    $(document).on('change', '.project-status', function () {

        let status = $(this).val();
        let id     = $(this).data('id');

        $.post(window.PROJECT_STATUS_URL, {
            id: id,
            status: status,
            _csrf: yii.getCsrfToken()
        })
        .done(function (response) {
            alert(response.message);
        })
        .fail(function () {
            alert('Server error occurred');
        });

    });

})();
