@extends('layouts.admin_layout')

@section('title', 'Dashboard')
@section('content_page')
    <div class="content-wrapper">
        <!-- Main content -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Shuttle Provider Management</h1>
                    </div>
                    <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Shuttle Provider Management</li>
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
                                <h3 class="card-title" style="margin-top: 8px;">Shuttle Provider Management</h3>
                                {{-- <button class="btn float-right reload"><i class="fas fa-sync-alt"></i></button> --}}
                            </div>
                            <div class="card-body">
                                <div class="text-right mt-4">                   
                                    <button type="button" class="btn btn-primary mb-3" id="buttonAddShuttleProvider" data-bs-toggle="modal" data-bs-target="#modalAddShuttleProvider"><i class="fa fa-plus fa-md"></i> New Shuttle Provider</button>
                                </div>
                                <div class="table-responsive">
                                    <table id="tableShuttleProvider" class="table table-bordered table-hover nowrap" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>Action</th>
                                                <th>Status</th>
                                                <th>Shuttle Provider Name</th>
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
    
    <!-- Add Shuttle Provider Modal Start -->
    <div class="modal fade" id="modalAddShuttleProvider" data-bs-keyboard="false" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><i class="fas fa-info-circle"></i>&nbsp;Add Shuttle Provider</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" id="formAddShuttleProvider" autocomplete="off">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card-body">
                                    <!-- For Shuttle Provider Id -->
                                    <input type="text" class="form-control" style="display: none" name="shuttle_provider_id" id="shuttleProviderId">
                                    
                                    <div class="mb-3">
                                        <label for="username" class="form-label">Shuttle Provider Name</label>
                                        <input type="text" class="form-control" name="shuttle_provider_name" id="textShuttleProviderName" placeholder="Shuttle Provider Name">
                                    </div>

                                    <div class="mb-3">
                                        <label for="username" class="form-label">Shuttle Provider Capacity</label>
                                        <input type="number" class="form-control" name="shuttle_provider_capacity" min="1" max="500" id="textShuttleProviderCapacity" placeholder="Shuttle Provider Capacity">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
                        <button type="submit" id="btnAddShuttleProvider" class="btn btn-primary"><i id="iBtnAddShuttleProviderIcon" class="fa fa-check"></i> Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div><!-- Add Shuttle Provider Modal End -->
    
    <!-- Edit Shuttle Provider Status Modal Start -->
    <div class="modal fade" id="modalEditShuttleProviderStatus" data-bs-keyboard="false" data-bs-backdrop="static">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="editShuttleProviderStatusTitle"><i class="fas fa-info-circle"></i> Edit Shuttle Provider Status</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" id="formEditShuttleProviderStatus" autocomplete="off">
                    @csrf
                    <div class="modal-body">
                        <p id="paragraphEditShuttleProviderStatus"></p>
                        <input type="hidden" name="shuttle_provider_id" placeholder="Shuttle Provider Id" id="textEditShuttleProviderStatusShuttleProviderId">
                        <input type="hidden" name="shuttle_provider_status" placeholder="Shuttle Provider Status" id="textEditShuttleProviderStatus">
                    </div>
                    
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
                        <button type="submit" id="buttonEditShuttleProviderStatus" class="btn btn-primary"><i id="iBtnAddShuttleProviderIcon" class="fa fa-check"></i> Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div><!-- Edit Shuttle Provider Status Modal End -->
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

            dataTablesShuttleProvider = $("#tableShuttleProvider").DataTable({
                "processing" : false,
                "serverSide" : true,
                "responsive": true,
                // "order": [[ 0, "desc" ],[ 4, "desc" ]],
                "language": {
                    "info": "Showing _START_ to _END_ of _TOTAL_ route records",
                    "lengthMenu": "Show _MENU_ shuttle provider records",
                },
                "ajax" : {
                    url: "view_shuttle_provider",
                },
                "columns":[
                    { "data" : "action", orderable:false, searchable:false},
                    { "data" : "shuttle_provider_status"},
                    { "data" : "shuttle_provider_name"},
                    { "data" : "shuttle_provider_capacity"},
                ],
            });

            $("#formAddShuttleProvider").submit(function(event){
                event.preventDefault();
                addShuttleProvider();
            });

            $(document).on('click', '.actionEditShuttleProvider', function(){
                let id = $(this).attr('shuttle-provider-id');
                console.log('id ',id);
                $("input[name='shuttle_provider_id'", $("#formAddShuttleProvider")).val(id);
                getShuttleProviderById(id);
            });

            $(document).on('click', '.actionEditShuttleProviderStatus', function(){
                let shuttleProviderId = $(this).attr('shuttle-provider-id');
                let shuttleProviderStatus = $(this).attr('shuttle-provider-status');
                console.log('shuttleProviderId', shuttleProviderId);
                console.log('shuttleProviderStatus', shuttleProviderStatus);
                
                $("#textEditShuttleProviderStatusShuttleProviderId").val(shuttleProviderId);
                $("#textEditShuttleProviderStatus").val(shuttleProviderStatus);

                if(shuttleProviderStatus == 1){
                    $("#paragraphEditShuttleProviderStatus").text('Are you sure to deactivate?');
                }
                else{
                    $("#paragraphEditShuttleProviderStatus").text('Are you sure to activate?');
                }
            });

            $("#formEditShuttleProviderStatus").submit(function(event){
                event.preventDefault();
                editShuttleProviderStatus();
            });
        });
    </script>
@endsection

