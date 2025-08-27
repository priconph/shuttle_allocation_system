@extends('layouts.admin_layout')

@section('title', 'Dashboard')
@section('content_page')
    <style>
        .card-header .nav-tabs {
            margin-bottom: -0.75rem; /* adjust until tabs touch */
        }
        .card-header .nav-item .nav-link {
            border-top-left-radius: 0;
            border-top-right-radius: 0;
        }
    </style>
    <div class="content-wrapper">
        <!-- Main content -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Manifest</h1>
                    </div>
                    <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Manifest</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <div class="card"> 
                    <div class="card-header d-flex">
                        <div >
                           <ul class="nav nav-tabs" id="manifestTab" role="tablist">
                               <li class="nav-item" role="">
                                   <button class="nav-link active" id="allManifestTab" data-bs-toggle="pill"
                                       data-bs-target="#allImportTab" type="button" role="tab" aria-controls="allImportTab"
                                       aria-selected="true">All Data</button>
                               </li>
                               <li class="nav-item" role="">
                                   <button class="nav-link" id="inconsistentTab" data-bs-toggle="pill"
                                       data-bs-target="#inconsistent" type="button" role="tab"
                                       aria-controls="inconsistent" aria-selected="false">Inconsistent Data</button>
                               </li>
                           </ul>
                        </div>
                        <div class="ms-auto">
                            <button class="btn btn-primary btn-sm" title="Import Manifest" id="btnImportManifest"
                            data-bs-toggle="modal" data-bs-target="#modalImportManifest">Import Shuttle Manifest</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="myTabContent">
                           <div class="tab-pane fade show active" id="allImportTab" role="tabpanel" aria-labelledby="allManifestTab">
                                 <div class="row">
                                    <div class="col-sm-12">
                                        <div class="row mb-2">
                                            <div class="col-sm-2">
                                                <input type="date" class="form-control filterList" value="{{ now()->format('Y-m-d') }}" name="" id="txtDateScanFilter">
                                            </div>
                                            <div class="col-sm-2">
                                                <select name="" id="selRouteFilter" class="form-control filterList"></select>
                                            </div>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-hover table-striped w-100" id="tableManifest">
                                                <thead>
                                                    <tr>
                                                        <th>Emp no.</th>
                                                        <th>Date Scanned</th>
                                                        <th>Time Scanned</th>
                                                        <th>Route</th>
                                                        <th>Factory</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="inconsistent" role="tabpanel" aria-labelledby="inconsistent">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="row mb-2">
                                            <div class="col-sm-3">
                                                <input type="date" class="form-control filterListInconsistent" value="{{ now()->format('Y-m-d') }}" name="" id="txtDateInconsistent">
                                            </div>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-hover table-striped w-100" id="tableInconsistent">
                                                <thead>
                                                    <tr>
                                                        <th>Emp no.</th>
                                                        <th>Date Scanned</th>
                                                        <th>Time Scanned</th>
                                                        <th>Route</th>
                                                        <th>Factory</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div> 
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div class="modal fade" id="modalImportManifest" data-bs-backdrop="static" data-bs-formid="" tabindex="-1" role="dialog" aria-labelledby="">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title"><i class="fas fa-info-circle fa-sm"></i> Import Manifest</h3>
                    <button id="close" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="formImportManifest" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="form-label">Select File</label>
                            <input class="form-control" type="file" id="fileImportManifest" name="manifest" accept=".xlsx, .xls">
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-sm btn-success" title="Import" onclick="$('#formImportManifest').submit()" id="btnImport">Import</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
    
    
    
@endsection

<!--     {{-- JS CONTENT --}} -->
@section('js_content')
    <script type="text/javascript">
        let dtManifest, dtInconsistent;
        $(document).ready(function () {
            getRouteCode($('#selRouteFilter'));

            dtManifest = $("#tableManifest").DataTable({
                "processing" : true,
                "serverSide" : true,
                "ajax" : {
                    url: "{{ route('dt_get_manifest') }}",
                     data: function (param){
                        param.date_scanned = $("#txtDateScanFilter").val();
                        param.route = $("#selRouteFilter").val();
                    }
                },
                fixedHeader: true,
                "columns":[
                    { "data" : "emp_no" },
                    { "data" : "date_scanned" },
                    { "data" : "time_scanned" },
                    { "data" : "route_details.routes_destination" },
                    { 
                        "data" : "factory",
                        render: function(data){
                            if(data == 1){
                                return "Factory 1 & 2";
                            }
                            else{
                                return "Factory 3";
                            }
                        }
                    },
                ],
                "columnDefs": [
                    {"className": "dt-center", "targets": "_all"},
                ],
                'drawCallback': function( settings ) {
                    let dtApi = this.api();
                }
            });//end
            
            $('#formImportManifest').on('submit', function(e){
                e.preventDefault();
                
                let formData = new FormData($(this)[0]);

                $.ajax({
                    type: "post",
                    url: "import_manifest",
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: "json",
                    beforeSend: function(){
                        $('#formImportManifest').prop('disabled', true);
                    },
                    success: function (response) {
                        $('#formImportManifest').prop('disabled', false);
                        if(!response.result){
                            toastr.error('Something went wrong! Please call ISS.');
                            return
                        }
                        toastr.success('Successfully Imported!');
                        $('#modalImportManifest').modal('hide');
                        $('#formImportManifest')[0].reset();
                        dtManifest.draw();
                    },
                    error: function (data, xhr, status) {
                        $('#formImportManifest').prop('disabled', false);

                        if(data.status == 422){
                            toastr.error(data.responseJSON.msg);
                            return;
                        }
                        toastr.error('An error occured!\n' + 'Data: ' + data + "\n" + "XHR: " + xhr + "\n" + "Status: " + status);
                    }
                });
            });

            $('#inconsistentTab').on('click', function(){
                drawInconsistentTable();
            });
        });
        
        $(document).on('change', '.filterList', function(){
            dtManifest.draw();
        });

        $(document).on('change', '.filterListInconsistent', function(){
            drawInconsistentTable();
        });

        const drawInconsistentTable = () => {
            dtInconsistent = $("#tableInconsistent").DataTable({
                "processing" : true,
                "serverSide" : true,
                "bDestroy"  : true,
                "ajax" : {
                    url: "{{ route('dt_get_inconsistent') }}",
                     data: function (param){
                        param.date = $("#txtDateInconsistent").val();
                    }
                },
                fixedHeader: true,
                "columns":[
                    { "data" : "emp_no" },
                    { "data" : "date_scanned" },
                    { "data" : "time_scanned" },
                    { "data" : "route_details.routes_destination" },
                    { 
                        "data" : "factory",
                        render: function(data){
                            if(data == 1){
                                return "Factory 1 & 2";
                            }
                            else{
                                return "Factory 3";
                            }
                        }
                    },
                ],
                // "columnDefs": [
                //     {"className": "dt-center", "targets": "_all"},
                //     {
                //         "targets": [7],
                //         "data": null,
                //         "defaultContent": "---"
                //     },
                // ],
                // 'drawCallback': function( settings ) {
                //     let dtApi = this.api();
                // }
            });//end of dataTableDevices
        }
    </script>
@endsection

