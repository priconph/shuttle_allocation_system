@extends('layouts.admin_layout')

@section('title', 'Dashboard')
@section('content_page')
    <div class="content-wrapper">
        <!-- Main content -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Cutoff Time Management</h1>
                    </div>
                    <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Cutoff Time Management</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title" style="margin-top: 8px;">Cutoff Time Management</h3>
                            </div>
                            <div class="card-body">
                                <div class="text-right mt-4">
                                    <button type="button" class="btn btn-primary mb-3" id="buttonAddCutoffTime" data-bs-toggle="modal" data-bs-target="#modalAddCutoffTime"><i class="fa fa-plus fa-md"></i> New Cutoff Time</button>
                                </div>
                                <div class="table-responsive">
                                    <table id="tableCutoffTime" class="table table-sm table-bordered table-hover nowrap" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>Factory</th>
                                                <th>Schedule</th>
                                                <th>Time</th>
                                                <th>Today</th>
                                                <th>Upcoming Days</th>
                                                <!-- <th>Action</th> -->
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

    <!-- Add Cutoff Time Modal Start -->
    <div class="modal fade" id="modalAddCutoffTime" data-bs-keyboard="false" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><i class="fas fa-info-circle"></i>&nbsp;Add Cutoff Time</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" id="formAddCutoffTime" autocomplete="off">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card-body">
                                    <!-- For Cutoff Time Id -->
                                    <input type="text" class="form-control" style="display: none" name="cutoff_time_id" id="cutoffTimeId">

                                    <div class="mb-3">
                                        <div class="row">
                                            <label>Factory</label>
                                            <div class="input-group input-group-sm mb-3">
                                                <select class="form-control select2bs5" name="factory" id="txtFactory">
                                                    <option value="0" disabled selected>Select Factory</option>
                                                    <option value="1">F1</option>
                                                    <option value="3">F3</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <div class="row">
                                            <label>Schedule</label>
                                            <div class="input-group input-group-sm mb-3">
                                                <select class="form-control select2bs5" name="category" id="txtCategory">
                                                    <option value="0" disabled selected>Select Schedule (IN/OUT)</option>
                                                    <option value="1">Outgoing</option>
                                                    <option value="2">Incoming</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label>Time</label>
                                        <div class="input-group input-group-sm mb-3">
                                            <select class="form-control select2bs5" name="schedule" id="txtSchedule">
                                                <option value="0" disabled selected>Select Time</option>
                                                <option value="6:00AM">6:00AM</option>
                                                <option value="7:30AM">7:30AM</option>
                                                <option value="2:00PM">2:00PM</option>
                                                <option value="3:30PM">3:30PM</option>
                                                <option value="4:30PM">4:30PM</option>
                                                <option value="7:30PM">7:30PM</option>
                                                <option value="10:00PM">10:00PM</option>
                                            </select>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
                        <button type="submit" id="btnAddCutoffTime" class="btn btn-primary"><i id="iBtnAddCutoffTimeIcon" class="fa fa-check"></i> Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div><!-- Add Cutoff Time Modal End -->

    <!-- Edit Cutoff Time Status Modal Start -->
    <div class="modal fade" id="modalEditCutoffTimeStatus" data-bs-keyboard="false" data-bs-backdrop="static">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="editCutoffTimeStatusTitle"><i class="fas fa-info-circle"></i> Schedule change confirmation</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" id="formEditCutoffTimeStatus" autocomplete="off">
                    @csrf
                    <div class="modal-body text-center">
                        <h4 id="paragraphEditCutoffTimeStatus"></h4>
                        <input type="hidden" name="cutoff_time_id" placeholder="Cutoff Time Id" id="textEditCutoffTimeStatusCutoffTimeId">
                        <input type="hidden" name="status" placeholder="Cutoff Time Status" id="textEditCutoffTimeStatus">
                        <input type="hidden" name="cutoff_time_type" placeholder="Cutoff Time Type" id="textEditCutoffTimeType">
                    </div>

                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
                        <button type="submit" id="buttonEditCutoffTimeStatus" class="btn btn-primary"><i id="iBtnAddCutoffTimeIcon" class="fa fa-check"></i> Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div><!-- Edit Cutoff Time Status Modal End -->
@endsection

