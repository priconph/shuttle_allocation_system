@extends('layouts.subcon_coor_layout')

@section('title', 'Dashboard')
@section('content_page')
    <style>
        .warning-alert-msg{
            background-color: #fff3cd;
            color: #856404;
            padding: 12px 16px;
            border: 1px solid #ffeeba;
            border-radius: 4px;
            font-weight: 500;
        }
    </style>
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Subcon Attendance</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Subcon Attendance</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-end">
                                <div>
                                    <button class="btn btn-primary" id="btnUploadAttendance" title="Import Attendance"><i class="fa-solid fa-upload"></i> Import</button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped table-sm w-100" id="tableSubconAttendance">
                                        <thead>
                                            <tr>
                                                <th>ID No.</th>
                                                <th>Employee Name</th>
                                                <th>Date/Time In</th>
                                                <th>Date/Time Out</th>
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
    <div class="modal fade" id="modalImportAttendance" data-bs-backdrop="static" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title"><i class="fas fa-info-circle fa-sm"></i> Import Attendance</h3>
                    <button id="close" class="close" data-bs-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="formImportAttendance" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="warning-alert-msg">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                            <label>Alert!</label><br>
                            Please use the provided template for uploading.<br>
                            Double-check your file before proceeding.
                            <hr>
                            <a href="{{ asset('public/storage/template/subcon_time_in_out.xlsx') }}" class="btn btn-sm btn-primary" download><i class="fa-solid fa-file-arrow-down"></i> Template</a>
                        </div>
                        <div class="form-group mt-3">
                            <label for="attendanceFile">Import Attendance</label>
                            <input type="file" class="form-control" name="attendance_file" id="attendanceFile" accept=".csv, .xlsx, .xls" required>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-end">
                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-sm btn-success">Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js_content')
<script>
    let dtSubconAttendance;
    $(document).ready(function () {
        dtSubconAttendance = $('#tableSubconAttendance').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                type: 'GET',
                url: '{{ route("dt_get_subcon_attendance") }}'
            },
            columns: [
                { data: 'emp_id'},
                { data: 'emp_name'},
                { 
                    data: 'date_in',
                    render(data, type, row) {
                        if(data){
                            return `${row.date_in} ${row.time_in}`;
                            
                        }
                        return '';
                    }
                },
                 { 
                    data: 'date_out',
                    render(data, type, row) {
                        if(data){
                            return `${row.date_out} ${row.time_out}`;
                            
                        }
                        return '';
                    }
                },
            ],
            order: [[2, 'desc']],
        });

        $('#btnUploadAttendance').on('click', function(){
            $('#modalImportAttendance').modal('show');
        });

        $('#formImportAttendance').on('submit', function(e) {
            e.preventDefault();
            let formData = new FormData(this);
            $.ajax({
                url: '{{ route("import_subcon_attendance") }}',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    if(response['result']){
                        toastr.success(response['msg']);
                        dtSubconAttendance.draw();
                        $(this)[0].reset();
                    }
                    $('#modalImportAttendance').modal('hide');
                },
                error: function(data, xhr, status) {
                    if(data.status == 409){
                        toastr.error(data.responseJSON.msg);
                        return;
                    }
                    toastr.error('Something went wrong. Please check your file.');
                }
            });
        });
    });
</script>
@endsection
