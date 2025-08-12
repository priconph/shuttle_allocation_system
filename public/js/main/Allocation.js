$(document).ready(function(){
    let txtGlobalUserId = $('#txtGlobalUserId').val();
    // console.log('txtGlobalUserId', $('#txtGlobalUserId').val());
    GetDetailsForFiltering();

    $('.select2bs5').select2({
        width: '100%',
        theme: 'bootstrap-5'
    });

    dtAllocation = $("#tblAllocation").DataTable({
        "processing" : false,
        "serverSide" : true,
        "responsive": true,
        "order": [[ 0, "desc" ]],
        "language": {
            "info": "Showing _START_ to _END_ of _TOTAL_ employee records",
            "lengthMenu": "Show _MENU_ employee records",
        },
        "pagingType": "full_numbers",
        lengthMenu: [10, 20, 50, 100, 200, 500, 1000, 1500, 2000],
        "ajax" : {
            url: "view_allocations",
            data: function (param){
                param.rapidXUserId = txtGlobalUserId;
            },
        },
        "columns":[
            { "data" : "action", orderable:false, searchable:false},
            { "data" : "request_status"},
            { "data" : "allocation_date"},
            { "data" : "requeste_ml_info.masterlist_employee_number"},
            { "data" : "masterlist_name"},
            { "data" : "requeste_ml_info.routes_info.routes_name", orderable:false, searchable:false},
            // { "data" : "requeste_ml_info.masterlist_factory"},
            {
                "data": "requeste_ml_info.masterlist_factory",
                render: function(data) {
                    if(data == null) {
                        return 'No Record'
                    }else{
                        return data;
                    }
                }
            },
            { "data" : "requeste_ml_info.masterlist_incoming", orderable:false, searchable:false},
            { "data" : "requeste_ml_info.masterlist_outgoing", orderable:false, searchable:false},
            { "data" : "alloc_factory", orderable:false, searchable:false},
            { "data" : "alloc_incoming", orderable:false, searchable:false},
            { "data" : "alloc_outgoing", orderable:false, searchable:false},
            // { "data" : "routes_info.routes_destination"},
            { "data" : "date_requested"},
            { "data" : "requestor_user_info.name"}
        ],
        "createdRow": function(row, data, index) {
            $('td', row).eq(5).css('white-space', 'normal');
            // console.log('row ', row);
            // console.log('data ', data);
            // console.log('index ', index);
        },
    });

    dtMasterListToAlloc = $("#tblMasterListToAlloc").DataTable({
        "processing" : false,
        "serverSide" : true,
        "responsive": true,
        "order": [[ 0, "desc" ]],
        "language": {
            "info": "Showing _START_ to _END_ of _TOTAL_ employee records",
            "lengthMenu": "Show _MENU_ employee records",
        },
        "pagingType": "full_numbers",
        lengthMenu: [10, 20, 50, 100, 200, 500, 1000, 1500, 2000],
        "ajax" : {
            url: "view_master_list_for_allocation",
            data: function (param){
                param.rapidXUserId  = txtGlobalUserId;
                param.factory       = $(".selectAllocFactory").val();
                param.department    = $(".selectAllocDepartment").val();
                param.section       = $(".selectAllocSection").val();
            },
            beforeSend: function (jqXHR, settings) {
                // $("#divForTblMasterListToAllocThead").addClass('d-none');
                // $("#divForTblMasterListToAllocTbody").addClass('d-none');
            },
            complete: function () {
                // $("#divForTblMasterListToAllocThead").removeClass('d-none');
                // $("#divForTblMasterListToAllocTbody").removeClass('d-none');
            }
        },
        "columns":[
            { "data" : "action", width: '10%', orderable:false, searchable:false},
            { "data" : "masterlist_employee_number"},
            { "data" : "name"},
            { "data" : "department"},
            { "data" : "section"},
            { "data" : "routes_info.routes_name", orderable:false, searchable:false},
            { "data" : "factory"},
            { "data" : "masterlist_incoming", orderable:false, searchable:false},
            { "data" : "masterlist_outgoing", orderable:false, searchable:false},
            // { "data" : "ml_route", orderable:false, searchable:false},
        ],
        "createdRow": function(row, data, index) {
            $('td', row).eq(5).css('white-space', 'normal');
            // console.log('row ', row);
            // console.log('data ', data);
            // console.log('index ', index);
        },
    });

    $('#btnAddAllocation').click(function (e) {
        e.preventDefault();

        $('.selectAllocFactory').val('').trigger('change');
        $('.selectAllocDepartment').val('').trigger('change');
        $('.selectAllocSection').val('').trigger('change');

        $.ajax({
            url: "get_user_info",
            method: "get",
            data: {
                userId : txtGlobalUserId,
            },
            dataType: "json",
            beforeSend: function(){
            },
            success: function(response){
                let formAddAllocation = $('#formAddAllocation');
                let userDetails = response['userDetails'];
                if(userDetails != null){
                    $('#txtEmployeeNumber', formAddAllocation).val(userDetails.rapidx_user_info.employee_number);
                    $('#txtRequestorId', formAddAllocation).val(userDetails.rapidx_user_id);
                    $('#txtRequestor', formAddAllocation).val(userDetails.name);
                    $('#txtDepartmentSection', formAddAllocation).val(userDetails.department);
                }else{
                    toastr.warning('No record found!');
                }
            },
            error: function(data, xhr, status){
                toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
            },
        });
    });

    // CHECK ALL ITEMS
    $("#tblMasterListToAlloc #chkAllItems").click(function(){
        if($(this).prop('checked')) {
            $(".itemCheckbox").prop('checked', 'checked');//check all result
        }else{
            dtMasterListToAlloc.draw(); //reload table to uncheck all
        }
    });

    $('#btnSaveNewAllocation').click(function (e){
        console.log('clicked submit');
        $('#formAddAllocation').submit();
    });

    $('#formAddAllocation').submit(function (e) {
        console.log('submitted');
        e.preventDefault();

        $.ajax({
            type:"POST",
            url: "add_allocation_data",
            data: $('#formAddAllocation').serialize(),
            dataType: "json",
            success: function(response){
                if(response['hasError'] == 0){
                    toastr.success('Successful!');
                    $("#modalAddAllocation").modal('hide');
                    dtAllocation.draw();
                }else{
                    toastr.error('Error!, Please Contanct ISS Local 208');
                }
            }
        });
    });

    function GetDetailsForFiltering(){
        console.log('test filter function');
        let result;
        // let result = '<option value="" disabled selected> Select Device Name </option>';
        $.ajax({
            type: "get",
            url: "get_masterlist_info_for_filter",
            dataType: "json",
            // data: {
            //     'category': category
            // },
            beforeSend: function(){
                result = '<option value="0" disabled selected>--Loading--</option>';
            },
            success: function (response) {
                let department = response['departmentDetails'];
                let section = response['sectionDetails'];
                // if(device_details.length > 0) {

                //         // result = '<option value="" disabled selected> Select Device Name </option>';
                //         if(category == 'factory'){
                //             result = '<option value="" disabled selected>--Select Factory--</option>';
                //         }else if(category == 'department'){
                //             result = '<option value="" disabled selected>--Select Department--</option>';
                //         }else if(category == 'section'){
                //             result = '<option value="" disabled selected>--Select SecDepartmenttion--</option>';
                //         }

                //     for (let index = 0; index < device_details.length; index++) {
                //         result += '<option value="' + device_details[index]['name'] + '">' + device_details[index]['name'] + '</option>';
                //     }
                // }else{
                //     result = '<option value="0" selected disabled> -- No record found -- </option>';
                // }
                // cboElement.html(result);

                result_department = '<option value="" disabled selected> Select Department </option>';
                result_section = '<option value="" disabled selected> Select Section </option>';

                result_department += '<option value="">ALL</option>';
                for (let d = 0; d < department.length; d++){
                    result_department += '<option value="'+department[d].Department+'">'+department[d].Department+'</option>';
                }

                result_section += '<option value="">ALL</option>';
                for (let s = 0; s < section.length; s++){
                    result_section += '<option value="'+section[s].Section+'">'+section[s].Section+'</option>';
                }

                $('.selectAllocDepartment').html(result_department);
                $('.selectAllocSection').html(result_section);
            },
            error: function(data, xhr, status) {
                result = '<option value="0" selected disabled> -- Reload Again -- </option>';
                $('.selectAllocDepartment').html(result);
                $('.selectAllocSection').html(result);
                console.log('Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
            }
        });
    }

    $('.selectAllocFactory').on('change', function(e){
        var selectedValue = $(this).val();
        console.log('factory', selectedValue);
        if (selectedValue) {
            dtMasterListToAlloc.column(6).search($(this).val()).draw();
        }else{
            dtMasterListToAlloc.column(6).search("").draw();
        }
    });

    $('.selectAllocDepartment').on('change', function(e){
        var selectedValue = $(this).val();
        if (selectedValue) {
            dtMasterListToAlloc.column(3).search($(this).val()).draw();
        }else{
            dtMasterListToAlloc.column(3).search("").draw();
        }
    });

    $('.selectAllocSection').on('change', function(e){
        var selectedValue = $(this).val();
        if (selectedValue) {
            dtMasterListToAlloc.column(4).search($(this).val()).draw();
        }else{
            dtMasterListToAlloc.column(4).search("").draw();
        }
    });
});
