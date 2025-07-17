function addRoutes(){
	$.ajax({
        url: "add_routes",
        method: "post",
        data: $('#formAddRoutes').serialize(),
        dataType: "json",
        beforeSend: function(){
        },
        success: function(response){
            if(response['validationHasError'] == 1){
                toastr.error('Saving failed!');
                if(response['error']['routes_name'] === undefined){
                    $("#textRoutesName").removeClass('is-invalid');
                    $("#textRoutesName").attr('title', '');
                }
                else{
                    $("#textRoutesName").addClass('is-invalid');
                    $("#textRoutesName").attr('title', response['error']['routes_name']);
                }

                if(response['error']['routes_description'] === undefined){
                    $("#textRoutesDescription").removeClass('is-invalid');
                    $("#textRoutesDescription").attr('title', '');
                }
                else{
                    $("#textRoutesDescription").addClass('is-invalid');
                    $("#textRoutesDescription").attr('title', response['error']['routes_description']);
                }
            
                if(response['error']['pickup_time_id'] === undefined){
                    $("#selectPickupTime").removeClass('is-invalid');
                    $("#selectPickupTime").attr('title', '');
                }
                else{
                    $("#selectPickupTime").addClass('is-invalid');
                    $("#selectPickupTime").attr('title', response['error']['pickup_time_id']);
                }

                if(response['error']['shuttle_provider_id'] === undefined){
                    $("#selectShuttleProvider").removeClass('is-invalid');
                    $("#selectShuttleProvider").attr('title', '');
                }
                else{
                    $("#selectShuttleProvider").addClass('is-invalid');
                    $("#selectShuttleProvider").attr('title', response['error']['shuttle_provider_id']);
                }
            }else if(response['hasError'] == 0){
                $("#formAddRoutes")[0].reset();
                toastr.success('Succesfully saved!');
                $('#modalAddRoutes').modal('hide');
                dataTablesRoutes.draw();
            }

            $("#btnAddRoutesIcon").removeClass('spinner-border spinner-border-sm');
            $("#btnAddRoutes").removeClass('disabled');
            $("#btnAddRoutesIcon").addClass('fa fa-check');
        },
        error: function(data, xhr, status){
            toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
        }
    });
}



function getRoutesById(id){
    $.ajax({
        url: "get_routes_by_id",
        method: "get",
        data: {
            routesId : id,
        },
        dataType: "json",
        beforeSend: function(){

        },
        success: function(response){
            let routesData = response['routesData'];
            if(routesData.length > 0){
                console.table(routesData);
                $("#textRoutesName").val(routesData[0].routes_name);
                $("#textRoutesDescription").val(routesData[0].routes_description);
                $("#selectPickupTime").val(routesData[0].pickup_time_id).trigger('change');
                $("#selectShuttleProvider").val(routesData[0].shuttle_provider_id).trigger('change');
            }
            else{
                toastr.warning('No records found!');
            }
        },
        error: function(data, xhr, status){
            toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
        },
    });
}

function editRoutesStatus(){
    $.ajax({
        url: "edit_routes_status",
        method: "post",
        data: $('#formEditRoutesStatus').serialize(),
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
                        dataTablesRoutes.draw();
                    }
                    else{
                        toastr.success('Activation success!');
                        dataTablesRoutes.draw();
                    }
                }
                $("#modalEditRoutesStatus").modal('hide');
                $("#formEditRoutesStatus")[0].reset();
            }
        },
        error: function(data, xhr, status){
            toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
            $("#iBtnChangeRoutesStatIcon").removeClass('fa fa-spinner fa-pulse');
            $("#btnChangeRoutesStat").removeAttr('disabled');
            $("#iBtnChangeRoutesStatIcon").addClass('fa fa-check');
        }
    });
}

function getRoutes(cboElement, routesId){
    return new Promise((resolve, reject) => {
        let result = '<option value="0" disabled selected>Select One</option>';
        $.ajax({
            url: 'get_routes',
            method: 'get',
            dataType: 'json',
            beforeSend: function(){
                result = '<option value="0" disabled>Loading</option>';
                cboElement.html(result);
            },
            success: function(response){
                resolve(response)
                let disabled = '';
                if(response['routesData'].length > 0){
                    result = '<option value="0" disabled selected>Select One</option>';
                    for(let index = 0; index < response['routesData'].length; index++){
                        result += '<option value="' + response['routesData'][index].id + '">' + response['routesData'][index].routes_name + '</option>';
                    }
                    console.log('routesId ', routesId);
                    $("#selectRoutes").val(routesId).trigger('change');
                }
                else{
                    result = '<option value="0" disabled>No Routes Role found</option>';
                }
                cboElement.html(result);
            },
            error: function(data, xhr, status){
                reject(data)
                result = '<option value="0" disabled>Reload Again</option>';
                cboElement.html(result);
                console.log('Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
            }
        });
    });
	
}
