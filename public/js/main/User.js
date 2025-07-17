function addUser(){
	$.ajax({
        url: "add_user",
        method: "post",
        data: $('#formAddUser').serialize(),
        dataType: "json",
        beforeSend: function(){
            // $("#btnAddUserIcon").addClass('spinner-border spinner-border-sm');
            // $("#btnAddUser").addClass('disabled');
            // $("#btnAddUserIcon").removeClass('fa fa-check');
        },
        success: function(response){
            if(response['validationHasError'] == 1){
                toastr.error('Saving user failed!');
                if(response['error']['username'] === undefined){
                    $("#textUsername").removeClass('is-invalid');
                    $("#textUsername").attr('title', '');
                }
                else{
                    $("#textUsername").addClass('is-invalid');
                    $("#textUsername").attr('title', response['error']['username']);
                }
                if(response['error']['email'] === undefined){
                    $("#textEmail").removeClass('is-invalid');
                    $("#textEmail").attr('title', '');
                }
                else{
                    $("#textEmail").addClass('is-invalid');
                    $("#textEmail").attr('title', response['error']['email']);
                }
                if(response['error']['department'] === undefined){
                    $("#textDepartment").removeClass('is-invalid');
                    $("#textDepartment").attr('title', '');
                }
                else{
                    $("#textDepartment").addClass('is-invalid');
                    $("#textDepartment").attr('title', response['error']['department']);
                }
                if(response['error']['user_role_id'] === undefined){
                    $("#selectUserRoles").removeClass('is-invalid');
                    $("#selectUserRoles").attr('title', '');
                }
                else{
                    $("#selectUserRoles").addClass('is-invalid');
                    $("#selectUserRoles").attr('title', response['error']['user_role_id']);
                }
            }else if(response['hasError'] == 0){
                $("#formAddUser")[0].reset();
                toastr.success('Succesfully saved!');
                $('#modalAddUser').modal('hide');
                dataTablesUsers.draw();
            }

            $("#btnAddUserIcon").removeClass('spinner-border spinner-border-sm');
            $("#btnAddUser").removeClass('disabled');
            $("#btnAddUserIcon").addClass('fa fa-check');
        },
        error: function(data, xhr, status){
            toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
        }
    });
}

function signIn(){
    $.ajax({
        url: 'sign_in',
        method: 'get',
        dataType: 'json',
        data: $("#formSignIn").serialize(),
        beforeSend: function(){
            $("#btnSignInIcon").addClass('spinner-border spinner-border-sm');
            $("#btnSignIn").addClass('disabled');
            $("#btnSignInIcon").removeClass('fa fa-check');
        },
        success: function(response){
            if(response['validationHasError'] == 1){
                if(response['error']['username'] === undefined){
                    $("#textUsername").removeClass('is-invalid');
                    $("#textUsername").attr('title', '');
                }
                else{
                    $("#textUsername").addClass('is-invalid');
                    $("#textUsername").attr('title', response['error']['username']);
                }

                if(response['error']['password'] === undefined){
                    $("#textPassword").removeClass('is-invalid');
                    $("#textPassword").attr('title', '');
                }
                else{
                    $("#textPassword").addClass('is-invalid');
                    $("#textPassword").attr('title', response['error']['password']);
                }
            }
            else {
                if(response['hasError'] == 1){
                    toastr.error(response['error_message']);
                }
                else if(response['isDeleted'] == 1){
                    toastr.error(response['error_message']);
                }
                else if(response['isAuthenticated'] == 0){
                    toastr.error(response['error_message']);
                }
                else if(response['inactive'] == 0){
                    toastr.error(response['error_message']);
                }
                // else if(response['isPasswordChanged'] == 0){
                //     window.location = "change_password_page";
                // }
                else{
                    toastr.success('Success!');
                    setTimeout(() => {
                        window.location = "dashboard";
                    }, 600);
                }
            }
            $("#btnSignInIcon").removeClass('spinner-border spinner-border-sm');
            $("#btnSignIn").removeClass('disabled');
            $("#btnSignInIcon").addClass('fa fa-check');
        }
    });
}

