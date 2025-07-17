function addMasterlist(){
	$.ajax({
        url: "add_masterlist",
        method: "post",
        data: $('#formAddMasterlist').serialize(),
        dataType: "json",
        beforeSend: function(){
        },
        success: function(response){
            if(response['validationHasError'] == 1){
                toastr.error('Saving failed!');
                if(response['error']['employee_type'] === undefined){
                    $("#selectEmployeeType").removeClass('is-invalid');
                    $("#selectEmployeeType").attr('title', '');
                }
                else{
                    $("#selectEmployeeType").addClass('is-invalid');
                    $("#selectEmployeeType").attr('title', response['error']['employee_type']);
                }

                if(response['error']['employee_number'] === undefined){
                    $("#selectEmployeeName").removeClass('is-invalid');
                    $("#selectEmployeeName").attr('title', '');
                }
                else{
                    $("#selectEmployeeName").addClass('is-invalid');
                    $("#selectEmployeeName").attr('title', response['error']['employee_number']);
                }
                
                if(response['error']['masterlist_incoming'] === undefined){
                    $("#textMasterlistIncoming").removeClass('is-invalid');
                    $("#textMasterlistIncoming").attr('title', '');
                }
                else{
                    $("#textMasterlistIncoming").addClass('is-invalid');
                    $("#textMasterlistIncoming").attr('title', response['error']['masterlist_incoming']);
                }
                
                if(response['error']['masterlist_outgoing'] === undefined){
                    $("#textMasterlistOutgoing").removeClass('is-invalid');
                    $("#textMasterlistOutgoing").attr('title', '');
                }
                else{
                    $("#textMasterlistOutgoing").addClass('is-invalid');
                    $("#textMasterlistOutgoing").attr('title', response['error']['masterlist_outgoing']);
                }

                if(response['error']['routes_id'] === undefined){
                    $("#selectRoutes").removeClass('is-invalid');
                    $("#selectRoutes").attr('title', '');
                }
                else{
                    $("#selectRoutes").addClass('is-invalid');
                    $("#selectRoutes").attr('title', response['error']['routes_id']);
                }
            }else if(response['hasExisted'] == 1){
                toastr.warning('Employee already added!');
            }
            else if(response['hasError'] == 0){
                $("#formAddMasterlist")[0].reset();
                toastr.success('Succesfully saved!');
                $('#modalAddMasterlist').modal('hide');
                dataTablesMasterlist.draw();
            }

            $("#btnAddMasterlistIcon").removeClass('spinner-border spinner-border-sm');
            $("#btnAddMasterlist").removeClass('disabled');
            $("#btnAddMasterlistIcon").addClass('fa fa-check');
        },
        error: function(data, xhr, status){
            toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
        }
    });
}

// function getMasterlistById(id){
//     $.ajax({
//         url: "get_masterlist_by_id",
//         method: "get",
//         data: {
//             masterlistId : id,
//         },
//         dataType: "json",
//         beforeSend: function(){

//         },
//         success: function(response){
//             let masterlistData = response['masterlistData'];
//             if(masterlistData.length > 0){
//                 let selectedEmployeeType = masterlistData[0].masterlist_employee_type;
//                 if(selectedEmployeeType == 1){
//                     console.log('selectEmployeeType before trigger');
//                     // $("#selectEmployeeType").val(selectedEmployeeType).trigger('change');
//                     $("#selectEmployeeType").val(selectedEmployeeType);

//                     getEmployees($('#selectEmployeeName'), selectedEmployeeType).then((response) => {
//                         console.log('getMasterlistById response ', response);
//                         $("#selectEmployeeName").val(masterlistData[0].systemone_hris_id).trigger('change');
//                     }).catch((error) => {
//                         console.log('getMasterlistById error ', error);
//                     });

//                     // setTimeout(() => {
//                     //     $("#selectEmployeeName").val(masterlistData[0].systemone_hris_id).trigger('change');
//                     //     $("#selectRoutes").val(masterlistData[0].routes_id).trigger('change');
//                     // }, 500);

//                     $("input[name='systemone_id']", $('#formAddMasterlist')).val(masterlistData[0].systemone_hris_id);
//                     $("input[name='masterlist_id']", $('#formAddMasterlist')).val(masterlistData[0].id);
//                 }else{
//                     $("#selectEmployeeType").val(selectedEmployeeType).trigger('change');

//                     setTimeout(() => {
//                         $("#selectEmployeeName").val(masterlistData[0].systemone_subcon_id).trigger('change');
//                         $("#selectRoutes").val(masterlistData[0].routes_id).trigger('change');
//                     }, 500);

//                     $("input[name='systemone_id']", $('#formAddMasterlist')).val(masterlistData[0].systemone_subcon_id);
//                     $("input[name='masterlist_id']", $('#formAddMasterlist')).val(masterlistData[0].id);
//                 }
                
                
//                 $('select#selectEmployeeType').prop('disabled', true);
//                 $('select#selectEmployeeName').prop('disabled', true);
//                 $("#textEmployeeNumber").val(masterlistData[0].masterlist_employee_number);
//                 $("#textMasterlistIncoming").val(masterlistData[0].masterlist_incoming);
//                 $("#textMasterlistOutgoing").val(masterlistData[0].masterlist_outgoing);
                
//             }
//             else{
//                 toastr.warning('No records found!');
//             }
//         },
//         error: function(data, xhr, status){
//             $('select#selectEmployeeType').prop('disabled', false);
//             $('select#selectEmployeeName').prop('disabled', false);
//             toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
//         },
//     });
// }

