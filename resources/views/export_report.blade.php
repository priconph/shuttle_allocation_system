@extends('layouts.admin_layout')

@section('title', 'Dashboard')
@section('content_page')
    <div class="content-wrapper">
        <!-- Main content -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        {{-- <h1>User Management</h1> --}}
                    {{-- <button class="btn btn-info bgfp" id="modalExportTransportReport" data-toggle="modal" data-target="#modalExportReport"><i class="fas fa-file-download"></i> Export  Report</button> --}}
                    <button class="btn btn-info bgfp" style="margin-top: 20px; width:210px;" data-bs-toggle="modal" data-bs-target="#modalExportReport"><i class="fas fa-download"></i> Export Report</button>
                    </div>
                    <div class="col-sm-6">

                    </div>
                </div>
            </div>
        </section>

        <div class="modal fade" id="modalExportReport">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-dark">
                        <h4 class="modal-title"><i class="fab fa-stack-overflow"></i> Export Report</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                            <div class="col-sm-12">
                                <div class="row">
                                    {{-- <div class="form-group col-sm-6 flex-column">
                                        <label>PO # to be Extracted</label>
                                        <input type="text" name="search_po" id="searchPONumberId">
                                    </div> --}}
                                </div>
                            </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Close</button>
                        <button type="submit" id="btnExportReport" class="btn btn-dark"><i id="BtnExportReportIcon" class="fa fa-check"></i> Export</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->



@endsection

<!--     {{-- JS CONTENT --}} -->
@section('js_content')
    <script type="text/javascript">

$('#btnExportReport').on('click', function(){
            // SummaryOfFindings();
            // let year_id = $('#selectYearId').val();
            // let selected_month = $('#selectMonthId').val();

            window.location.href = `export_report/`;

            $('#modalExportReport').modal('hide');
        });

    </script>
@endsection

