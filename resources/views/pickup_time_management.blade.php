@extends('layouts.admin_layout')

@section('title', 'Dashboard')
@section('content_page')
    <div class="content-wrapper">
        <!-- Main content -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Pickup Time Management</h1>
                    </div>
                    <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Pickup Time Management</li>
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
                                <h3 class="card-title" style="margin-top: 8px;">Pickup Time Management</h3>
                            </div>
                            <div class="card-body">
                                <div class="text-right mt-4">                   
                                    <button type="button" class="btn btn-primary mb-3" id="buttonAddPickupTime" data-bs-toggle="modal" data-bs-target="#modalAddPickupTime"><i class="fa fa-plus fa-md"></i> New Pickup Time</button>
                                </div>
                                <div class="table-responsive">
                                    <table id="tablePickupTime" class="table table-bordered table-hover nowrap" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>Action</th>
                                                <th>Status</th>
                                                <th>Pickup Time</th>
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
    
    <!-- Add Pickup Time Modal Start -->
    <div class="modal fade" id="modalAddPickupTime" data-bs-keyboard="false" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><i class="fas fa-info-circle"></i>&nbsp;Add Pickup Time</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" id="formAddPickupTime" autocomplete="off">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card-body">
                                    <!-- For Pickup Time Id -->
                                    <input type="text" class="form-control" style="display: none" name="pickup_time_id" id="pickupTimeId">
                                    
                                    <div class="mb-3">
                                        <label for="username" class="form-label">Pickup Time</label>
                                        <input type="time" class="form-control" name="pickup_time" id="textPickupTime" placeholder="Pickup Time">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
                        <button type="submit" id="btnAddPickupTime" class="btn btn-primary"><i id="iBtnAddPickupTimeIcon" class="fa fa-check"></i> Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div><!-- Add Pickup Time Modal End -->
    
    <!-- Edit Pickup Time Status Modal Start -->
    <div class="modal fade" id="modalEditPickupTimeStatus" data-bs-keyboard="false" data-bs-backdrop="static">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="editPickupTimeStatusTitle"><i class="fas fa-info-circle"></i> Edit Pickup Time Status</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" id="formEditPickupTimeStatus" autocomplete="off">
                    @csrf
                    <div class="modal-body">
                        <p id="paragraphEditPickupTimeStatus"></p>
                        <input type="hidden" name="pickup_time_id" placeholder="Pickup Time Id" id="textEditPickupTimeStatusPickupTimeId">
                        <input type="hidden" name="pickup_time_status" placeholder="Pickup Time Status" id="textEditPickupTimeStatus">
                    </div>
                    
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
                        <button type="submit" id="buttonEditPickupTimeStatus" class="btn btn-primary"><i id="iBtnAddPickupTimeIcon" class="fa fa-check"></i> Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div><!-- Edit Pickup Time Status Modal End -->
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

            dataTablesPickupTime = $("#tablePickupTime").DataTable({
                "processing" : false,
                "serverSide" : true,
                "responsive": true,
                // "order": [[ 0, "desc" ],[ 4, "desc" ]],
                "language": {
                    "info": "Showing _START_ to _END_ of _TOTAL_ route records",
                    "lengthMenu": "Show _MENU_ pickup time records",
                },
                "ajax" : {
                    url: "view_pickup_time",
                },
                "columns":[
                    { "data" : "action", orderable:false, searchable:false},
                    { "data" : "pickup_time_status"},
                    { "data" : "pickup_time"},
                ],
            });

            $("#formAddPickupTime").submit(function(event){
                event.preventDefault();
                addPickupTime();
            });

            $(document).on('click', '.actionEditPickupTime', function(){
                let id = $(this).attr('pickup-time-id');
                console.log('id ',id);
                $("input[name='pickup_time_id'", $("#formAddPickupTime")).val(id);
                getPickupTimeById(id);
            });

            $(document).on('click', '.actionEditPickupTimeStatus', function(){
                let pickupTimeId = $(this).attr('pickup-time-id');
                let pickupTimeStatus = $(this).attr('pickup-time-status');
                console.log('pickupTimeId', pickupTimeId);
                console.log('pickupTimeStatus', pickupTimeStatus);
                
                $("#textEditPickupTimeStatusPickupTimeId").val(pickupTimeId);
                $("#textEditPickupTimeStatus").val(pickupTimeStatus);

                if(pickupTimeStatus == 1){
                    $("#paragraphEditPickupTimeStatus").text('Are you sure to deactivate?');
                }
                else{
                    $("#paragraphEditPickupTimeStatus").text('Are you sure to activate?');
                }
            });

            $("#formEditPickupTimeStatus").submit(function(event){
                event.preventDefault();
                editPickupTimeStatus();
            });
        });
    </script>
@endsection

