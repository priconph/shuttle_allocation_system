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
                        <h1>Allocations</h1>
                    </div>
                    <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Allocations</li>
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
                                <h3 class="card-title" style="margin-top: 8px;">Allocations</h3>
                                {{-- <button class="btn float-right reload"><i class="fas fa-sync-alt"></i></button> --}}
                            </div>
                            <div class="card-body">
                                <div class="text-right mt-4">
                                    <button type="button" class="btn btn-primary mb-3" id="btnAddAllocation" data-bs-toggle="modal" data-bs-target="#modalAddAllocation"><i class="fa fa-plus fa-md"></i> Request New Allocation</button>
                                </div>
                                <div class="table-responsive">
                                    <table id="tblAllocation" class="table-sm table-bordered table-hover nowrap" style="width:100%;">
                                        <thead>
                                            <tr>
                                                <th rowspan="2">Action</th>
                                                <th rowspan="2">Status</th>
                                                <th rowspan="2">Allocation Date</th>
                                                <th rowspan="2">Emp No.</th>
                                                <th rowspan="2">Name</th>
                                                <th rowspan="2">Routes</th>
                                                <th colspan="3" style="text-align: center; background-color: #d9edf7;">Original</th>
                                                <th colspan="3" style="text-align: center; background-color: #dff0d8;">Request</th>
                                                <th rowspan="2">Date Requested</th>
                                                <th rowspan="2">Requested By</th>
                                            </tr>
                                            <tr>
                                                <th>Factory</th>
                                                <th>Incoming</th>
                                                <th>Outgoing</th>
                                                <th>Factory</th>
                                                <th>Incoming</th>
                                                <th>Outgoing</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Add Masterlist Modal Start -->
    <div class="modal fade" id="modalAddAllocation" data-bs-keyboard="false" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-lg-custom">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><i class="fas fa-info-circle"></i>&nbsp;Add Employee/s Shuttle Allocation</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" id="formAddAllocation" autocomplete="off">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="mb-3" hidden>
                                <label for="allocation_id" class="form-label">Allocation ID</label>
                                <!-- For Edit Masterlist Id -->
                                <input type="text" class="form-control" name="allocation_id" id="txtAllocationId">
                            </div>

                            <div class="mb-3" hidden>
                                <label for="requestor_id" class="form-label">Requestor ID</label>
                                <input type="text" class="form-control" name="requestor_id" id="txtRequestorId">
                            </div>

                            <div class="col-sm-3">
                                <div class="mb-3">
                                     <label for="requestor" class="form-label">Date Requested</label>
                                    <input type="date" class="form-control" name="date_requested" id="txtDateRequested" readonly value="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="mb-3">
                                    <label for="employee_number" class="form-label">Employee #</label>
                                    <input type="text" class="form-control" name="employee_number" id="txtEmployeeNumber" placeholder="Employee #" readonly>
                                </div>

                                <div class="mb-3">
                                    <label for="requestor" class="form-label">Name of Requestor</label>
                                    <input type="text" class="form-control" name="requestor" id="txtRequestor" readonly>
                                </div>

                                <div class="mb-3">
                                    <label for="department" class="form-label">Department & Section</label>
                                    <input type="text" class="form-control" name="department_section" id="txtDepartmentSection" readonly>
                                </div>
                            </div>
                            <div class="col-sm-8">
                                <div class="mb-3">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <label for="type_of_request" class="form-label">Type of Request</label>
                                            <select class="form-control select2bs5" type="text" name="type_of_request" id="txtTypeOfRequest">
                                                <option value="" disabled selected>Select Category</option>
                                                <option value="1">Change Schedule</option>
                                                <option value="2">Shutdown</option>
                                                <option value="3">Undertime</option>
                                                <option value="4">Others</option>
                                            </select>
                                        </div>

                                        <div class="col-sm-6">
                                            <label for="alloc_factory" class="form-label">Allocate to Factory</label>
                                            <select class="form-control select2bs5" type="text" name="alloc_factory" id="txtAllocFactory">
                                                <option value="" disabled selected>Select Factory</option>
                                                <option value="F1">Factory 1</option>
                                                <option value="F3">Factory 3</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <label for="alloc_incoming" class="form-label">Allocate Incoming</label>
                                            {{-- <input type="time" class="form-control" name="alloc_incoming" id="txtAllocIncoming"> --}}
                                            <select class="form-control form-control-sm select2bs5" type="text" name="alloc_incoming" id="txtAllocIncoming">
                                                <option value="" disabled selected>Select Incoming</option>
                                                <option value="7:30AM">7:30 AM</option>
                                                <option value="7:30PM">7:30 PM</option>
                                            </select>
                                        </div>

                                        <div class="col-sm-6">
                                            <label for="alloc_outgoing" class="form-label">Allocate Outgoing</label>
                                            {{-- <input type="time" class="form-control" name="alloc_outgoing" id="txtAllocOutgoing"> --}}
                                            <select class="form-control form-control-sm select2bs5" type="text" name="alloc_outgoing" id="txtAllocOutgoing">
                                                <option value="" disabled selected>Select Incoming</option>
                                                <option value="7:30AM">7:30 AM</option>
                                                <option value="3:30PM">3:30 PM</option>
                                                <option value="4:30PM">4:30 PM</option>
                                                <option value="7:30PM">7:30 PM</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <label for="start_date" class="form-label">Date Start</label>
                                            <input type="date" class="form-control" name="start_date" id="txtStartDate">
                                        </div>

                                        <div class="col-sm-6">
                                            <label for="end_date" class="form-label">Date End</label>
                                            <input type="date" class="form-control" name="end_date" id="txtEndDate">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- <div class="row mb-3">
                            <div class="col-sm-12">
                                <hr>
                                    <h3 class="form-label text-center" style="margin-top: 8px;">Masterlist Data</h3>
                                <hr>
                            </div>
                        </div> --}}
                        <hr>
                        <div class="row mb-3">
                            <div class="col-sm-4">
                                <label for="alloc_factory" class="form-label">Filter Factory</label>
                                <select class="form-control form-control-sm select2bs5 selectAllocFactory" type="text">
                                    <option value="" disabled selected>Select Factory</option>
                                    <option value="">ALL</option>
                                    <option value="F1">Factory 1</option>
                                    <option value="F3">Factory 3</option>
                                </select>
                            </div>

                            <div class="col-sm-4">
                                <label for="alloc_department" class="form-label">Filter Department</label>
                                <select class="form-control form-control-sm select2bs5 selectAllocDepartment" type="text">
                                    <option value="" disabled selected>Select Department</option>
                                </select>
                            </div>

                            <div class="col-sm-4">
                                <label for="alloc_section" class="form-label">Filter Section</label>
                                <select class="form-control form-control-sm select2bs5 selectAllocSection" type="text">
                                    <option value="" disabled selected>Select Section</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="table-responsive">
                                <table class="table-sm table-bordered table-hover nowrap small" id="tblMasterListToAlloc" style="width: 100%;">
                                    <thead id="divForTblMasterListToAllocThead">
                                        <tr class="bg-light" style="text-align: center;">
                                            <th rowspan="2" style="width: 10%;"><center><input type="checkbox" style="width: 25px; height: 25px;" name="check_all" id="chkAllItems"></center></th>
                                            <th rowspan="2" style="">E.N</th>
                                            <th rowspan="2" style="">Name</th>
                                            <th rowspan="2" style="">Department</th>
                                            <th rowspan="2" style="">Section</th>
                                            <th rowspan="2" style="">Route</th>
                                            <th colspan="3" style="text-align: center; background-color: #4baeff;">CURRENT DATA</th>
                                        </tr>
                                        <tr class="bg-light " style="text-align: center; width: 100%;">
                                            <th style="width: 20%;">Factory</th>
                                            <th style="width: 40%;">Incoming</th>
                                            <th style="width: 40%;">Outgoing</th>
                                        </tr>
                                    </thead>
                                    <tbody id="divForTblMasterListToAllocTbody">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
                        <button type="button" id="btnSaveNewAllocation" class="btn btn-primary"><i id="iBtnAddAllocationIcon" class="fa fa-check"></i> Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div><!-- Add Masterlist Modal End -->

    <!-- Edit Masterlist Status Modal Start -->
    {{-- <div class="modal fade" id="modalEditAllocationStatus" data-bs-keyboard="false" data-bs-backdrop="static">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="editAllocationStatusTitle"><i class="fas fa-info-circle"></i> Edit Masterlist Status</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" id="formEditAllocationStatus" autocomplete="off">
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
    </div> --}}
    <!-- Edit Masterlist Status Modal End -->

    <!-- Delete Masterlist Status Modal Start -->
    {{-- <div class="modal fade" id="modalDeleteMasterlistStatus" data-bs-keyboard="false" data-bs-backdrop="static">
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
    </div> --}}
    <!-- Delete Masterlist Status Modal End -->
