function capitalizeFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}

function resetFormValues() {
    // Reset input values
    $("#formAddUser")[0].reset();

    $('#formAddUser').find("select").val(0).trigger('change');

    // Remove invalid & title validation
    $('div').find('input').removeClass('is-invalid');
    $("div").find('input').attr('title', '');
    $('div').find('select').removeClass('is-invalid');
    $("div").find('select').attr('title', '');
    $('div').find('textarea').removeClass('is-invalid');
    $("div").find('textarea').attr('title', '');
}

$("#modalAddUser").on('hidden.bs.modal', function () {
    console.log('modalAddUser modal is closed');
    resetFormValues();
});

function resetRoutesFormValues() {
    // Reset input values
    $("#formAddRoutes")[0].reset();

    $('#formAddRoutes').find("select").val(0).trigger('change');

    // Remove invalid & title validation
    $('div').find('input').removeClass('is-invalid');
    $("div").find('input').attr('title', '');
    $('div').find('select').removeClass('is-invalid');
    $("div").find('select').attr('title', '');
    $('div').find('textarea').removeClass('is-invalid');
    $("div").find('textarea').attr('title', '');
}

$("#modalAddRoutes").on('hidden.bs.modal', function () {
    console.log('modalAddUser modal is closed');
    resetRoutesFormValues();
});

function resetShuttleProviderFormValues() {
    // Reset input values
    $("#formAddShuttleProvider")[0].reset();

    $('#formAddShuttleProvider').find("select").val(0).trigger('change');

    // Remove invalid & title validation
    $('div').find('input').removeClass('is-invalid');
    $("div").find('input').attr('title', '');
    $('div').find('select').removeClass('is-invalid');
    $("div").find('select').attr('title', '');
    $('div').find('textarea').removeClass('is-invalid');
    $("div").find('textarea').attr('title', '');
}

$("#modalAddShuttleProvider").on('hidden.bs.modal', function () {
    console.log('modalAddShuttleProvider modal is closed');
    resetShuttleProviderFormValues();
});

function resetPickupTimeFormValues() {
    // Reset input values
    $("#formAddPickupTime")[0].reset();

    $('#formAddPickupTime').find("select").val(0).trigger('change');

    // Remove invalid & title validation
    $('div').find('input').removeClass('is-invalid');
    $("div").find('input').attr('title', '');
    $('div').find('select').removeClass('is-invalid');
    $("div").find('select').attr('title', '');
    $('div').find('textarea').removeClass('is-invalid');
    $("div").find('textarea').attr('title', '');
}

$("#modalAddPickupTime").on('hidden.bs.modal', function () {
    console.log('modalAddPickupTime modal is closed');
    resetPickupTimeFormValues();
});

function resetMasterlistFormValues() {
    // Reset input values
    $("#formAddMasterlist")[0].reset();

    $('#formAddMasterlist').find("select").val(0).trigger('change');
    $('select#selectEmployeeType').prop('disabled', false);
    $('select#selectEmployeeName').prop('disabled', true);
    $('select#selectRoutes').prop('disabled', true);
    // $('select#selectEmployeeName').val(0).trigger('change');

    // Remove invalid & title validation
    $('div').find('input').removeClass('is-invalid');
    $("div").find('input').attr('title', '');
    $('div').find('select').removeClass('is-invalid');
    $("div").find('select').attr('title', '');
    $('div').find('textarea').removeClass('is-invalid');
    $("div").find('textarea').attr('title', '');
}

$("#modalAddMasterlist").on('hidden.bs.modal', function () {
    console.log('modalAddMasterlist modal is closed');
    resetMasterlistFormValues();
});