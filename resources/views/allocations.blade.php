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
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-sm-2">
                                        <label class="form-label">Filter Request Type</label>
                                        <div class="input-group input-group-sm mb-3">
                                            <select class="form-control select2bs5" id="filterRequestType">
                                                <option value="0" disabled selected>Select Category</option>
                                                <option value="1">Change Schedule</option>
                                                <option value="2">Not Riding Shuttle</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-sm-2">
                                        <label class="form-label">Filter Factory</label>
                                        <div class="input-group input-group-sm mb-3">
                                            <select class="form-control select2bs5" id="filterFactory">
                                                <option value="0" disabled selected>Select Factory</option>
                                                <option value="ALL">ALL</option>
                                                <option value="F1">F1</option>
                                                <option value="F3">F3</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <label class="form-label">Filter Allocation Date</label>
                                        <div class="container text-center">
                                            <div class="row align-items-start col-sm-12">
                                                <div class="col-sm-2">
                                                    From:
                                                </div>
                                                <div class="col-sm-4">
                                                    <input type="date" class="form-control" id="filterStartDate">
                                                </div>

                                                <div class="col-sm-1">
                                                    To:
                                                </div>
                                                <div class="col-sm-4">
                                                    <input type="date" class="form-control" id="filterEndDate">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="card" style="margin-bottom: 80px">
                            <div class="card-header">
                                <h3 class="card-title" style="margin-top: 8px;">Allocations</h3>
                            </div>
                            <div class="card-body">
                                <div class="text-right mt-4">
                                    <button type="button" class="btn btn-primary mb-3" id="btnAddAllocation" data-bs-toggle="modal" data-bs-target="#modalAddAllocation"><i class="fa fa-plus fa-md"></i> Request New Allocation</button>
                                </div>
                                <div class="table-responsive">
                                    <table id="tblAllocation" class="table-sm table-bordered table-hover nowrap" style="width:100%;">
                                        <thead>
                                            <tr>
                                                <th>Action</th>
                                                <th>Status</th>
                                                <th>Date Requested</th>
                                                <th>Type of Request</th>
                                                <th>Allocation Date</th>
                                                <th>Allocated Factory</th>
                                                <th>No. of Allocated Person</th>
                                                <th>Requested By</th>
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
                    <h4 class="modal-title" id="allocationRequestChangeTitle"></h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" id="formAddAllocation" autocomplete="off">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="mb-3" hidden>
                                <label for="allocation_id" class="form-label">View Mode</label>
                                <!-- For Edit Control No -->
                                <input type="text" class="form-control" name="is_view_mode" id="txtIsViewMode" value="0">
                            </div>

                            <div class="mb-3" hidden>
                                <label for="allocation_id" class="form-label">Allocation Control No</label>
                                <!-- For Edit Control No -->
                                <input type="text" class="form-control" name="request_control_no" id="txtRequestControlNo">
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
                                            <select class="form-control select2bs5" type="text" name="type_of_request" id="txtTypeOfRequest" required>
                                                <option value="0" disabled selected>Select Category</option>
                                                <option value="1">Change Schedule</option>
                                                <option value="2">Not Riding Shuttle</option>
                                            </select>
                                        </div>

                                        <div class="col-sm-6">
                                            <label for="alloc_factory" class="form-label">Allocate to Factory</label>
                                            <select class="form-control select2bs5" type="text" name="alloc_factory" id="txtAllocFactory">
                                                <option value="" disabled selected>Select Factory</option>
                                                {{-- <option value="ALL">ALL</option> --}}
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
                                                <option value="N/A" id="na_in_option">N/A</option>
                                                <option value="7:30AM">7:30 AM</option>
                                                <option value="7:30PM">7:30 PM</option>
                                            </select>
                                        </div>

                                        <div class="col-sm-6">
                                            <label for="alloc_outgoing" class="form-label">Allocate Outgoing</label>
                                            {{-- <input type="time" class="form-control" name="alloc_outgoing" id="txtAllocOutgoing"> --}}
                                            <select class="form-control form-control-sm select2bs5" type="text" name="alloc_outgoing" id="txtAllocOutgoing">
                                                <option value="" disabled selected>Select Incoming</option>
                                                <option value="N/A" id="na_out_option">N/A</option>
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
                                <label for="alloc_factory" class="form-label">Factory</label>
                                <select class="form-control form-control-sm select2bs5 selectAllocFactory" type="text">
                                    <option value="" disabled selected>Select Factory</option>
                                    {{-- <option value="ALL">ALL</option> --}}
                                    <option value="F1">Factory 1</option>
                                    <option value="F3">Factory 3</option>
                                </select>
                            </div>

                            <div class="col-sm-4">
                                <label for="alloc_department" class="form-label">Department</label>
                                <select class="form-control form-control-sm select2bs5 selectAllocDepartment" type="text">
                                    <option value="" disabled selected>Select Department</option>
                                </select>
                            </div>

                            <div class="col-sm-4">
                                <label for="alloc_section" class="form-label">Section</label>
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
                                            <th rowspan="2" style="width: 5%;">
                                                <div id="actionCheckAllTheadDiv" class="d-none">
                                                    <center><input type="checkbox" style="width: 25px; height: 25px;" name="check_all" id="chkAllItems"></center>
                                                </div>
                                                <div id="actionTextTheadDiv" class="d-none">
                                                    <center>REMOVE</center>
                                                </div>
                                            </th>
                                            <th rowspan="2" style="width: 5%;">E.N</th>
                                            <th rowspan="2" style="width: 15%;">Name</th>
                                            <th rowspan="2" style="width: 10%;">Department</th>
                                            <th rowspan="2" style="width: 10%;">Section</th>
                                            <th rowspan="2" style="width: 30%;">Route</th>
                                            <th colspan="3" style="width: 20%; text-align: center; background-color: #4baeff;">CURRENT DATA</th>
                                        </tr>
                                        <tr class="bg-light " style="text-align: center;">
                                            <th>Factory</th>
                                            <th>Incoming</th>
                                            <th>Outgoing</th>
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

    <!-- Delete request MODAL START -->
        <div class="modal fade" id="modalDeleteRequest">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-gradient" id="changeStatusChangeDivModalHeader">
                        <h4 class="modal-title" id="changeStatusChangeTitle"></h4>
                        <button type="button" style="color: #fff" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form method="post" id="FrmChangeStatusAllocation">
                        @csrf
                        <div class="modal-body">
                            <div class="row d-flex justify-content-center">
                                <label class="text-secondary mt-2" id="changeStatusChangeLabel"></label>
                                <input type="hidden" class="form-control" name="delete_control_no" id="deleteFrmControlNumber">
                                <input type="hidden" class="form-control" name="delete_request_status" id="deleteFrmRequestStatus">
                            </div>
                        </div>
                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
                            <button type="submit" id="btnDeleteRequest" class="btn"></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Delete request MODAL END -->

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
    </script>
@endsection

