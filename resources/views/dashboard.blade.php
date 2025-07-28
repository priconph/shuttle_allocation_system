@php
    $isLogin = false;
    $shuttle_allocation_user_role_id = null;

    if (!isset($_SESSION)) {
        session_start(); // ❌ Remove if using Laravel session system
    }

    $layout = null;

    if (isset($_SESSION['rapidx_user_id'])) {
        $isLogin = true;
        $user_level_id = $_SESSION['rapidx_user_level_id'] ?? null;
        $rapidx_user_id = $_SESSION["rapidx_user_id"] ?? null;
        $shuttle_allocation_user_role_id = $_SESSION["shuttle_allocation_user_role_id"] ?? null;
    }

    switch ($shuttle_allocation_user_role_id) {
        case 1:
            $layout = "layouts.admin_layout";
            break;
        case 2:
            $layout = "layouts.person_incharge_layout";
            break;
        case 3:
            $layout = "layouts.superior_layout";
            break;
        default:
            echo view('errors.unauthorized'); // ✅ Directly render the unauthorized error page
            exit; // ✅ Stop further execution
    }
    if($rapidx_user_id == 216){
            $layout = "layouts.subcon_coor_layout"; // ✅ Special case for Subcon Coordinator
    }

@endphp

@extends($layout) {{-- ✅ Will only be executed if a valid layout exists --}}

@if ($shuttle_allocation_user_role_id == 1)
    @section('title', 'Dashboard')
    @section('content_page')
        <div class="content-wrapper">
            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <h2 class="my-3">Dashboard</h2>
                        <div class="col-sm-12 col-md-6 col-xl-4">
                            <div class="card card-dashboard">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col mt-0">
                                            <h5 class="card-title-dashboard">USER</h5>
                                        </div>

                                        <div class="col-auto">
                                            <div class="stat text-primary">
                                                <span><i class="fa-solid fa-xl fa-users"></i></i></span>
                                            </div>
                                        </div>
                                    </div>
                                    <h1 class="mt-1 mb-3" id="totalUsers">0</h1>
                                    <a href="{{ route('user_management') }}">View Users</a>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12 col-md-6 col-xl-4">
                            <div class="card card-dashboard">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col mt-0">
                                            <h5 class="card-title-dashboard">MASTERLIST</h5>
                                        </div>

                                        <div class="col-auto">
                                            <div class="stat text-primary">
                                                <span><i class="fa-solid fa-xl fa-list-check"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                    <h1 class="mt-1 mb-3" id="totalMasterlist">0</h1>
                                    <a href="{{ route('masterlist_management') }}">View Masterlists</a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-sm-12 col-md-6 col-xl-4">
                            <div class="card card-dashboard">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col mt-0">
                                            <h5 class="card-title-dashboard">ROUTES</h5>
                                        </div>

                                        <div class="col-auto">
                                            <div class="stat text-primary">
                                                <span><i class="fa-solid fa-xl fa-route"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                    <h1 class="mt-1 mb-3" id="totalRoutes">0</h1>
                                    <a href="{{ route('routes_management') }}">View Routes</a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-sm-12 col-md-6 col-xl-4">
                            <div class="card card-dashboard">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col mt-0">
                                            <h5 class="card-title-dashboard">SHUTTLE PROVIDER</h5>
                                        </div>

                                        <div class="col-auto">
                                            <div class="stat text-primary">
                                                <span><i class="fa-solid fa-xl fa-bus"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                    <h1 class="mt-1 mb-3" id="totalShuttleProvider">0</h1>
                                    <a href="{{ route('shuttle_provider_management') }}">View Shuttle Providers</a>
                                </div>
                            </div>
                        </div>
                </div>
            </section>
        </div>
    @endsection

    <!--     {{-- JS CONTENT --}} -->
    @section('js_content')
        <script type="text/javascript">
            $(document).ready(function () {
                function getDataForDashboard(){
                    $.ajax({
                        url: "get_data_for_dashboard",
                        method: "get",
                        dataType: "json",
                        success: function(response){
                            $('#totalUsers').text(response['totalUsers']);
                            $('#totalMasterlist').text(response['totalMasterlist']);
                            $('#totalRoutes').text(response['totalRoutes']);
                            $('#totalShuttleProvider').text(response['totalShuttleProvider']);
                        },
                    });
                }
                getDataForDashboard();
            });
        </script>
    @endsection
@endif