<!--     {{-- JS CONTENT --}} -->
@section('js_content')
    <script type="text/javascript">
        $(document).ready(function () {
            /**
             * Initialize Select2 Elements
            */
            $('.select2bs5').select2({
                // width: '100%',
                theme: 'bootstrap-5'
            });

            dataTablesCutoffTime = $("#tableCutoffTime").DataTable({
                "processing" : false,
                "serverSide" : true,
                "responsive": true,
                // "order": [[ 0, "desc" ],[ 4, "desc" ]],
                "language": {
                    "info": "Showing _START_ to _END_ of _TOTAL_ route records",
                    "lengthMenu": "Show _MENU_ cutoff-time records",
                },
                "ajax" : {
                    url: "view_cutoff_time",
                },
                "columns":[
                    { "data" : "factory",
                        "defaultContent": 'N/A',
                        "orderable": true,
                        "searchable": true,
                        "width": "15%",
                        "render": function (data, type, row) {
                            if(row.factory == 1){
                                return "<center><span class='badge bg-primary px-3 py-2'><i class='fas fa-industry me-1'></i>Factory 1</span></center>";
                            }else{
                                return "<center><span class='badge bg-secondary px-3 py-2'><i class='fas fa-industry me-1'></i>Factory 3</span></center>";
                            }
                        },
                    },
                    { "data" : "category",
                        "defaultContent": 'N/A',
                        "width": "15%",
                        "render": function (data, type, row) {
                            if(row.category == 1){
                                return "<center><span class='badge bg-warning text-dark'><i class='fas fa-arrow-up'></i> Outgoing</span></center>";
                            }else if(row.category == 2){
                                return "<center><span class='badge bg-info'><i class='fas fa-arrow-down'></i> Incoming</span></center>";
                            }else{
                                return "---";
                            }
                        },
                    },
                    { "data" : "schedule",
                        "defaultContent": 'N/A',
                        "width": "15%",
                        "render": function (data, type, row) {
                            return "<center><i class='far fa-clock text-secondary me-1'></i>" + row.schedule +"</center>";
                        },
                     },
                    { "data" : "today_label", width: "20%"},
                    { "data" : "succeeding_label", width: "20%"},
                    // { "data" : "action", orderable:false, searchable:false, width: "15%"},
                    // { "data" : "status", width: "20%"},
                    // Old code clark commented out 11/20/2025
                    // { "data" : "schedule",
                    //     "defaultContent": 'N/A',
                    //     "render": function (data, type, row) {
                    //         if(row.schedule == '3:30PM'){
                    //             return "3:30PM";
                    //         }else if(row.schedule == '4:30PM'){
                    //             return "4:30PM";
                    //         }else if(row.schedule == '7:30PM'){
                    //             return "INCOMING & 7:30PM";
                    //         }else if(row.schedule == '7:30AM'){
                    //             return "INCOMING & 7:30AM";
                    //         }else{
                    //             return "---";
                    //         }
                    //     },
                    // },
                ],
            });

            $("#formAddCutoffTime").submit(function(event){
                event.preventDefault();
                addCutoffTime();
            });

            $('#modalAddCutoffTime').on('hidden.bs.modal', function (e){
                let form = $(this).find('form');
                    form.find('#txtFactory').val(0).trigger('change'); // reset
                    form.find('#txtSchedule').val(0).trigger('change'); // reset
            });

            $(document).on('click', '.actionEditCutoffTime', function(){
                let id = $(this).attr('pickup-time-id');
                $("input[name='cutoff_time_id'", $("#formAddCutoffTime")).val(id);
                getCutoffTimeById(id);
            });

            $(document).on('click', '.actionEditCutoffTimeStatus', function(){
                let cutoffTimeId = $(this).attr('pickup-time-id');
                let cutoffTimeStatus = $(this).attr('pickup-time-status');
                let cutoffTimeType = $(this).attr('pickup-time-type');

                $("#textEditCutoffTimeStatusCutoffTimeId").val(cutoffTimeId);
                $("#textEditCutoffTimeStatus").val(cutoffTimeStatus);
                $("#textEditCutoffTimeType").val(cutoffTimeType);

                if(cutoffTimeStatus == 1){
                    $("#paragraphEditCutoffTimeStatus").text('CLOSE This Schedule?');
                }
                else{
                    $("#paragraphEditCutoffTimeStatus").text('OPEN This Schedule?');
                }
            });

            $("#formEditCutoffTimeStatus").submit(function(event){
                event.preventDefault();
                editCutoffTimeStatus();
            });
        });
    </script>
@endsection

