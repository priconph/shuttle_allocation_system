@extends('layouts.admin_layout')

@section('title', 'Dashboard')
@section('content_page')
    <div class="content-wrapper">
        <!-- Main content -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Looc Database</h1>
                    </div>
                    <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Looc Database</li>
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
                                <h3 class="card-title" style="margin-top: 8px;">Looc Database</h3>
                            </div>
                            <div class="card-body">
                                <div class="text-right mt-4">                   
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddBarangayResidentDatabase"><i class="fa fa-plus fa-md"></i> New Resident</button>
                                </div><br>
                                <div class="table-responsive">
                                    <table id="tableBarangayResidentDatabase" class="table table-sm table-bordered table-hover display nowrap" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>Action</th>
                                                <th>First Name</th>
                                                <th>Last Name</th>
                                                <th>Middle Initial</th>
                                                <th>Full Address</th>
                                                <th>Gender</th>
                                                <th>Age</th>
                                                <th>Status</th>
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
    

    <!-- Add Resident Database Modal Start -->
    <div class="modal fade" id="modalAddBarangayResidentDatabase" tabindex="-1" data-bs-keyboard="false" data-bs-backdrop="static">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><i class="fas fa-info-circle"></i>&nbsp;Resident Database Details</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" id="formAddBarangayResidentDatabase" autocomplete="off">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card-body">
                                    <!-- For Resident Database Id -->
                                    <input type="text" class="form-control" style="display: none" name="barangay_resident_database_id" id="barangayResidentDatabaseId" aria-label="Default" aria-describedby="inputGroup-sizing-default">
                                    
                                    <div class="mb-3">
                                        <label for="firstname" class="form-label">First Name<span class="text-danger" title="Required">*</span></label>
                                        <input type="text" class="form-control" name="firstname" id="textFirstname" placeholder="Firstname">
                                    </div>
                                    <div class="mb-3">
                                        <label for="lastname" class="form-label">Last Name<span class="text-danger" title="Required">*</span></label>
                                        <input type="text" class="form-control" name="lastname" id="textLastname" placeholder="Lastname">
                                    </div>
                                    <div class="mb-3">
                                        <label for="middleInitial" class="form-label">Middle Initial</label>
                                        <input type="text" class="form-control" name="middle_initial" id="textMiddleInitial" placeholder="Middle Initial">
                                    </div>
                                    <div class="mb-3">
                                        <label for="selectAddress" class="form-label">Full Address<span class="text-danger" title="Required">*</span></label>
                                        <select class="form-select" id="selectAddress" name="address">
                                            <option value="0" disabled selected>Select One</option>
                                            <option value="Looc">Looc</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="selectGender" class="form-label">Gender<span class="text-danger" title="Required">*</span></label>
                                        <select class="form-select" id="selectGender" name="gender">
                                            <option value="0" disabled selected>Select One</option>
                                            <option value="1">Male</option>
                                            <option value="2">Female</option>
                                            <option value="3">Other</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="age" class="form-label">Age</label>
                                        <input type="number" class="form-control" min="1" max="200" name="age" id="textAge" placeholder="Age">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
                        <button type="submit" id="buttonAddBarangayResidentDatabase" class="btn btn-primary" title="On going module"><i id="iconAddBarangayResidentDatabase" class="fa fa-check"></i> Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div><!-- Add Resident Database Modal End -->

    <!-- Edit ResidentDatabase Status Modal Start -->
    <div class="modal fade" id="modalEditBarangayResidentDatabaseStatus" data-bs-keyboard="false" data-bs-backdrop="static">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="editResidentDatabaseStatusTitle"><i class="fas fa-info-circle"></i> Edit Resident Database Status</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" id="formEditBarangayResidentDatabaseStatus" autocomplete="off">
                    @csrf
                    <div class="modal-body">
                        <p id="paragraphEditResidentDatabaseStatus"></p>
                        <input type="hidden" name="barangay_resident_database_id" placeholder="ResidentDatabase Id" id="textEditResidentDatabaseStatusResidentDatabaseId">
                        <input type="hidden" name="status" placeholder="Status" id="textEditResidentDatabaseStatus">
                    </div>
                    
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
                        <button type="submit" id="buttonEditResidentDatabaseStatus" class="btn btn-primary"><i id="iconEditResidentDatabase" class="fa fa-check"></i> Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div><!-- Edit ResidentDatabase Status Modal End -->
