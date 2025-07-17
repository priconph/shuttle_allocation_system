@extends('layouts.admin_layout')

@section('title', 'Dashboard')
@section('content_page')
    <div class="content-wrapper">
        <!-- Main content -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>User Management</h1>
                    </div>
                    <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">User Management</li>
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
                                <h3 class="card-title" style="margin-top: 8px;">User Management</h3>
                                {{-- <button class="btn float-right reload"><i class="fas fa-sync-alt"></i></button> --}}
                            </div>
                            <div class="card-body">
                                <div class="text-right mt-4">                   
                                    <button type="button" class="btn btn-primary mb-3" id="buttonAddUser" data-bs-toggle="modal" data-bs-target="#modalAddUser"><i class="fa fa-plus fa-md"></i> New User</button>
                                </div>
                                <div class="table-responsive">
                                    <table id="tableUsers" class="table table-bordered table-hover nowrap" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>Action</th>
                                                <th>Status</th>
                                                <th>Name</th>
                                                <th>Username</th>
                                                <th>Email</th>
                                                <th>Department</th>
                                                <th>User Roles</th>
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
    
    <!-- Add User Modal Start -->
    <div class="modal fade" id="modalAddUser" data-bs-keyboard="false" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><i class="fas fa-info-circle"></i>&nbsp;Add User</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" id="formAddUser" autocomplete="off">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card-body">
                                    <!-- For User Id -->
                                    <input type="text" class="form-control" style="display: none" name="user_id" id="userId">
                                    
                                    <!-- For RapidX User Id -->
                                    <input type="text" class="form-control" style="display: none" name="rapidx_user_id" id="rapidxUserId">

                                    <!-- For Name -->
                                    <input type="text" class="form-control" readonly style="display: none" name="name" id="textName" placeholder="Name">
                                    
                                    <div class="mb-3">
                                        <label for="rapidx_user" class="form-label">Name<span class="text-danger" title="Required">*</span></label>
                                        <select class="form-select select2" id="rapidxUsers" name="rapidx_user">
                                            <!-- Auto Generated -->
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="username" class="form-label">Username</label>
                                        <input type="text" class="form-control" readonly name="username" id="textUsername" placeholder="Username">
                                    </div>
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email address</label>
                                        <input type="text" class="form-control" readonly name="email" id="textEmail" placeholder="Email Address">
                                    </div>
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Department</label>
                                        <input type="text" class="form-control" readonly name="department" id="textDepartment" placeholder="Department">
                                    </div>
                                    <div class="mb-3">
                                        <label for="selectUserRoles" class="form-label">User Roles<span class="text-danger" title="Required">*</span></label>
                                        <select class="form-select select2" id="selectUserRoles" name="user_roles">
                                            <!-- Auto Generated -->
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
                        <button type="submit" id="btnAddUser" class="btn btn-primary"><i id="iBtnAddUserIcon" class="fa fa-check"></i> Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div><!-- Add User Modal End -->
    
    <!-- Edit User Status Modal Start -->
    <div class="modal fade" id="modalEditUserStatus" data-bs-keyboard="false" data-bs-backdrop="static">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="editUserStatusTitle"><i class="fas fa-info-circle"></i> Edit User Status</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" id="formEditUserStatus" autocomplete="off">
                    @csrf
                    <div class="modal-body">
                        <p id="paragraphEditUserStatus"></p>
                        <input type="hidden" name="user_id" placeholder="User Id" id="textEditUserStatusUserId">
                        <input type="hidden" name="status" placeholder="Status" id="textEditUserStatus">
                    </div>
                    
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
                        <button type="submit" id="buttonEditUserStatus" class="btn btn-primary"><i id="iBtnAddUserIcon" class="fa fa-check"></i> Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div><!-- Edit User Status Modal End -->
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
            getUserRoles($('#selectUserRoles'));
            getRapidxUsers($('#rapidxUsers'));
            $("select#rapidxUsers").on('change',function(){
                var selectedRapidxUserId = $(this).children("option:selected").attr('value');
                var selectedName = $(this).children("option:selected").attr('name');
                var selectedUsername = $(this).children("option:selected").attr('username');
                var selectedEmail = $(this).children("option:selected").attr('email');
                // var selectedDepartment = $(this).children("option:selected").attr('department');
                var selectedDepartmentGroup = $(this).children("option:selected").attr('department-group');

                $("input[name='rapidx_user_id']", $('#formAddUser')).val(selectedRapidxUserId);
                $('#textName').val(selectedName);
                $('#textUsername').val(selectedUsername);
                $('#textEmail').val(selectedEmail);
                $('#textDepartment').val(selectedDepartmentGroup);
            });

            dataTablesUsers = $("#tableUsers").DataTable({
                "processing" : false,
                "serverSide" : true,
                "responsive": true,
                // "order": [[ 0, "desc" ],[ 4, "desc" ]],
                "language": {
                    "info": "Showing _START_ to _END_ of _TOTAL_ user records",
                    "lengthMenu": "Show _MENU_ user records",
                },
                "ajax" : {
                    url: "view_users",
                },
                "columns":[
                    { "data" : "action", orderable:false, searchable:false},
                    { "data" : "status"},
                    { "data" : "name"},
                    { "data" : "username"},
                    { "data" : "email"},
                    { "data" : "department"},
                    { "data" : "user_roles.name"},
                ],
            });

            $("#formAddUser").submit(function(event){
                event.preventDefault();
                addUser();
            });

            $(document).on('click', '.actionEditUser', function(){
                let id = $(this).attr('user-id');
                $("input[name='user_id'", $("#formAddUser")).val(id);
                getUserById(id);
            });

            // $(document).on('click', '.actionEditUserStatus', function(){
            //     let userStatus = $(this).attr('user-status');
            //     let userId = $(this).attr('user-id');

            //     $("#textEditUserStatus").val(userStatus);
            //     $("#textEditUserStatusUserId").val(userId);

            //     if(userStatus == 1){
            //         $("#paragraphEditUserStatus").text('Are you sure to deactivate the user?');
            //     }
            //     else{
            //         $("#paragraphEditUserStatus").text('Are you sure to activate the user?');
            //     }
            // });

            // $("#formEditUserStatus").submit(function(event){
            //     event.preventDefault();
            //     editUserStatus();
            // });
        });
    </script>
@endsection

