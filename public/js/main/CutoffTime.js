function addCutoffTime(){
	$.ajax({
        url: "add_cutoff_time",
        method: "post",
        data: $('#formAddCutoffTime').serialize(),
        dataType: "json",
        beforeSend: function(){
        },
        success: function(response){
            if(response['validationHasError'] == 1){
                toastr.error('Saving failed!');
                if(response['error']['cutoff_time'] === undefined){
                    $("#textCutoffTime").removeClass('is-invalid');
                    $("#textCutoffTime").attr('title', '');
                }
                else{
                    $("#textCutoffTime").addClass('is-invalid');
                    $("#textCutoffTime").attr('title', response['error']['cutoff_time']);
                }
            }else if(response['hasError'] == 0){
                $("#formAddCutoffTime")[0].reset();
                toastr.success('Succesfully saved!');
                $('#modalAddCutoffTime').modal('hide');
                dataTablesCutoffTime.draw();
            }

            $("#btnAddCutoffTimeIcon").removeClass('spinner-border spinner-border-sm');
            $("#btnAddCutoffTime").removeClass('disabled');
            $("#btnAddCutoffTimeIcon").addClass('fa fa-check');
        },
        error: function(data, xhr, status){
            toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
        }
    });
}



function getCutoffTimeById(id){
    $.ajax({
        url: "get_cutoff_time_by_id",
        method: "get",
        data: {
            cutoffTimeId : id,
        },
        dataType: "json",
        beforeSend: function(){

        },
        success: function(response){
            let cutoffTimeData = response['cutoffTimeData'];
            if(cutoffTimeData.length > 0){
                console.table(cutoffTimeData);
                $("#textCutoffTime").val(cutoffTimeData[0].cutoff_time);
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

function editCutoffTimeStatus(){
    $.ajax({
        url: "edit_cutoff_time_status",
        method: "post",
        data: $('#formEditCutoffTimeStatus').serialize(),
        dataType: "json",
        beforeSend: function(){
        },
        success: function(response){
            if(response['validationHasError'] == 1){
                toastr.error('Edit status failed!');
            }else{
                if(response['hasError'] == 0){
                    if(response['status'] == 0){
                        toastr.success('Masterlist successfully locked!');
                        dataTablesCutoffTime.draw();
                    }
                    else{
                        toastr.success('Masterlist successfully unlocked!');
                        dataTablesCutoffTime.draw();
                    }
                }
                $("#modalEditCutoffTimeStatus").modal('hide');
                $("#formEditCutoffTimeStatus")[0].reset();
            }
        },
        error: function(data, xhr, status){
            toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
            $("#iBtnChangeCutoffTimeStatIcon").removeClass('fa fa-spinner fa-pulse');
            $("#btnChangeCutoffTimeStat").removeAttr('disabled');
            $("#iBtnChangeCutoffTimeStatIcon").addClass('fa fa-check');
        }
    });
}

function getCutoffTime(cboElement){
	let result = '<option value="0" disabled selected>Select One</option>';
	$.ajax({
		url: 'get_cutoff_time',
		method: 'get',
		dataType: 'json',
		beforeSend: function(){
			result = '<option value="0" disabled>Loading</option>';
			cboElement.html(result);
		},
		success: function(response){
			if(response['cutoffTimeData'].length > 0){
				result = '<option value="0" disabled selected>Select One</option>';
				for(let index = 0; index < response['cutoffTimeData'].length; index++){

                    result += '<option value="' + response['cutoffTimeData'][index].id + '">' + response['parsedCutoffTimeColumn'][index] + '</option>';
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