@endsection

<!--     {{-- JS CONTENT --}} -->
@section('js_content')
    <script type="text/javascript">
        $(document).ready(function () {
            // Initialize Select2 Elements
            // $('.bootstrap-5').select2();
            $('.bootstrap-5').select2({
                theme: 'bootstrap-5'
            });

            // function capitalizeFirstLetter(string) {
            //     return string.charAt(0).toUpperCase() + string.slice(1);
            // }

            // $("input").keyup(function () { 
            //     const origValue = $(this).val();
            //     console.log('origValue ', origValue);
            //     let newValue = capitalizeFirstLetter(origValue);
            //     console.log('newValue ', newValue);
            //     $(this).val(newValue);
            // });  
            
            $("#formAddBarangayResidentDatabase").submit(function(event){
                event.preventDefault();
                addBarangayResidentDatabase();
            });

            dataTablesBarangayResidentDatabase = $("#tableBarangayResidentDatabase").DataTable({
                "processing" : false,
                "serverSide" : true,
                "responsive": true,
                "orderClasses": false, // disable sorting_1 for unknown background
                // "order": [[ 0, "desc" ],[ 4, "desc" ]],
                "ajax" : {
                    url: "view_barangay_resident_database",
                },
                "columns":[
                    { "data" : "action", orderable:false, searchable:false},
                    { "data" : "firstname"},
                    { "data" : "lastname"},
                    { "data" : "middle_initial"},
                    { "data" : "address"},
                    { "data" : "gender"},
                    { "data" : "age"},
                    { "data" : "status"},
                ],
                "columnDefs": [
                    // { className: 'align-middle', targets: [0, 1, 2, 3, 4, 5, 6 ,7] },
                    { className: 'text-center', targets: [0, 1, 2, 3, 4, 5, 6 ,7] },
                ],
                "createdRow": function(row, data, index) {
                    $('td', row).eq(1).css('white-space', 'normal');
                    $('td', row).eq(2).css('white-space', 'normal');
                    // console.log('row ', row);
                    // console.log('data ', data);
                    // console.log('index ', index);
                },
            });

            $(document).on('click', '.actionEditBarangayResidentDatabase', function(){
                let id = $(this).attr('barangay-resident-database-id');
                console.log('id ', id);
                $("input[name='barangay_resident_database_id'", $("#formAddBarangayResidentDatabase")).val(id);
                getBarangayResidentDatabaseById(id);
            });
            
            $(document).on('click', '.actionEditBarangayResidentDatabaseStatus', function(){
                let barangayResidentDatabaseStatus = $(this).attr('barangay-resident-database-status');
                let barangayResidentDatabaseId = $(this).attr('barangay-resident-database-id');
                
                $("#textEditResidentDatabaseStatus").val(barangayResidentDatabaseStatus);
                $("#textEditResidentDatabaseStatusResidentDatabaseId").val(barangayResidentDatabaseId);

                if(barangayResidentDatabaseStatus == 1){
                    $("#paragraphEditResidentDatabaseStatus").text('Are you sure to disable?');
                }
                else{
                    $("#paragraphEditResidentDatabaseStatus").text('Are you sure to enable?');
                }
            });

            $("#formEditBarangayResidentDatabaseStatus").submit(function(event){
                event.preventDefault();
                editBarangayResidentDatabaseStatus();
            });
        });
    </script>
@endsection
