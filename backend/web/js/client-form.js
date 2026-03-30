(function () {

    function toggleCompany() {
        let type = $('#client-type').val();

        if (type === 'company') {
            $('.field-client-company_name').slideDown();
        } else {
            $('.field-client-company_name').slideUp();
            $('#client-company_name').val('');
        }
    }

    // DOM ready
    $(document).on('change', '#client-type', toggleCompany);

    // page load pe run
    $(document).ready(function () {
        toggleCompany();
    });

})();