@endsection

<!--     {{-- JS CONTENT --}} -->
@section('js_content')
    <script type="text/javascript">
        // $(document).ready(function () {
        //     let txtGlobalUserId = $('#txtGlobalUserId').val();
        //     console.log('txtGlobalUserId', txtGlobalUserId);

        //     /**
        //      * Initialize Select2 Elements
        //     */
        //     $('.select2').select2({
        //         theme: 'bootstrap-5',
        //         dropdownParent: $('#modalAddMasterlist')
        //     });

        //     /**
        //      * Disable typing in datetimepicker
        //     */
        //     $(".datetimepicker").keypress(function(event) {
        //         event.preventDefault();
        //     });

        //     $('#textMasterlistIncoming').datetimepicker({
        //         datepicker:false,
        //         formatTime: 'h:iA', // if input value is empty, timepicker set time use defaultTime
        //         allowTimes: [
        //             '07:30',
        //             '19:30',
        //         ],
        //         validateOnBlur:false, // Verify datetime value from input, when losing focus. If value is not valid datetime, then to value inserts the current datetime
        //         onSelectTime:function(ct,$input){
        //             $('#textMasterlistIncoming').val(moment($input.val()).format('h:mmA'));
        //         },
        //     });

        //     $('#textMasterlistOutgoing').datetimepicker({
        //         datepicker:false,
        //         formatTime: 'h:iA', // if input value is empty, timepicker set time use defaultTime
        //         allowTimes: [
        //             '07:30',
        //             '15:30',
        //             '16:30',
        //             '19:30',
        //         ],
        //         validateOnBlur:false, // Verify datetime value from input, when losing focus. If value is not valid datetime, then to value inserts the current datetime
        //         onSelectTime:function(ct,$input){
        //             $('#textMasterlistOutgoing').val(moment($input.val()).format('h:mmA'));
        //         },
        //     });

        //     $("select#selectEmployeeType").on('change',function(){
        //         // console.log('selectEmployeeType onchange');
        //         let selectedEmployeeType = $(this).children("option:selected").attr('value');
        //         $("input[name='masterlist_id']", $('#formAddMasterlist')).val('');
        //         $('#textEmployeeName').val('');
        //         $('#textEmployeeNumber').val('');
        //         $('#textGender').val('');
        //         $('#textPosition').val('');
        //         $('#textDivision').val('');
        //         $('#textDepartment').val('');
        //         $('#textSection').val('');
        //         $('#textMasterlistIncoming').val('');
        //         $('#textMasterlistOutgoing').val('');

        //         if(selectedEmployeeType != 0){
        //             $('select#selectEmployeeName').prop('disabled', false);

        //             getEmployees($('#selectEmployeeName'), selectedEmployeeType).then((response) => {
        //                 console.log('response ', response);
        //             }).catch((error) => {
        //                 console.log('error ', error);
        //             });

        //             getRoutes($('#selectRoutes')).then((response) => {
        //                 console.log('response ', response);
        //                 $('select#selectRoutes').prop('disabled', false);
        //             }).catch((error) => {
        //                 console.log('error ', error);
        //             });
        //         }
        //     });

        //     $("select#selectEmployeeName").on('change',function(){
        //         let selectedSystemoneId = $(this).children("option:selected").attr('value');
        //         let selectedEmployeeNumber = $(this).children("option:selected").attr('employee-number');
        //         let selectedGender = $(this).children("option:selected").attr('gender');
        //         let selectedPosition = $(this).children("option:selected").attr('position');
        //         let selectedDivision = $(this).children("option:selected").attr('division');
        //         let selectedDepartment = $(this).children("option:selected").attr('department');
        //         let selectedSection = $(this).children("option:selected").attr('section');

        //         let gender = "";
        //         if(selectedGender == 1){
        //             gender = "Male";
        //         }else{
        //             gender = "Female";
        //         }

        //         $("input[name='systemone_id']", $('#formAddMasterlist')).val(selectedSystemoneId);
        //         $('#textEmployeeNumber').val(selectedEmployeeNumber);
        //         $('#textGender').val(gender);
        //         $('#textPosition').val(selectedPosition);
        //         $('#textDivision').val(selectedDivision);
        //         $('#textDepartment').val(selectedDepartment);
        //         $('#textSection').val(selectedSection);
        //     });

        //     $("#formAddMasterlist").submit(function(event){
        //         event.preventDefault();
        //         addMasterlist();
        //     });

        //     $(document).on('click', '.actionEditMasterlist', function(){
        //         let id = $(this).attr('masterlist-id');
        //         // console.log('id ',id);
        //         $("input[name='masterlist_id'", $("#formAddMasterlist")).val(id);
        //         getMasterlistById(id);
        //     });

        //     $(document).on('click', '.actionEditMasterlistStatus', function(){
        //         let masterlistId = $(this).attr('masterlist-id');
        //         let masterlistStatus = $(this).attr('masterlist-status');
        //         console.log('masterlistId', masterlistId);
        //         console.log('masterlistStatus', masterlistStatus);

        //         $("#textEditMasterlistStatusMasterlistId").val(masterlistId);
        //         $("#textEditMasterlistStatus").val(masterlistStatus);

        //         if(masterlistStatus == 1){
        //             $("#paragraphEditMasterlistStatus").text('Are you sure to deactivate?');
        //         }
        //         else{
        //             $("#paragraphEditMasterlistStatus").text('Are you sure to activate?');
        //         }
        //     });

        //     $("#formEditMasterlistStatus").submit(function(event){
        //         event.preventDefault();
        //         editMasterlistStatus();
        //     });

        //     $(document).on('click', '.actionDeleteMasterlistStatus', function(){
        //         let masterlistId = $(this).attr('masterlist-id');
        //         let masterlistIsDeleted = $(this).attr('masterlist-is-deleted');
        //         console.log('masterlistId', masterlistId);
        //         console.log('masterlistIsDeleted', masterlistIsDeleted);

        //         $("#textDeleteMasterlistStatusMasterlistId").val(masterlistId);
        //         $("#textDeleteMasterlistStatus").val(masterlistIsDeleted);

        //         if(masterlistIsDeleted == 0){
        //             $("#paragraphDeleteMasterlistStatus").text('Are you sure to delete?');
        //         }
        //     });

        //     $("#formDeleteMasterlist").submit(function(event){
        //         event.preventDefault();
        //         deleteMasterlist();
        //     });
        // });
    </script>
@endsection