function getUserRoles(cboElement){
	let result = '<option value="0" disabled selected>Select One</option>';
	$.ajax({
		url: 'get_user_roles',
		method: 'get',
		dataType: 'json',
		beforeSend: function(){
			result = '<option value="0" disabled>Loading</option>';
			cboElement.html(result);
		},
		success: function(response){
			let disabled = '';
			if(response['userRoles'].length > 0){
				result = '<option value="0" disabled selected>Select One</option>';
				for(let index = 0; index < response['userRoles'].length; index++){
                    result += '<option value="' + response['userRoles'][index].id + '">' + response['userRoles'][index].name + '</option>';
				}
			}
			else{
				result = '<option value="0" disabled>No User Role found</option>';
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

function getRapidxUsers(cboElement){
	let result = '<option value="0" disabled selected>Select One</option>';
	$.ajax({
		url: 'get_rapidx_users',
		method: 'get',
		dataType: 'json',
		beforeSend: function(){
			result = '<option value="0" disabled>Loading</option>';
			cboElement.html(result);
		},
		success: function(response){
			let disabled = '';
			if(response['rapidxUsers'].length > 0){
				result = '<option value="0" disabled selected>Select One</option>';
				for(let index = 0; index < response['rapidxUsers'].length; index++){
                    result += `<option name="${response['rapidxUsers'][index].name}" username="${response['rapidxUsers'][index].username}" email="${response['rapidxUsers'][index].email}" department="${response['rapidxUsers'][index]['department'].department_name}" department-group="${response['rapidxUsers'][index]['department'].department_group}" value="${response['rapidxUsers'][index].id}">${response['rapidxUsers'][index].name}</option>`;
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

function getUserById(userId){
    $.ajax({
        url: "get_user_by_id",
        method: "get",
        data: {
            userId : userId,
        },
        dataType: "json",
        beforeSend: function(){

        },
        success: function(response){
            let formAddUser = $('#formAddUser');
            let userDetails = response['userDetails'];
            if(userDetails.length > 0){
                // console.table(userDetails);
                $('select[name="rapidx_user"]', formAddUser).val(userDetails[0].rapidx_user_id).trigger('change');
                $("#textUsername").val(userDetails[0].username);
                $("#textEmail").val(userDetails[0].email);
                $("#textDepartment").val(userDetails[0].department);
                $('select[name="user_roles"]', formAddUser).val(userDetails[0].user_role_id).trigger('change');
            }
            else{
                toastr.warning('No Customer Classification records found!');
            }
        },
        error: function(data, xhr, status){
            toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
        },
    });
}

// function editUserStatus(){
//     $.ajax({
//         url: "edit_user_status",
//         method: "post",
//         data: $('#formEditUserStatus').serialize(),
//         dataType: "json",
//         beforeSend: function(){
//             $("#iBtnAddUserIcon").addClass('fa fa-spinner fa-pulse');
//             $("#buttonEditUserStatus").prop('disabled', 'disabled');
//         },
//         success: function(response){

//             if(response['validationHasError'] == 1){
//                 toastr.error('Edit user status failed!');
//             }else{
//                 if(response['hasError'] == 0){
//                     if(response['status'] == 0){
//                         toastr.success('User deactivation success!');
//                         dataTablesUsers.draw();
//                         dataTablesPendingUsers.draw();
//                     }
//                     else{
//                         toastr.success('User activation success!');
//                         dataTablesUsers.draw();
//                         dataTablesPendingUsers.draw();
//                     }
//                 }
//                 $("#modalEditUserStatus").modal('hide');
//                 $("#formEditUserStatus")[0].reset();
//             }

//             $("#iBtnAddUserIcon").removeClass('fa fa-spinner fa-pulse');
//             $("#buttonEditUserStatus").removeAttr('disabled');
//             $("#iBtnAddUserIcon").addClass('fa fa-check');
//         },
//         error: function(data, xhr, status){
//             toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
//             $("#iBtnChangeUserStatIcon").removeClass('fa fa-spinner fa-pulse');
//             $("#btnChangeUserStat").removeAttr('disabled');
//             $("#iBtnChangeUserStatIcon").addClass('fa fa-check');
//         }
//     });
// }