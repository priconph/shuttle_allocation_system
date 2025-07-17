@extends('layouts.admin_layout')

@section('title', 'Dashboard')
@section('content_page')
    <div class="content-wrapper">
        <!-- Main content -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Routes Management</h1>
                    </div>
                    <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Routes Management</li>
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
                                <h3 class="card-title" style="margin-top: 8px;">Routes Management</h3>
                                {{-- <button class="btn float-right reload"><i class="fas fa-sync-alt"></i></button> --}}
                            </div>
                            <div class="card-body">
                                <div class="text-right mt-4">                   
                                    <button type="button" class="btn btn-primary mb-3" id="buttonAddRoutes" data-bs-toggle="modal" data-bs-target="#modalAddRoutes"><i class="fa fa-plus fa-md"></i> New Route</button>
                                </div>
                                <div class="table-responsive">
                                    <table id="tableRoutes" class="table table-bordered table-hover nowrap" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>Action</th>
                                                <th>Status</th>
                                                <th>Route Name</th>
                                                <th>Route Description</th>
                                                <th>Pickup Time</th>
                                                <th>Shuttle Provider</th>
                                                <th>Shuttle Provider Capacity</th>
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
    
    <!-- Add Routes Modal Start -->
    <div class="modal fade" id="modalAddRoutes" data-bs-keyboard="false" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><i class="fas fa-info-circle"></i>&nbsp;Add Routes</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" id="formAddRoutes" autocomplete="off">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card-body">
                                    <!-- For Routes Id -->
                                    <input type="text" class="form-control" style="display: none" name="routes_id" id="routesId">
                                    
                                    <div class="mb-3">
                                        <label for="username" class="form-label">Route Name</label>
                                        <input type="text" class="form-control" name="routes_name" id="textRoutesName" placeholder="Route Name">
                                    </div>
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Route Description</label>
                                        <textarea type="text" class="form-control" name="routes_description" id="textRoutesDescription" placeholder="Route Description"></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="selectPickupTime" class="form-label">Pickup Time<span class="text-danger" title="Required">*</span></label>
                                        <select class="form-select select2" id="selectPickupTime" name="pickup_time_id">
                                            <!-- Auto Generated -->
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="selectShuttleProvider" class="form-label">Shuttle Provider<span class="text-danger" title="Required">*</span></label>
                                        <select class="form-select select2" id="selectShuttleProvider" name="shuttle_provider_id">
                                            <!-- Auto Generated -->
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
                        <button type="submit" id="btnAddRoutes" class="btn btn-primary"><i id="iBtnAddRoutesIcon" class="fa fa-check"></i> Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div><!-- Add Routes Modal End -->
    
    <!-- Edit Routes Status Modal Start -->
    <div class="modal fade" id="modalEditRoutesStatus" data-bs-keyboard="false" data-bs-backdrop="static">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="editRoutesStatusTitle"><i class="fas fa-info-circle"></i> Edit Routes Status</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" id="formEditRoutesStatus" autocomplete="off">
                    @csrf
                    <div class="modal-body">
                        <p id="paragraphEditRoutesStatus"></p>
                        <input type="hidden" name="routes_id" placeholder="Routes Id" id="textEditRoutesStatusRoutesId">
                        <input type="hidden" name="status" placeholder="Status" id="textEditRoutesStatus">
                    </div>
                    
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
                        <button type="submit" id="buttonEditRoutesStatus" class="btn btn-primary"><i id="iBtnAddRoutesIcon" class="fa fa-check"></i> Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div><!-- Edit Routes Status Modal End -->
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

            /**
             * Auto generated for select tag
            */
            getPickupTime($('#selectPickupTime'));
            getShuttleProvider($('#selectShuttleProvider'));

            dataTablesRoutes = $("#tableRoutes").DataTable({
                "processing" : false,
                "serverSide" : true,
                "responsive": true,
                // "order": [[ 0, "desc" ],[ 4, "desc" ]],
                "language": {
                    "info": "Showing _START_ to _END_ of _TOTAL_ route records",
                    "lengthMenu": "Show _MENU_ route records",
                },
                "ajax" : {
                    url: "view_routes",
                },
                "columns":[
                    { "data" : "action", orderable:false, searchable:false},
                    { "data" : "status"},
                    { "data" : "routes_name"},
                    { "data" : "routes_description"},
                    { "data" : "pickup_time_info"},
                    { "data" : "shuttle_provider_info.shuttle_provider_name"},
                    { "data" : "shuttle_provider_info.shuttle_provider_capacity"},
                ],
                "createdRow": function(row, data, index) {
                    $('td', row).eq(2).css('white-space', 'normal');
                    $('td', row).eq(3).css('white-space', 'normal');
                    // console.log('row ', row);
                    // console.log('data ', data);
                    // console.log('index ', index);
                },
            });

            $("#formAddRoutes").submit(function(event){
                event.preventDefault();
                addRoutes();
            }); 

            $(document).on('click', '.actionEditRoutes', function(){
                let id = $(this).attr('routes-id');
                console.log('id ',id);
                $("input[name='routes_id'", $("#formAddRoutes")).val(id);
                getRoutesById(id);
            });

            $(document).on('click', '.actionEditRoutesStatus', function(){
                let routesId = $(this).attr('routes-id');
                let routesStatus = $(this).attr('routes-status');
                console.log('routesId', routesId);
                console.log('routesStatus', routesStatus);
                
                $("#textEditRoutesStatusRoutesId").val(routesId);
                $("#textEditRoutesStatus").val(routesStatus);

                if(routesStatus == 1){
                    $("#paragraphEditRoutesStatus").text('Are you sure to deactivate?');
                }
                else{
                    $("#paragraphEditRoutesStatus").text('Are you sure to activate?');
                }
            });

            $("#formEditRoutesStatus").submit(function(event){
                event.preventDefault();
                editRoutesStatus();
            });
        });
    </script>
@endsection

