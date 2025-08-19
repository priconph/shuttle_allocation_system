
<aside class="main-sidebar sidebar-dark-navy elevation-4" style="height: 100vh">

    <!-- System title and logo -->
    {{-- <a href="{{ route('dashboard') }}" class="brand-link"> --}}
    <a href="" class="brand-link text-center mt-2">
        <img src=""
            class="brand-image img-circle elevation-3"
            style="opacity: .8">

        <span class="brand-text font-weight-light"><h5>Shuttle Allocation</h5></span>
    </a> <!-- System title and logo -->

    <!-- Sidebar -->
    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item has-treeview">
                    <a href="{{ url('../RapidX') }}" class="nav-link">
                        <i class="nav-icon fas fa-arrow-left"></i>
                        <p>Return to RapidX</p>
                    </a>
                </li>
                <li class="nav-item has-treeview mb-3">
                    <a href="{{ route('dashboard') }}" class="nav-link">
                    {{-- <a href="" data-toggle="modal" data-target="" class="nav-link"> --}}
                        <i class="nav-icon fa-solid fa-gauge-high"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <li class="nav-header font-weight-bold">&nbsp;MOBILE APPLICATION</li>
                <li class="nav-item has-treeview">
                    <a href="http://192.168.3.188/shuttle_manifest/F1shuttleManifest.apk" class="nav-link">
                        <i class="fa fa-download"></i>
                        <p>F1 Application Download</p></p>
                    </a>
                </li>

                <li class="nav-item has-treeview mb-3">
                    <a href="http://192.168.3.188/shuttle_manifest/F3shuttleManifest.apk" class="nav-link">
                        <i class="fa fa-download"></i>
                        <p>F3 Application Download</p></p>
                    </a>
                </li>

                <li class="nav-header font-weight-bold">&nbsp;MAIN</li>
                <li class="nav-item has-treeview">
                    <a href="{{ route('masterlist_management') }}" class="nav-link">
                        <i class="nav-icon fa-solid fa-user-lock"></i>
                        <p>Masterlist</p></p>
                    </a>
                </li>
                <li class="nav-item has-treeview">
                    <a href="{{ route('allocations')}}" class="nav-link">
                        <i class="nav-icon fa-solid fa-user-clock"></i>
                        <p>Allocations</p></p>
                    </a>
                </li>

                <li class="nav-item has-treeview mb-3">
                    <a href="{{ route('subcon_attendance_management') }}" class="nav-link">
                        <i class="nav-icon fa-solid fa-user-shield"></i>                        
                        <p>Subcon Attendance</p></p>
                    </a>
                </li>

                <li class="nav-header font-weight-bold">&nbsp;CONFIGURATION</li>
                <li class="nav-item has-treeview">
                    <a href="{{ route('user_management') }}" class="nav-link">
                        <i class="nav-icon fa-solid fa-user-gear"></i>
                        <p>User</p></p>
                    </a>
                </li>

                <li class="nav-item has-treeview">
                    <a href="{{ route('routes_management') }}" class="nav-link">
                        <i class="nav-icon fa-solid fa-map-location-dot"></i>
                        <p>Routes</p></p>
                    </a>
                </li>

                <li class="nav-item has-treeview">
                    <a href="{{ route('pickup_time_management') }}" class="nav-link">
                        <i class="nav-icon fa-solid fa-clock"></i>
                        <p>Pickup Time</p></p>
                    </a>
                </li>

                <li class="nav-item has-treeview">
                    <a href="{{ route('shuttle_provider_management') }}" class="nav-link">
                        <i class="nav-icon fa-solid fa-van-shuttle"></i>
                        <p>Shuttle Provider</p></p>
                    </a>
                </li>

                <li class="nav-item has-treeview">
                    <a href="{{ route('cutoff_time_management')}}" class="nav-link">
                        <i class="nav-icon fa-solid fa-bell-slash"></i>
                        <p>Cut-off Time</p></p>
                    </a>
                </li>
                <li class="nav-item has-treeview mb-3 d-none">
                    <a href="{{ route('import_shuttle_manifest')}}" class="nav-link">
                        <i class="nav-icon fa-solid fa-file-excel"></i>
                        <p>Import Manifest</p></p>
                    </a>
                </li>

                <li class="nav-header font-weight-bold">&nbsp;REPORT</li>
                <li class="nav-item has-treeview">
                    <a href="{{ route('export_report_v2')}}" class="nav-link">
                        <i class="nav-icon fa-solid fa-file-excel"></i>
                        <p>Reports</p></p>
                    </a>
                </li>

            </ul>
        </nav>
    </div><!-- Sidebar -->
</aside>

