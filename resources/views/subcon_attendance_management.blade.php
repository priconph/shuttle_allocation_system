@extends('layouts.subcon_coor_layout')

@section('title', 'Dashboard')
@section('content_page')
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
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped w-100" id="tableSubconAttendance">
                                        <thead>
                                            <tr>
                                                <th>ID No.</th>
                                                <th>Employee Name</th>
                                                <th>Date/Time In</th>
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
@endsection

@section('js_content')
<script>
    let dtSubconAttendance;
    $(document).ready(function () {
        // dtSubconAttendance = $('#tableSubconAttendance').DataTable({
        //     processing: true,
        //     serverSide: true,
        //     ajax: {
        //         type: 'GET',
        //     },
        //     columns: [
        //         { data: 'id_no', name: 'id_no' },
        //         { data: 'employee_name', name: 'employee_name' },
        //         { data: 'date_time_in', name: 'date_time_in' },
        //     ],
        //     order: [[2, 'desc']],
        // });
    });
</script>
@endsection
