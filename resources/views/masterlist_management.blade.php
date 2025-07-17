{{-- @extends('shared.includes.session') --}}

@if (file_exists(resource_path('views/shared/includes/session.blade.php')))
    @extends('shared.includes.session')
@else
    @php
        echo '<script type="text/javascript">
                window.location = "/";
            </script>';
    @endphp
@endif

@section('title', 'Dashboard')
@section('content_page')
    <style>
    </style>
    <div class="content-wrapper">
        <!-- Main content -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Masterlist Management</h1>
                    </div>
                    <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Masterlist Management</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card" style="margin-bottom: 80px">
                            <div class="card-header">
                                <h3 class="card-title" style="margin-top: 8px;">Masterlist Management</h3>
                                {{-- <button class="btn float-right reload"><i class="fas fa-sync-alt"></i></button> --}}
                            </div>
                            <div class="card-body">
                                <div class="text-right mt-4">                   
                                    <button type="button" class="btn btn-primary mb-3" id="buttonAddMasterlist" data-bs-toggle="modal" data-bs-target="#modalAddMasterlist"><i class="fa fa-plus fa-md"></i> Add New</button>
                                </div>
                                {{-- <div class="table-responsive"> --}}
                                    <table id="tableMasterlist" class="table table-responsive table-bordered table-hover nowrap" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>Action</th>
                                                <th>Status</th>
                                                <th>Employee #</th>
                                                <th>Employee name</th>
                                                <th>Incoming</th>   
                                                <th>Outgoing</th>
                                                <th style="min-width: 300px; width: 300px;">Routes</th>
                                                <th>Gender</th>
                                                <th>Position</th>
                                                <th>Division</th>
                                                <th>Department</th>
                                                <th>Section</th>
                                                <th>Added by</th>
                                                
                                            </tr>
                                        </thead>
                                    </table>
                                {{-- </div> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    
    <!-- Add Masterlist Modal Start -->
    <div class="modal fade" id="modalAddMasterlist" data-bs-keyboard="false" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><i class="fas fa-info-circle"></i>&nbsp;Add Masterlist</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" id="formAddMasterlist" autocomplete="off">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card-body">
                                    <!-- For Edit Masterlist Id -->
                                    <input type="text" class="form-control" style="display: none" name="masterlist_id" id="masterlistId">

                                    <!-- HRIS / Subcon Id-->
                                    <input type="text" class="form-control" style="display: none" name="systemone_id" id="systemoneId">
                                    
                                    <div class="mb-3">
                                        <label for="selectEmployeeType" class="form-label">Select Type<span class="text-danger" title="Required">*</span></label>
                                        <select class="form-select" id="selectEmployeeType" name="employee_type">
                                            <option value="0" selected disabled >Select One</option>
                                            <option value="1">Pricon</option>
                                            <option value="2">Subcon</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="selectEmployeeNumber" class="form-label">Select Employee<span class="text-danger" title="Required">*</span></label>
                                        <select class="form-select select2" id="selectEmployeeName" disabled name="employee_name">
                                            <!-- Auto Generated -->
                                            <option value="0" disabled selected>Select One</option>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="username" class="form-label">Employee #</label>
                                        <input type="text" class="form-control" readonly name="employee_number" id="textEmployeeNumber" placeholder="Employee #">
                                    </div>

                                    <div class="mb-3">
                                        <label for="username" class="form-label">Gender</label>
                                        <input type="text" class="form-control" readonly name="employee_gender" id="textGender" placeholder="Gender">
                                    </div>

                                    <div class="mb-3">
                                        <label for="username" class="form-label">Position</label>
                                        <input type="text" class="form-control" readonly name="employee_position" id="textPosition" placeholder="Position">
                                    </div>

                                    <div class="mb-3">
                                        <label for="username" class="form-label">Division</label>
                                        <input type="text" class="form-control" readonly name="employee_division" id="textDivision" placeholder="Division">
                                    </div>

                                    <div class="mb-3">
                                        <label for="username" class="form-label">Department</label>
                                        <input type="text" class="form-control" readonly name="employee_department" id="textDepartment" placeholder="Department">
                                    </div>

                                    <div class="mb-3">
                                        <label for="username" class="form-label">Section</label>
                                        <input type="text" class="form-control" readonly name="employee_section" id="textSection" placeholder="Section">
                                    </div>

                                    <div class="mb-3">
                                        <label for="selectEmployeeNumber" class="form-label">Select Routes<span class="text-danger" title="Required">*</span></label>
                                        <select class="form-select select2" id="selectRoutes" disabled name="routes_id">
                                            <!-- Auto Generated -->
                                            <option value="0" disabled selected>Select One</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <div class="row">
                                            <div class="col">
                                                <label for="username" class="form-label">Incoming</label>
                                                <input type="text" class="form-control datetimepicker" name="masterlist_incoming" id="textMasterlistIncoming" placeholder="Masterlist Capacity">
                                            </div>
                                            <div class="col">
                                                <label for="username" class="form-label">Outgoing</label>
                                                <input type="text" class="form-control datetimepicker" name="masterlist_outgoing" id="textMasterlistOutgoing" placeholder="Masterlist Capacity">
                                            </div>
                                        </div>
                                        
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
                        <button type="submit" id="btnAddMasterlist" class="btn btn-primary"><i id="iBtnAddMasterlistIcon" class="fa fa-check"></i> Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div><!-- Add Masterlist Modal End -->
    
    <!-- Edit Masterlist Status Modal Start -->
    <div class="modal fade" id="modalEditMasterlistStatus" data-bs-keyboard="false" data-bs-backdrop="static">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="editMasterlistStatusTitle"><i class="fas fa-info-circle"></i> Edit Masterlist Status</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" id="formEditMasterlistStatus" autocomplete="off">
                    @csrf
                    <div class="modal-body">
                        <p id="paragraphEditMasterlistStatus"></p>
                        <input type="hidden" name="masterlist_id" placeholder="Masterlist Id" id="textEditMasterlistStatusMasterlistId">
                        <input type="hidden" name="masterlist_status" placeholder="Masterlist Status" id="textEditMasterlistStatus">
                    </div>
                    
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
                        <button type="submit" id="buttonEditMasterlistStatus" class="btn btn-primary"><i id="iBtnAddMasterlistIcon" class="fa fa-check"></i> Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div><!-- Edit Masterlist Status Modal End -->

    <!-- Delete Masterlist Status Modal Start -->
    <div class="modal fade" id="modalDeleteMasterlistStatus" data-bs-keyboard="false" data-bs-backdrop="static">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="deleteMasterlistStatusTitle"><i class="fas fa-info-circle"></i> Delete Masterlist</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" id="formDeleteMasterlist" autocomplete="off">
                    @csrf
                    <div class="modal-body">
                        <p id="paragraphDeleteMasterlistStatus"></p>
                        <input type="hidden" name="masterlist_id" placeholder="Masterlist Id" id="textDeleteMasterlistStatusMasterlistId">
                        <input type="hidden" name="masterlist_is_deleted" placeholder="Masterlist Status" id="textDeleteMasterlistStatus">
                    </div>
                    
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
                        <button type="submit" id="buttonDeleteMasterlistStatus" class="btn btn-primary"><i id="iBtnAddMasterlistIcon" class="fa fa-check"></i> Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div><!-- Delete Masterlist Status Modal End -->
@endsection

<!--     {{-- JS CONTENT --}} -->
@section('js_content')
    <script type="text/javascript">
        $(document).ready(function () {
            let txtGlobalUserId = $('#txtGlobalUserId').val();
            console.log('txtGlobalUserId', txtGlobalUserId);
            
            /**
             * Initialize Select2 Elements
            */
            $('.select2').select2({
                theme: 'bootstrap-5',
                dropdownParent: $('#modalAddMasterlist')
            });

            /**
             * Disable typing in datetimepicker
            */
            $(".datetimepicker").keypress(function(event) {
                event.preventDefault();
            });

            $('#textMasterlistIncoming').datetimepicker({
                datepicker:false,
                formatTime: 'h:iA', // if input value is empty, timepicker set time use defaultTime
                allowTimes: [
                    '07:30',
                    '19:30',
                ],
                validateOnBlur:false, // Verify datetime value from input, when losing focus. If value is not valid datetime, then to value inserts the current datetime
                onSelectTime:function(ct,$input){
                    $('#textMasterlistIncoming').val(moment($input.val()).format('h:mmA'));
                },
            });

            $('#textMasterlistOutgoing').datetimepicker({
                datepicker:false,
                formatTime: 'h:iA', // if input value is empty, timepicker set time use defaultTime
                allowTimes: [
                    '07:30',
                    '15:30',
                    '16:30',
                    '19:30',
                ],
                validateOnBlur:false, // Verify datetime value from input, when losing focus. If value is not valid datetime, then to value inserts the current datetime
                onSelectTime:function(ct,$input){
                    $('#textMasterlistOutgoing').val(moment($input.val()).format('h:mmA'));
                },
            });

            $("select#selectEmployeeType").on('change',function(){
                // console.log('selectEmployeeType onchange');
                let selectedEmployeeType = $(this).children("option:selected").attr('value');
                $("input[name='masterlist_id']", $('#formAddMasterlist')).val('');
                $('#textEmployeeName').val('');
                $('#textEmployeeNumber').val('');
                $('#textGender').val('');
                $('#textPosition').val('');
                $('#textDivision').val('');
                $('#textDepartment').val('');
                $('#textSection').val('');
                $('#textMasterlistIncoming').val('');
                $('#textMasterlistOutgoing').val('');

                if(selectedEmployeeType != 0){
                    $('select#selectEmployeeName').prop('disabled', false);

                    getEmployees($('#selectEmployeeName'), selectedEmployeeType).then((response) => {
                        console.log('response ', response);
                    }).catch((error) => {
                        console.log('error ', error);
                    });

                    getRoutes($('#selectRoutes')).then((response) => {
                        console.log('response ', response);
                        $('select#selectRoutes').prop('disabled', false);
                    }).catch((error) => {
                        console.log('error ', error);
                    });
                }
            });

            $("select#selectEmployeeName").on('change',function(){
                let selectedSystemoneId = $(this).children("option:selected").attr('value');
                let selectedEmployeeNumber = $(this).children("option:selected").attr('employee-number');
                let selectedGender = $(this).children("option:selected").attr('gender');
                let selectedPosition = $(this).children("option:selected").attr('position');
                let selectedDivision = $(this).children("option:selected").attr('division');
                let selectedDepartment = $(this).children("option:selected").attr('department');
                let selectedSection = $(this).children("option:selected").attr('section');

                let gender = "";
                if(selectedGender == 1){
                    gender = "Male";
                }else{
                    gender = "Female";
                }

                $("input[name='systemone_id']", $('#formAddMasterlist')).val(selectedSystemoneId);
                $('#textEmployeeNumber').val(selectedEmployeeNumber);
                $('#textGender').val(gender);
                $('#textPosition').val(selectedPosition);
                $('#textDivision').val(selectedDivision);
                $('#textDepartment').val(selectedDepartment);
                $('#textSection').val(selectedSection);
            });

            dataTablesMasterlist = $("#tableMasterlist").DataTable({
                "processing" : false,
                "serverSide" : true,
                "responsive": true,
                // "order": [[ 0, "desc" ],[ 4, "desc" ]],
                "language": {
                    "info": "Showing _START_ to _END_ of _TOTAL_ employee records",
                    "lengthMenu": "Show _MENU_ employee records",
                },
                "pagingType": "full_numbers",
                lengthMenu: [10, 20, 50, 100, 200, 500, 1000, 1500, 2000],
                "ajax" : {
                    url: "view_masterlist",
                    data: function (param){
                        param.rapidXUserId = txtGlobalUserId;
                    },
                },
                "columns":[
                    { "data" : "action", orderable:false, searchable:false},
                    { "data" : "masterlist_status"},
                    { "data" : "masterlist_employee_number"},
                    { "data" : "masterlist_employee_name"},
                    { "data" : "masterlist_incoming"},
                    { "data" : "masterlist_outgoing"},
                    { "data" : "routes_info.routes_name"},
                    { "data" : "masterlist_employee_gender"},
                    { "data" : "masterlist_employee_position"},
                    { "data" : "masterlist_employee_division"},
                    { "data" : "masterlist_employee_department"},
                    { "data" : "masterlist_employee_section"},
                    { "data" : "rapidx_user_info.name"},
                ],
                "createdRow": function(row, data, index) {
                    $('td', row).eq(6).css('white-space', 'normal');
                    // console.log('row ', row);
                    // console.log('data ', data);
                    // console.log('index ', index);
                },
            });

            $("#formAddMasterlist").submit(function(event){
                event.preventDefault();
                addMasterlist();
            });

            $(document).on('click', '.actionEditMasterlist', function(){
                let id = $(this).attr('masterlist-id');
                // console.log('id ',id);
                $("input[name='masterlist_id'", $("#formAddMasterlist")).val(id);
                getMasterlistById(id);
            });

            $(document).on('click', '.actionEditMasterlistStatus', function(){
                let masterlistId = $(this).attr('masterlist-id');
                let masterlistStatus = $(this).attr('masterlist-status');
                console.log('masterlistId', masterlistId);
                console.log('masterlistStatus', masterlistStatus);
                
                $("#textEditMasterlistStatusMasterlistId").val(masterlistId);
                $("#textEditMasterlistStatus").val(masterlistStatus);

                if(masterlistStatus == 1){
                    $("#paragraphEditMasterlistStatus").text('Are you sure to deactivate?');
                }
                else{
                    $("#paragraphEditMasterlistStatus").text('Are you sure to activate?');
                }
            });

            $("#formEditMasterlistStatus").submit(function(event){
                event.preventDefault();
                editMasterlistStatus();
            });

            $(document).on('click', '.actionDeleteMasterlistStatus', function(){
                let masterlistId = $(this).attr('masterlist-id');
                let masterlistIsDeleted = $(this).attr('masterlist-is-deleted');
                console.log('masterlistId', masterlistId);
                console.log('masterlistIsDeleted', masterlistIsDeleted);
                
                $("#textDeleteMasterlistStatusMasterlistId").val(masterlistId);
                $("#textDeleteMasterlistStatus").val(masterlistIsDeleted);

                if(masterlistIsDeleted == 0){
                    $("#paragraphDeleteMasterlistStatus").text('Are you sure to delete?');
                }
            });

            $("#formDeleteMasterlist").submit(function(event){
                event.preventDefault();
                deleteMasterlist();
            });
        });
    </script>
@endsection

