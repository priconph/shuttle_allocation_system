function addPickupTime(){
	$.ajax({
        url: "add_pickup_time",
        method: "post",
        data: $('#formAddPickupTime').serialize(),
        dataType: "json",
        beforeSend: function(){
        },
        success: function(response){
            if(response['validationHasError'] == 1){
                toastr.error('Saving failed!');
                if(response['error']['pickup_time'] === undefined){
                    $("#textPickupTime").removeClass('is-invalid');
                    $("#textPickupTime").attr('title', '');
                }
                else{
                    $("#textPickupTime").addClass('is-invalid');
                    $("#textPickupTime").attr('title', response['error']['pickup_time']);
                }
            }else if(response['hasError'] == 0){
                $("#formAddPickupTime")[0].reset();
                toastr.success('Succesfully saved!');
                $('#modalAddPickupTime').modal('hide');
                dataTablesPickupTime.draw();
            }

            $("#btnAddPickupTimeIcon").removeClass('spinner-border spinner-border-sm');
            $("#btnAddPickupTime").removeClass('disabled');
            $("#btnAddPickupTimeIcon").addClass('fa fa-check');
        },
        error: function(data, xhr, status){
            toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
        }
    });
}



function getPickupTimeById(id){
    $.ajax({
        url: "get_pickup_time_by_id",
        method: "get",
        data: {
            pickupTimeId : id,
        },
        dataType: "json",
        beforeSend: function(){

        },
        success: function(response){
            let pickupTimeData = response['pickupTimeData'];
            if(pickupTimeData.length > 0){
                console.table(pickupTimeData);
                $("#textPickupTime").val(pickupTimeData[0].pickup_time);
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

function editPickupTimeStatus(){
    $.ajax({
        url: "edit_pickup_time_status",
        method: "post",
        data: $('#formEditPickupTimeStatus').serialize(),
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
                        dataTablesPickupTime.draw();
                    }
                    else{
                        toastr.success('Activation success!');
                        dataTablesPickupTime.draw();
                    }
                }
                $("#modalEditPickupTimeStatus").modal('hide');
                $("#formEditPickupTimeStatus")[0].reset();
            }
        },
        error: function(data, xhr, status){
            toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
            $("#iBtnChangePickupTimeStatIcon").removeClass('fa fa-spinner fa-pulse');
            $("#btnChangePickupTimeStat").removeAttr('disabled');
            $("#iBtnChangePickupTimeStatIcon").addClass('fa fa-check');
        }
    });
}

function getPickupTime(cboElement){
	let result = '<option value="0" disabled selected>Select One</option>';
	$.ajax({
		url: 'get_pickup_time',
		method: 'get',
		dataType: 'json',
		beforeSend: function(){
			result = '<option value="0" disabled>Loading</option>';
			cboElement.html(result);
		},
		success: function(response){
			if(response['pickupTimeData'].length > 0){
				result = '<option value="0" disabled selected>Select One</option>';
				for(let index = 0; index < response['pickupTimeData'].length; index++){

                    result += '<option value="' + response['pickupTimeData'][index].id + '">' + response['parsedPickupTimeColumn'][index] + '</option>';
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