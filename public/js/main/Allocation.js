$(document).ready(function(){
    let txtGlobalUserId = $('#txtGlobalUserId').val();
    const formAddAllocation = $('#formAddAllocation');
    // Run check every minute
    // let currentSessionUserRole;
    // checkButtonStatus(currentSessionUserRole);
    getDetailsForFiltering();
    // getUsersAllowedAfterCutoff(txtGlobalUserId);

    function getUsersAllowedAfterCutoff(txtGlobalUserId){
        $.ajax({
            url: "get_user_by_rapidx_id",
            method: "get",
            data: {
                userId : txtGlobalUserId,
            },
            dataType: "json",
            beforeSend: function(){

            },
            success: function(response){
                let userDetails = response['userDetails'];
                if(userDetails != null){
                    if(userDetails.user_roles == 1){
                        console.log('admin');
                        currentSessionUserRole = 'admin';
                    }else{
                        console.log('not admin');
                        currentSessionUserRole = 'user';
                    }
                }else{
                    console.log('no data userDetails');
                }
            },
            error: function(data, xhr, status){
                toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
            },
        });
    }

    function checkButtonStatus(currentSessionUserRole){
        const now = new Date();
        const currentHour = now.getHours(); // 0–23
        const currentMinute = now.getMinutes(); // 0–59

        const targetHour = 14; // 2 PM
        // const targetMinute = 00;
        const addButton = $('#btnAddAllocation');

        // Disable if current time is later than 2:00 PM
        if(currentHour > targetHour) {
            console.log('disabled');
            if(currentSessionUserRole == 'admin'){
                // addButton.prop('disabled', true); //clark comment
                addButton.prop('disabled', false);
            }
        }else{
            console.log('enabled');
            addButton.prop('disabled', false);
        }
    }

    $('#filterRequestType').change(function (e) {
        e.preventDefault();
        if($(this).val() == 1){
            $('#filterFactory').prop('disabled', false);
        }else{
            $('#filterFactory').val(0).trigger('change');
            $('#filterFactory').prop('disabled', true);
        }
        dtAllocation.draw();
    });

    $('#filterFactory').change(function (e) {
        e.preventDefault();
        dtAllocation.draw();
    });

    $('#filterStartDate').change(function (e) {
        e.preventDefault();
        dtAllocation.draw();
    });

    $('#filterEndDate').change(function (e) {
        e.preventDefault();
        dtAllocation.draw();
    });

    $('.select2bs5').select2({
        width: '100%',
        theme: 'bootstrap-5'
    });

    // When start date changes
    $('#formAddAllocation #txtStartDate').on('change', function(){
        let startDate = $(this).val();
        $('#formAddAllocation #txtEndDate').attr('min', startDate); // end date cannot be before start date
        console.log('startval', startDate);
        console.log('endval', $('#formAddAllocation #txtEndDate').val());

        // if($('#formAddAllocation #txtTypeOfRequest').val() == 1 && startDate != '' && $('#formAddAllocation #txtEndDate').val() != ''){
        //     $('#txtAllocIncoming').prop('disabled', false);
        //     $('#txtAllocOutgoing').prop('disabled', false);
        // }else{
        //     $('#txtAllocIncoming').prop('disabled', true);
        //     $('#txtAllocOutgoing').prop('disabled', true);
        // }
        buttonDisablingForInOut()
        getSchedulesForFiltering()
    });

    // When end date changes
    $('#formAddAllocation #txtEndDate').on('change', function(){
        let endDate = $(this).val();
        $('#formAddAllocation #txtStartDate').attr('max', endDate); // start date cannot be after end date

        // if($('#formAddAllocation #txtTypeOfRequest').val() == 1 && endDate != '' && $('#formAddAllocation #txtStartDate').val() != ''){
        //     $('#txtAllocIncoming').prop('disabled', false);
        //     $('#txtAllocOutgoing').prop('disabled', false);
        // }else{
        //     $('#txtAllocIncoming').prop('disabled', true);
        //     $('#txtAllocOutgoing').prop('disabled', true);
        // }
        buttonDisablingForInOut()
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
                param.rapidXUserId          = txtGlobalUserId;
                param.RequestType           = $('#filterRequestType').find(':selected').val();
                param.Factory               = $('#filterFactory').find(':selected').val();
                param.AllocationStartDate   = $('#filterStartDate').val();
                param.AllocationEndDate     = $('#filterEndDate').val();
            },
        },
        "columns":[
            { "data" : "action", orderable:false, searchable:false},
            { "data" : "request_status"},
            { "data" : "date_requested"},
            { "data" : "request_category"},
            { "data" : "allocation_date"},
            { "data" : "allocated_factory"},
            { "data" : "no_of_allocated_emp"},
            { "data" : "requestor_user_info.name"}
        ],
        "createdRow": function(row, data, index) {
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
        "lengthMenu": [10, 30, 50, 100, 500],
        "pageLength": 30,
        // "lengthMenu": [[-1], ["All"]], // remove other options, only show "All"
        "ajax" : {
            url: "view_master_list_for_allocation",
            data: function (param){
                param.rapidXUserId  = txtGlobalUserId;
                param.requestControlNo = $('#formAddAllocation #txtRequestControlNo').val();
                param.isViewMode = $('#formAddAllocation #txtIsViewMode').val();
            },
            beforeSend: function (jqXHR, settings){
                // $("#divForTblMasterListToAllocThead").addClass('d-none');
                $("#divForTblMasterListToAllocTbody").addClass('d-none');
                $('#tblMasterListToAlloc').find('#actionTextTheadDiv').addClass('d-none');
                $('#tblMasterListToAlloc').find('#actionCheckAllTheadDiv').addClass('d-none');
            },
            complete: function (){
                // $("#divForTblMasterListToAllocThead").removeClass('d-none');
                $("#divForTblMasterListToAllocTbody").removeClass('d-none');
                if($('#formAddAllocation #txtIsViewMode').val() != 0){
                    $('#tblMasterListToAlloc').find('#actionTextTheadDiv').removeClass('d-none');
                    console.log('txtIsViewMode', 'true');
                }else{
                    $('#tblMasterListToAlloc').find('#actionCheckAllTheadDiv').removeClass('d-none');
                    console.log('txtIsViewMode', 'false');
                }
            }
        },
        "columns":[
            { "data" : "action", width: '5%', orderable:false, searchable:false},
            { "data" : "masterlist_employee_number", width: '5%'},
            { "data" : "name", width: '15%'},
            { "data" : "department", width: '10%'},
            { "data" : "section", width: '10%'},
            { "data" : "routes_info.routes_name", width: '30%'},
            { "data" : "factory"},
            { "data" : "masterlist_incoming"},
            { "data" : "masterlist_outgoing"},
            { "data": "rapidx_user_info.name"},

            // { "data" : "ml_route", orderable:false, searchable:false},
        ],
        "createdRow": function(row, data, index) {
            $('td', row).eq(5).css('white-space', 'normal');
        },
    });

    $('#btnAddAllocation').click(function (e) {
        e.preventDefault();
        const today = new Date();
        let yyyy = today.getFullYear();
        let mm = String(today.getMonth() + 1).padStart(2, '0'); // Months start at 0
        let dd = String(today.getDate()).padStart(2, '0');
        let formattedDate = `${yyyy}-${mm}-${dd}`;
        console.log(formattedDate);

        $('#formAddAllocation #txtRequestControlNo').val('');
        $('#allocationRequestChangeTitle').html('<i class="fas fa-info-circle"></i>&nbsp; Add Employee/s Allocation Request');

        $('#tblMasterListToAlloc').find('#chkAllItems').prop('disabled', false);
        $('.selectAllocFactory').prop('disabled', false);
        $('.selectAllocDepartment, .selectAllocSection').prop('disabled', true);

        $('.selectAllocFactory').val('').trigger('change');
        $('.selectAllocDepartment').val('').trigger('change');
        $('.selectAllocSection').val('').trigger('change');

        $('#formAddAllocation #txtStartDate').attr('min', formattedDate); // start date cannot be after end date
        $('#formAddAllocation #txtEndDate').attr('min', formattedDate); // start date cannot be after end date

        $.ajax({
            url: "get_user_info",
            method: "get",
            data:{
                userId  : txtGlobalUserId,
                factory : txtGlobalUserId,
            },
            dataType: "json",
            beforeSend: function(){
            },
            success: function(response){
                let userDetails = response['userDetails'];
                let scheduleDetails = response['scheduleDetails'];
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

        // filterDataTable(false, false); //this will draw the table;
    });

    let selectedIds = new Set();

    // Handle individual row checkbox
    $('#tblMasterListToAlloc').on('change', '.itemCheckbox', function () {
        const id = $(this).val(); // Make sure each checkbox has a unique value (e.g., ID)
        if ($(this).prop('checked')) {
            selectedIds.add(id);
        } else {
            selectedIds.delete(id);
        }

        // ✅ Uncheck master checkbox if nothing is selected
        if (selectedIds.size === 0) {
            $('#chkAllItems').prop('checked', false);
        }
        console.log('selectedIds', selectedIds)
    });

    $('#txtAllocFactory').on('change', function(){
        buttonDisablingForInOut()
    })

    //     getSchedulesForFiltering()
        // let txtFactory = $(this).val();
        // if(txtFactory == 'F1'){
        //     txtFactory = 1;
        // }else if(txtFactory == 'F3'){
        //     txtFactory = 3;
        // }else{
        //     txtFactory = null;
        // }

        // $.ajax({
        //     url: "get_cutoff_time",
        //     method: "get",
        //     data:{
        //         factory : txtFactory,
        //     },
        //     dataType: "json",
        //     beforeSend: function(){
        //     },
        //     success: function(response){
        //         // if(txtFactory != null){
        //         //     $('#txtAllocIncoming').prop('disabled', false);
        //         //     $('#txtAllocOutgoing').prop('disabled', false);
        //         // }

        //         let scheduleDetails = response['scheduleDetails'];
        //         let disabled = '';

        //         if(scheduleDetails != null){
        //             result_out_schedule = '<option value="" disabled selected> Select Outgoing </option>';
        //             result_in_schedule = '<option value="" disabled selected> Select Incoming </option>';

        //             result_out_schedule += '<option value="N/A">N/A</option>';
        //             result_in_schedule += '<option value="N/A">N/A</option>';

        //             for (let c = 0; c < scheduleDetails.length; c++){
        //                 if(scheduleDetails[c].status == 0){//Not Active
        //                     disabled = 'disabled';
        //                 }else{
        //                     disabled = '';
        //                 }

        //                 if(scheduleDetails[c].category > 0){ //1 or 2 Incoming  & Outgoing
        //                     result_out_schedule += '<option '+disabled+' value="'+scheduleDetails[c].schedule+'">'+scheduleDetails[c].schedule+'</option>';
        //                 }

        //                 if(scheduleDetails[c].category == 2){ //1 Outgoing
        //                     result_in_schedule += '<option '+disabled+' value="'+scheduleDetails[c].schedule+'">'+scheduleDetails[c].schedule+'</option>';
        //                 }
        //             }
        //             $('.SelectAllocOutgoing').html(result_out_schedule);
        //             $('.SelectAllocIncoming').html(result_in_schedule);
        //         }
        //     },
        //     error: function(data, xhr, status){
        //         toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
        //     },
        // });
    // });

    function buttonDisablingForInOut(){
        let RequestTypeVal = formAddAllocation.find('#txtTypeOfRequest').val();
        let FactoryVal = formAddAllocation.find('#txtAllocFactory').val();
        let StartdateVal = formAddAllocation.find('#txtStartDate').val();
        let EnddateVal = formAddAllocation.find('#txtEndDate').val();

        if(RequestTypeVal != 2 && FactoryVal != null && StartdateVal != '' && EnddateVal != ''){
            $('#formAddAllocation').find('#txtAllocIncoming').prop('disabled', false);
            $('#formAddAllocation').find('#txtAllocOutgoing').prop('disabled', false);
        }else{
            $('#formAddAllocation').find('#txtAllocIncoming').prop('disabled', true);
            $('#formAddAllocation').find('#txtAllocOutgoing').prop('disabled', true);
        }
    }

    function getSchedulesForFiltering(){
        const today = new Date();
        let yyyy = today.getFullYear();
        let mm = String(today.getMonth() + 1).padStart(2, '0'); // Months start at 0
        let dd = String(today.getDate()).padStart(2, '0');
        let formattedDate = `${yyyy}-${mm}-${dd}`;

        let txtFactory = $('#formAddAllocation').find('#txtAllocFactory').val();
        let txtStartDate = $('#formAddAllocation').find('#txtStartDate').val();

        if(txtFactory == 'F1'){
            txtFactory = 1;
        }else if(txtFactory == 'F3'){
            txtFactory = 3;
        }else{
            txtFactory = null;
        }

        $.ajax({
            url: "get_cutoff_time",
            method: "get",
            data:{
                param_factory : txtFactory,
                // param_start_date : txtStartDate,
            },
            dataType: "json",
            beforeSend: function(){

            },
            success: function(response){
                let scheduleDetails = response['scheduleDetails'];
                let disabled = '';

                if(scheduleDetails != null){
                    result_out_schedule = '<option value="" disabled selected> Select Outgoing </option>';
                    result_in_schedule = '<option value="" disabled selected> Select Incoming </option>';

                    result_out_schedule += '<option value="N/A">N/A</option>';
                    result_in_schedule += '<option value="N/A">N/A</option>';

                    for (let c = 0; c < scheduleDetails.length; c++){
                        if(scheduleDetails[c].status == 0){//Locked
                            if(scheduleDetails[c].schedule != '7:30AM' && txtStartDate == formattedDate){//SUCCEEDING DAYS
                                disabled = 'disabled';
                            }else if(scheduleDetails[c].schedule == '7:30AM' && txtStartDate > formattedDate){//TODAY
                                disabled = 'disabled';
                            }
                        }else{
                            disabled = '';
                        }

                        if(scheduleDetails[c].category > 0){ //1 or 2 Incoming  & Outgoing
                            result_out_schedule += '<option '+disabled+' value="'+scheduleDetails[c].schedule+'">'+scheduleDetails[c].schedule+'</option>';
                        }

                        if(scheduleDetails[c].category == 2){ //1 Outgoing
                            result_in_schedule += '<option '+disabled+' value="'+scheduleDetails[c].schedule+'">'+scheduleDetails[c].schedule+'</option>';
                        }
                    }
                    $('.SelectAllocOutgoing').html(result_out_schedule);
                    $('.SelectAllocIncoming').html(result_in_schedule);
                }
            },
            error: function(data, xhr, status){
                toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
            },
        });
    }

    dtMasterListToAlloc.on('draw', function () {
        // Loop through all checkboxes in the current page
        $('.itemCheckbox').each(function () {
            const id = $(this).val();
            if (selectedIds.has(id)) {
                $(this).prop('checked', true);
            }
        });
        console.log('selectedIds', selectedIds)
    });

    // CHECK ALL ITEMS
    $("#tblMasterListToAlloc #chkAllItems").click(function(){
        if($(this).prop('checked')) {
            $(".itemCheckbox").prop('checked', true);//check all result
        }else{
            $(".itemCheckbox").prop('checked', false);
            selectedIds.clear();
        }

        const isChecked = $(this).prop('checked');
        $('.itemCheckbox').each(function () {
            const id = $(this).val();

            $(this).prop('checked', isChecked);

            if(isChecked){
                selectedIds.add(id);
            }else{
                selectedIds.delete(id);
            }
        });
        console.log('selectedIds', selectedIds)
    });

    $('#btnSaveNewAllocation').click(function (e){
        $('#formAddAllocation').submit();
    });

    $('#formAddAllocation').submit(function (e) {
        e.preventDefault();
        // let serializedFormAddAllocation = $('#formAddAllocation').serialize();

        let formDataAddAllocation = new FormData($('#formAddAllocation')[0]);

        if(selectedIds.size > 0){
            // Append each ID in the Set
            selectedIds.forEach(id => {
                formDataAddAllocation.append('selectedIds[]', id);
            });
        }else{
            toastr.error('No Selected Employee to be Allocated, Please Select First');
        }

        $.ajax({
            type:"POST",
            url: "add_allocation_data",
            data: formDataAddAllocation,
            processData: false,
            contentType: false,
            beforeSend: function(){
                $('#formAddAllocation').find('#btnSaveNewAllocation').prop('disabled', true)
            },
            success: function(response){
                setTimeout(() => {
                    $('#formAddAllocation').find('#btnSaveNewAllocation').prop('disabled', false)
                }, 3000);
                if(response['validationHasError'] == 1){
                    toastr.error('Saving failed!, Please complete all required fields');
                    if (response['error']['type_of_request'] === undefined) {
                        $("#txtTypeOfRequest").removeClass('is-invalid');
                        $("#txtTypeOfRequest").attr('title', '');
                    } else {
                        $("#txtTypeOfRequest").addClass('is-invalid');
                        $("#txtTypeOfRequest").attr('title', response['error']['type_of_request']);
                    }

                    if (response['error']['alloc_factory'] === undefined) {
                        $("#txtAllocFactory").removeClass('is-invalid');
                        $("#txtAllocFactory").attr('title', '');
                    } else {
                        $("#txtAllocFactory").addClass('is-invalid');
                        $("#txtAllocFactory").attr('title', response['error']['alloc_factory']);
                    }

                    if (response['error']['alloc_incoming'] === undefined) {
                        $("#txtAllocIncoming").removeClass('is-invalid');
                        $("#txtAllocIncoming").attr('title', '');
                    } else {
                        $("#txtAllocIncoming").addClass('is-invalid');
                        $("#txtAllocIncoming").attr('title', response['error']['alloc_incoming']);
                    }

                    if (response['error']['alloc_outgoing'] === undefined) {
                        $("#txtAllocOutgoing").removeClass('is-invalid');
                        $("#txtAllocOutgoing").attr('title', '');
                    } else {
                        $("#txtAllocOutgoing").addClass('is-invalid');
                        $("#txtAllocOutgoing").attr('title', response['error']['alloc_outgoing']);
                    }

                    if (response['error']['start_date'] === undefined) {
                        $("#txtStartDate").removeClass('is-invalid');
                        $("#txtStartDate").attr('title', '');
                    } else {
                        $("#txtStartDate").addClass('is-invalid');
                        $("#txtStartDate").attr('title', response['error']['start_date']);
                    }

                    if (response['error']['end_date'] === undefined) {
                        $("#txtEndDate").removeClass('is-invalid');
                        $("#txtEndDate").attr('title', '');
                    } else {
                        $("#txtEndDate").addClass('is-invalid');
                        $("#txtEndDate").attr('title', response['error']['end_date']);
                    }
                }else if (response['hasError'] == 1 && response['result'] == 0 ) {
                    toastr.error(response['message']);
                }else if (response['hasExisted'] > 0 ) {
                    toastr.error('Selected Employee/s is already allocated, Please Re-evaluate');

                    // First, clear any previous highlights/details
                    $('.conflict-row').removeClass('conflict-row');
                    $('.conflict-details-row').remove();

                    // Loop through conflicts
                    response.conflicts.forEach(function(conflict){

                        // Find the checkbox with matching data-checkbox-id
                        dtMasterListToAlloc.rows().every(function(){
                            let $row = $(this.node());
                            let checkbox = $row.find('.itemCheckbox');
                            let checkboxId = checkbox.data('checkbox-id'); //read data-checkbox-id
                            console.log('checkboxId, conflict.requestee_ml_id', checkboxId, conflict.requestee_ml_id);

                            if(checkboxId == conflict.requestee_ml_id){
                                console.log('matched',$row);

                                // Highlight main row
                                $row.addClass('conflict-row');

                                // Get column count for colspan
                                let colspan = $row.find('td').length;

                                // Insert a details row under it
                                let detailsRow = `
                                    <tr class="conflict-details-row">
                                        <td colspan="${colspan}" style="font-weight:bold; color:#000; text-align:center;">
                                            ${conflict.requested_emp} is already allocated from <span style="color:#b30000">${conflict.start}</span>
                                            to <span style="color:#b30000">${conflict.end}</span>
                                            (Requested by: <span style="color:#b30000">${conflict.requested_by}</span>)
                                        </td>
                                    </tr>
                                `;

                                // Append details row after the highlighted row
                                $row.after(detailsRow);
                            }
                        });
                    });

                }else if (response['hasError'] == 0 ) {
                    toastr.success('Successful!');
                    $("#modalAddAllocation").modal('hide');
                    dtAllocation.draw();
                }else{
                    toastr.error('Error!, Please Contact ISS Local 208');
                }
            }
        });
    });

    function getDetailsForFiltering(param_factory, param_department, param_section){
        let result;
        // let result = '<option value="" disabled selected> Select Device Name </option>';
        $.ajax({
            type: "get",
            url: "get_masterlist_info_for_filter",
            dataType: "json",
            data: {
                'param_factory': param_factory,
                'param_department': param_department,
            },
            beforeSend: function(){
                result = '<option value="0" disabled selected>--Loading--</option>';
            },
            success: function (response) {
                let department = response['departmentDetails'];
                let section = response['sectionDetails'];

                if(param_department == 0){
                    result_department = '<option value="" disabled selected> Select Department </option>';
                    result_department += '<option data-id="ALL" value="ALL">ALL</option>';
                    for (let d = 0; d < department.length; d++){
                        result_department += '<option data-id="'+department[d].pkid+'" value="'+department[d].Department+'">'+department[d].Department+'</option>';
                    }

                    $('.selectAllocDepartment').html(result_department);
                }

                if(param_section == 0){
                    result_section = '<option value="" disabled selected> Select Section </option>';
                    result_section += '<option value="ALL">ALL</option>';
                    for (let s = 0; s < section.length; s++){
                        result_section += '<option value="'+section[s].Section+'">'+section[s].Section+'</option>';
                    }

                    $('.selectAllocSection').html(result_section);
                }
            },
            error: function(data, xhr, status) {
                result = '<option value="0" selected disabled> -- Reload Again -- </option>';
                $('.selectAllocDepartment').html(result);
                $('.selectAllocSection').html(result);
                console.log('Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
            }
        });
    }

    function filterDataTable(loadExistingData = false, viewMode = false){
        const factoryVal = $('.selectAllocFactory').val();
        const deptVal = $('.selectAllocDepartment').val();
        const sectionVal = $('.selectAllocSection').val();

        if(!loadExistingData && (!factoryVal || !deptVal || !sectionVal)) {
            dtMasterListToAlloc
                .column(6).search('___NO_MATCH___')
                .column(3).search('')
                .column(4).search('')
                .draw();
        }else{
            dtMasterListToAlloc
            .column(6).search(factoryVal && factoryVal !== "ALL" ? factoryVal : '')
            .column(3).search(deptVal && deptVal !== "ALL" ? '<span>' + deptVal + '</span>' : '')
            .column(4).search(sectionVal && sectionVal !== "ALL" ? sectionVal : '')
            .draw();
        }

        // console.log('viewMode', viewMode);
        dtMasterListToAlloc.column(0).visible(!viewMode);
        // if(viewMode){
        //     dtMasterListToAlloc.column(0).visible(false); // column index starts at 0
        //     // console.log('col 0 invi');
        // }else{
        //     dtMasterListToAlloc.column(0).visible(true); // column index starts at 0
        //     // console.log('col 0 not invi');
        // }
    }

    $('#txtTypeOfRequest').on('change', function() {
        const selectedValue = $(this).val();
        if (selectedValue == 2) {
            $('#txtAllocIncoming, #txtAllocOutgoing, #txtAllocFactory').prop('disabled', true);
            $('#txtAllocIncoming, #txtAllocOutgoing, #txtAllocFactory').val('').trigger('change');
        }else{
            $('#txtAllocFactory').prop('disabled', false);
        }
    });

    $('#txtAllocIncoming').on('change', function() {
        const selectedValue = $(this).val();
        if (selectedValue == 'N/A') {
            $('#na_out_option').prop('disabled', true);
        }else{
            $('#na_out_option').prop('disabled', false);
        }
    });

    $('#txtAllocOutgoing').on('change', function() {
        const selectedValue = $(this).val();
        if (selectedValue == 'N/A') {
            $('#na_in_option').prop('disabled', true);
        }else{
            $('#na_in_option').prop('disabled', false);
        }
    });

    $('.selectAllocFactory').on('change', function() {
        const factoryVal = $(this).val();

        // Reset department and section
        $('.selectAllocDepartment').val('');
        $('.selectAllocSection').val('');

        $('.selectAllocDepartment').val('').prop('disabled', true);
        $('.selectAllocSection').val('').prop('disabled', true);

        if(factoryVal){
            $('.selectAllocDepartment').prop('disabled', false);
        }
    });

    $('.selectAllocDepartment').on('change', function() {
        const deptVal = $(this).val();

        // Reset section
        $('.selectAllocSection').val('');
        $('.selectAllocSection').prop('disabled', true);

        if (deptVal) {
            $('.selectAllocSection').prop('disabled', false);
        }
    });

    $('.selectAllocFactory, .selectAllocDepartment, .selectAllocSection').on('change', function (){
        let factoryVal  = $('.selectAllocFactory').find(':selected').val();
        let departmentVal = $('.selectAllocDepartment').find(':selected').data('id');
        let sectionVal = $('.selectAllocSection').find(':selected').val();

        if(departmentVal == undefined){
            departmentVal = 0;
        }

        if(sectionVal == undefined){
            sectionVal = 0;
        }

        // ✅ Check if the changed element is .selectAllocFactory
        getDetailsForFiltering(factoryVal, departmentVal, sectionVal);
        filterDataTable(false, false); //this will draw the table;
    });

    $('#modalAddAllocation').on('hidden.bs.modal', function (e){
        let form = $(this).find('form');
        form[0].reset(); // reset normal fields
        // console.log('test val', $('#formAddAllocation #txtIsViewMode').val());

        selectedIds.clear(); // removes all items
        // form.find('select').val('').trigger('change'); // reset
        form.find('#txtTypeOfRequest').val(0).trigger('change'); // reset
        form.find('#txtAllocFactory').val('').trigger('change'); // reset
        form.find('#txtAllocIncoming').val('').trigger('change'); // reset
        form.find('#txtAllocOutgoing').val('').trigger('change'); // reset
        form.find('#txtAllocOutgoing').val('').trigger('change'); // reset

        // Clear values
        form.find('#txtStartDate').val('');
        form.find('#txtEndDate').val('');

        // Remove restrictions
        form.find('#txtStartDate').removeAttr('max');
        form.find('#txtEndDate').removeAttr('min');

        $('#formAddAllocation').find('input').prop('disabled', false)
        $('#formAddAllocation').find('#txtAllocFactory').prop('disabled', false)
        $('#txtAllocFactory').prop('disabled', false);

        $('#modalAddAllocation #btnSaveNewAllocation').removeClass('d-none');

        $('#formAddAllocation').find('select').removeClass('is-invalid');
        $('#formAddAllocation').find('select').attr('title', '');

        $('#formAddAllocation').find('input').removeClass('is-invalid');
        $('#formAddAllocation').find('input').attr('title', '');

        $('#chkAllItems').prop('disabled', false);
    });

    $('#tblAllocation').on('click', '.editRequest', function (e){
        e.preventDefault();
        $('#allocationRequestChangeTitle').html('<i class="fas fa-info-circle"></i>&nbsp; Edit Employee/s Allocation Request');
        $('#formAddAllocation #txtIsViewMode').val(2);
        $('.selectAllocFactory').val('').trigger('change');
        $('.selectAllocDepartment').val('').trigger('change');
        $('.selectAllocSection').val('').trigger('change');

        $('.selectAllocFactory').prop('disabled', true);
        $('.selectAllocDepartment').prop('disabled', true);
        $('.selectAllocSection').prop('disabled', true);

        $('#formAddAllocation').find('input').prop('disabled', false)
        $('#txtTypeOfRequest, #txtAllocIncoming, #txtOAllocutgoing, #txtAllocFactory').prop('disabled', false);

        $('#modalAddAllocation').modal('show');
        let control_number = $(this).data('control_no');

        $.ajax({
            type: "get",
            url: "get_allocation_data",
            data: {
                userId : txtGlobalUserId,
                control_number : control_number,
            },
            dataType: "json",
            success: function (response) {
                let allocDetails = response['allocationDetails'];
                let userDetails = response['userDetails'];

                if(userDetails != null){
                    $('#txtEmployeeNumber', formAddAllocation).val(userDetails.rapidx_user_info.employee_number);
                    $('#txtRequestorId', formAddAllocation).val(userDetails.rapidx_user_id);
                    $('#txtRequestor', formAddAllocation).val(userDetails.name);
                    $('#txtDepartmentSection', formAddAllocation).val(userDetails.department);
                }else{
                    toastr.warning('No record found!');
                }

                $('#txtRequestControlNo', formAddAllocation).val(allocDetails[0].control_number);
                $('#txtTypeOfRequest', formAddAllocation).val(allocDetails[0].request_type).trigger('change');
                $('#txtAllocFactory', formAddAllocation).val(allocDetails[0].alloc_factory).trigger('change');
                $('#txtAllocIncoming', formAddAllocation).val(allocDetails[0].alloc_incoming).trigger('change');
                $('#txtAllocOutgoing', formAddAllocation).val(allocDetails[0].alloc_outgoing).trigger('change');
                $('#txtStartDate', formAddAllocation).val(allocDetails[0].alloc_date_start);
                $('#txtEndDate', formAddAllocation).val(allocDetails[0].alloc_date_end);

                allocDetails.forEach(function(id) {
                    selectedIds.add(id.requestee_ml_id);
                });
                console.log('selectedIds', selectedIds)

                filterDataTable(true, false); //this will draw the table;
            }
        });
    });

    $('#tblAllocation').on('click', '.viewRequest', function (e){
        e.preventDefault();

        $('#allocationRequestChangeTitle').html('<i class="fas fa-info-circle"></i>&nbsp; View Employee/s Allocation Request');
        $('#formAddAllocation #txtIsViewMode').val(1);
        $('#modalAddAllocation').modal('show');
        let control_number = $(this).data('control_no');

        $.ajax({
            type: "get",
            url: "get_allocation_data",
            data: {
                userId : txtGlobalUserId,
                control_number : control_number,
            },
            dataType: "json",
            success: function (response){
                let allocDetails = response['allocationDetails'];
                let userDetails = response['userDetails'];

                if(userDetails != null){
                    $('#txtEmployeeNumber', formAddAllocation).val(userDetails.rapidx_user_info.employee_number);
                    $('#txtRequestorId', formAddAllocation).val(userDetails.rapidx_user_id);
                    $('#txtRequestor', formAddAllocation).val(userDetails.name);
                    $('#txtDepartmentSection', formAddAllocation).val(userDetails.department);
                }else{
                    toastr.warning('No record found!');
                }

                $('#txtRequestControlNo', formAddAllocation).val(allocDetails[0].control_number);
                $('#txtTypeOfRequest', formAddAllocation).val(allocDetails[0].request_type).trigger('change');
                $('#txtAllocFactory', formAddAllocation).val(allocDetails[0].alloc_factory).trigger('change');
                $('#txtAllocIncoming', formAddAllocation).val(allocDetails[0].alloc_incoming).trigger('change');
                $('#txtAllocOutgoing', formAddAllocation).val(allocDetails[0].alloc_outgoing).trigger('change');
                $('#txtStartDate', formAddAllocation).val(allocDetails[0].alloc_date_start);
                $('#txtEndDate', formAddAllocation).val(allocDetails[0].alloc_date_end);


                $('#formAddAllocation').find('select').prop('disabled', true)
                $('#txtStartDate, #txtEndDate, #txtAllocIncoming, #txtAllocOutgoing, #txtAllocFactory').prop('disabled', true);

                allocDetails.forEach(function(id) {
                    selectedIds.add(id.requestee_ml_id);
                });
                console.log('selectedIds', selectedIds)
                filterDataTable(true, true); //this will draw the table;
            }
        });
    });

    $("#tblMasterListToAlloc").on('click', '.btnRemoveEmp', function(){
        let id = $(this).data('checkbox-id'); // get the ID from the button
        $(this).closest('tr').remove();
        selectedIds.delete(id);
        console.log('selectedIds', selectedIds)
    });

    $('#tblAllocation').on('click', '.updateRequestStatus', function () {
        let deleteControlNo = $(this).data('control_no');
        let requestStatus = $(this).data('status');
        if(requestStatus == 0){
            $('#changeStatusChangeDivModalHeader').addClass('bg-danger');
            $('#changeStatusChangeDivModalHeader').removeClass('bg-success');
            $('#changeStatusChangeTitle').html('<i class="fa-solid fa-ban"></i>&nbsp;&nbsp; Cancel Request?');
            $('#changeStatusChangeLabel').html('Are you sure you want to cancel this request?');
            $('#btnDeleteRequest').addClass('btn-danger');
            $('#btnDeleteRequest').removeClass('btn-success');
            $('#btnDeleteRequest').html('<i class="fa fa-arrow-rotate-right"></i> Cancel Allocation Request');
        }else{
            $('#changeStatusChangeDivModalHeader').addClass('bg-success');
            $('#changeStatusChangeDivModalHeader').removeClass('bg-danger');
            $('#changeStatusChangeTitle').html('<i class="fa-solid fa-arrow-rotate-right"></i>&nbsp;&nbsp; Activate Request?');
            $('#changeStatusChangeLabel').html('Are you sure you want to activate this request?');
            $('#btnDeleteRequest').removeClass('btn-danger');
            $('#btnDeleteRequest').addClass('btn-success');
            $('#btnDeleteRequest').html('<i class="fa fa-arrow-rotate-right"></i> Activate Allocation Request');

        }

        $('#modalDeleteRequest').modal('show')
        $('#modalDeleteRequest').find('#deleteFrmControlNumber').val(deleteControlNo);
        $('#modalDeleteRequest').find('#deleteFrmRequestStatus').val(requestStatus);
    });

    $('#FrmChangeStatusAllocation').submit(function (e) {
        console.log('submitted');
        e.preventDefault();

        $.ajax({
            type:"POST",
            url: "change_status_allocation",
            data: $('#FrmChangeStatusAllocation').serialize(),
            dataType: "json",
            success: function(response){
                if (response['hasError'] == 1 && response['result'] == 0 ) {
                    toastr.error(response['message']);
                }else if (response['hasError'] == 0 ) {
                    toastr.success('Update Successful!');
                    $("#modalDeleteRequest").modal('hide');
                    dtAllocation.draw();
                }else{
                    toastr.error('Error!, Please Contanct ISS Local 208');
                }
            }
        });
    });
})
