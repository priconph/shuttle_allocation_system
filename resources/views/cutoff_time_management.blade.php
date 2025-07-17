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
                                    {{-- <button type="button" class="btn btn-primary mb-3" id="buttonAddCutoffTime" data-bs-toggle="modal" data-bs-target="#modalAddCutoffTime"><i class="fa fa-plus fa-md"></i> New Cutoff Time</button> --}}
                                </div>
                                <div class="table-responsive">
                                    <table id="tableCutoffTime" class="table table-bordered table-hover nowrap" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>Action</th>
                                                <th>Status</th>
                                                {{-- <th>Cutoff Time</th> --}}
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
                                        <label for="username" class="form-label">Cutoff Time</label>
                                        <input type="time" class="form-control" name="cutoff_time" id="textCutoffTime" placeholder="Cutoff Time">
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
                    <h4 class="modal-title" id="editCutoffTimeStatusTitle"><i class="fas fa-info-circle"></i> Lock Masterlist</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" id="formEditCutoffTimeStatus" autocomplete="off">
                    @csrf
                    <div class="modal-body">
                        <p id="paragraphEditCutoffTimeStatus"></p>
                        <input type="hidden" name="cutoff_time_id" placeholder="Cutoff Time Id" id="textEditCutoffTimeStatusCutoffTimeId">
                        <input type="hidden" name="cutoff_time_status" placeholder="Cutoff Time Status" id="textEditCutoffTimeStatus">
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
            $('.select2').select2({
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
                    { "data" : "action", orderable:false, searchable:false},
                    { "data" : "cutoff_time_status"},
                    // { "data" : "cutoff_time"},
                ],
            });

            $("#formAddCutoffTime").submit(function(event){
                event.preventDefault();
                addCutoffTime();
            });

            $(document).on('click', '.actionEditCutoffTime', function(){
                let id = $(this).attr('pickup-time-id');
                console.log('id ',id);
                $("input[name='cutoff_time_id'", $("#formAddCutoffTime")).val(id);
                getCutoffTimeById(id);
            });

            $(document).on('click', '.actionEditCutoffTimeStatus', function(){
                let cutoffTimeId = $(this).attr('pickup-time-id');
                let cutoffTimeStatus = $(this).attr('pickup-time-status');
                console.log('cutoffTimeId', cutoffTimeId);
                console.log('cutoffTimeStatus', cutoffTimeStatus);
                
                $("#textEditCutoffTimeStatusCutoffTimeId").val(cutoffTimeId);
                $("#textEditCutoffTimeStatus").val(cutoffTimeStatus);

                if(cutoffTimeStatus == 1){
                    $("#paragraphEditCutoffTimeStatus").text('Are you sure to lock masterlist?');
                }
                else{
                    $("#paragraphEditCutoffTimeStatus").text('Are you sure to unlock masterlist?');
                }
            });

            $("#formEditCutoffTimeStatus").submit(function(event){
                event.preventDefault();
                editCutoffTimeStatus();
            });
        });
    </script>
@endsection

