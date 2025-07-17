function addShuttleProvider(){
	$.ajax({
        url: "add_shuttle_provider",
        method: "post",
        data: $('#formAddShuttleProvider').serialize(),
        dataType: "json",
        beforeSend: function(){
        },
        success: function(response){
            if(response['validationHasError'] == 1){
                toastr.error('Saving failed!');
                if(response['error']['shuttle_provider_name'] === undefined){
                    $("#textShuttleProviderName").removeClass('is-invalid');
                    $("#textShuttleProviderName").attr('title', '');
                }
                else{
                    $("#textShuttleProviderName").addClass('is-invalid');
                    $("#textShuttleProviderName").attr('title', response['error']['shuttle_provider_name']);
                }

                if(response['error']['shuttle_provider_capacity'] === undefined){
                    $("#textShuttleProviderCapacity").removeClass('is-invalid');
                    $("#textShuttleProviderCapacity").attr('title', '');
                }
                else{
                    $("#textShuttleProviderCapacity").addClass('is-invalid');
                    $("#textShuttleProviderCapacity").attr('title', response['error']['shuttle_provider_capacity']);
                }
            }else if(response['hasError'] == 0){
                $("#formAddShuttleProvider")[0].reset();
                toastr.success('Succesfully saved!');
                $('#modalAddShuttleProvider').modal('hide');
                dataTablesShuttleProvider.draw();
            }

            $("#btnAddShuttleProviderIcon").removeClass('spinner-border spinner-border-sm');
            $("#btnAddShuttleProvider").removeClass('disabled');
            $("#btnAddShuttleProviderIcon").addClass('fa fa-check');
        },
        error: function(data, xhr, status){
            toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
        }
    });
}



function getShuttleProviderById(id){
    $.ajax({
        url: "get_shuttle_provider_by_id",
        method: "get",
        data: {
            shuttleProviderId : id,
        },
        dataType: "json",
        beforeSend: function(){

        },
        success: function(response){
            let shuttleProviderData = response['shuttleProviderData'];
            if(shuttleProviderData.length > 0){
                console.table(shuttleProviderData);
                $("#textShuttleProviderName").val(shuttleProviderData[0].shuttle_provider_name);
                $("#textShuttleProviderCapacity").val(shuttleProviderData[0].shuttle_provider_capacity);
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

function editShuttleProviderStatus(){
    $.ajax({
        url: "edit_shuttle_provider_status",
        method: "post",
        data: $('#formEditShuttleProviderStatus').serialize(),
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
                        dataTablesShuttleProvider.draw();
                    }
                    else{
                        toastr.success('Activation success!');
                        dataTablesShuttleProvider.draw();
                    }
                }
                $("#modalEditShuttleProviderStatus").modal('hide');
                $("#formEditShuttleProviderStatus")[0].reset();
            }
        },
        error: function(data, xhr, status){
            toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
            $("#iBtnChangeShuttleProviderStatIcon").removeClass('fa fa-spinner fa-pulse');
            $("#btnChangeShuttleProviderStat").removeAttr('disabled');
            $("#iBtnChangeShuttleProviderStatIcon").addClass('fa fa-check');
        }
    });
}

function getShuttleProvider(cboElement){
	let result = '<option value="0" disabled selected>Select One</option>';
	$.ajax({
		url: 'get_shuttle_provider',
		method: 'get',
		dataType: 'json',
		beforeSend: function(){
			result = '<option value="0" disabled>Loading</option>';
			cboElement.html(result);
		},
		success: function(response){
			if(response['shuttleProviderData'].length > 0){
				result = '<option value="0" disabled selected>Select One</option>';
				for(let index = 0; index < response['shuttleProviderData'].length; index++){
                    result += '<option value="' + response['shuttleProviderData'][index].id + '">' + response['shuttleProviderData'][index].shuttle_provider_name + '</option>';
				}
			}
			else{
				result = '<option value="0" disabled>No record found</option>';
			}
			cboElement.html(result);
		},
		error: function(data, xhr, status){
			result = '<option value="0" disabled>Reload Again</option>';
			cboElement.html(result);
            console.log('Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
        }
	});
}