function getMasterlistById(id){
    $.ajax({
        url: "get_masterlist_by_id",
        method: "get",
        data: {
            masterlistId : id,
        },
        dataType: "json",
        beforeSend: function(){
            $('select#selectEmployeeType').prop('disabled', true);
            $('select#selectEmployeeName').prop('disabled', true);
        },
        success: function(response){
            let masterlistData = response['masterlistData'];
            if(masterlistData.length > 0){
                let selectedEmployeeType = masterlistData[0].masterlist_employee_type;
                if(selectedEmployeeType == 1){
                    // $("#selectEmployeeType").val(selectedEmployeeType).trigger('change');
                    $("#selectEmployeeType").val(selectedEmployeeType);

                    getEmployees($('#selectEmployeeName'), selectedEmployeeType).then((response) => {
                        console.log('getEmployees response ', response);
                        // $('select#selectEmployeeType').prop('disabled', true);
                        // $('select#selectEmployeeName').prop('disabled', true);

                        $("#selectEmployeeName").val(masterlistData[0].systemone_hris_id).trigger('change');
                        $("#textEmployeeNumber").val(masterlistData[0].masterlist_employee_number);
                        $("#textMasterlistIncoming").val(masterlistData[0].masterlist_incoming);
                        $("#textMasterlistOutgoing").val(masterlistData[0].masterlist_outgoing);
                    }).catch((error) => {
                        console.log('getMasterlistById error ', error);
                    });

                    getRoutes($('#selectRoutes')).then((response) => {
                        console.log('getRoutes response ', response);
                        $('select#selectRoutes').prop('disabled', false);
                        $("#selectRoutes").val(masterlistData[0].routes_id).trigger('change');
                    }).catch((error) => {
                        console.log('error ', error);
                    });

                    $("input[name='systemone_id']", $('#formAddMasterlist')).val(masterlistData[0].systemone_hris_id);
                    $("input[name='masterlist_id']", $('#formAddMasterlist')).val(masterlistData[0].id);
                }else{
                    $("#selectEmployeeType").val(selectedEmployeeType);

                    getEmployees($('#selectEmployeeName'), selectedEmployeeType).then((response) => {
                        console.log('getEmployees response ', response);
                        // $('select#selectEmployeeType').prop('disabled', true);
                        // $('select#selectEmployeeName').prop('disabled', true);

                        $("#selectEmployeeName").val(masterlistData[0].systemone_subcon_id).trigger('change');
                        $("#textEmployeeNumber").val(masterlistData[0].masterlist_employee_number);
                        $("#textMasterlistIncoming").val(masterlistData[0].masterlist_incoming);
                        $("#textMasterlistOutgoing").val(masterlistData[0].masterlist_outgoing);
                    }).catch((error) => {
                        console.log('getMasterlistById error ', error);
                    });

                    getRoutes($('#selectRoutes')).then((response) => {
                        console.log('getRoutes response ', response);
                        $('select#selectRoutes').prop('disabled', false);
                        $("#selectRoutes").val(masterlistData[0].routes_id).trigger('change');
                    }).catch((error) => {
                        console.log('error ', error);
                    });

                    $("input[name='systemone_id']", $('#formAddMasterlist')).val(masterlistData[0].systemone_subcon_id);
                    $("input[name='masterlist_id']", $('#formAddMasterlist')).val(masterlistData[0].id);
                }
            }
            else{
                toastr.warning('No records found!');
            }
        },
        error: function(data, xhr, status){
            $('select#selectEmployeeType').prop('disabled', false);
            $('select#selectEmployeeName').prop('disabled', false);
            toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
        },
    });
}

function editMasterlistStatus(){
    $.ajax({
        url: "edit_masterlist_status",
        method: "post",
        data: $('#formEditMasterlistStatus').serialize(),
        dataType: "json",
        beforeSend: function(){
        },
        success: function(response){
            if(response['validationHasError'] == 1){
                toastr.error('Edit status failed!');
            }else{
                if(response['hasError'] == 0){
                    if(response['status'] == 0){
                        toastr.success('Deactivation success!');
                        dataTablesMasterlist.draw();
                    }
                    else{
                        toastr.success('Activation success!');
                        dataTablesMasterlist.draw();
                    }
                }
                $("#modalEditMasterlistStatus").modal('hide');
                $("#formEditMasterlistStatus")[0].reset();
            }
        },
        error: function(data, xhr, status){
            toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
            $("#iBtnChangeMasterlistStatIcon").removeClass('fa fa-spinner fa-pulse');
            $("#btnChangeMasterlistStat").removeAttr('disabled');
            $("#iBtnChangeMasterlistStatIcon").addClass('fa fa-check');
        }
    });
}

function deleteMasterlist(){
    $.ajax({
        url: "delete_masterlist",
        method: "post",
        data: $('#formDeleteMasterlist').serialize(),
        dataType: "json",
        beforeSend: function(){
        },
        success: function(response){
            if(response['validationHasError'] == 1){
                toastr.error('Deleting masterlist failed!');
            }else{
                if(response['hasError'] == 0){
                    if(response['status'] == 1){
                        toastr.success('Successfully deleted!');
                        dataTablesMasterlist.draw();
                    }
                }
                $("#modalDeleteMasterlistStatus").modal('hide');
                $("#formDeleteMasterlist")[0].reset();
            }
        },
        error: function(data, xhr, status){
            toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
        }
    });
}