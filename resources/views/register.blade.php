<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Barangay Management System</title>
    @include('shared.css_links.css_links')

</head>
<body>
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-white px-0">
                <li class="breadcrumb-item"><a href="{{ route('index') }}">Home Page</a></li>
                <li class="breadcrumb-item active">Register</li>
            </ol>
        </nav>
    </div>

    <div class="container" style="margin-top: 1rem">
        <h1 class="fw-bold text-md-center">Register</h1>
        <p class="text-left mx-auto">The access request feature allows you to request an access to content that you do not currently have permission to see. Wait for the approval of the website owner to approve your account.</p>
        <div class="row mx-auto flex-wrap-reverse align-items-center mt-5">
            <div class="col-lg-6">
                <img class="svg-images img-fluid d-none d-lg-block" src="{{ asset('/images/svg/undraw_social_interaction.svg') }}">
            </div>
            <div class="col-lg-6">
                <form id="formAddUser">
                    @csrf
                    <div class="mb-3">
                        <label for="firstname" class="form-label">First Name<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="firstname" id="textFirstname" placeholder="Firstname">
                    </div>
                    <div class="mb-3">
                        <label for="lastname" class="form-label">Last Name<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="lastname" id="textLastname" placeholder="Lastname">
                    </div>
                    <div class="mb-3">
                        <label for="middleInitial" class="form-label">Middle Initial</label>
                        <input type="text" class="form-control" name="middle_initial" id="textMiddleInitial" placeholder="Middle Initial">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email address<small>(For Account Activation)</small><span class="text-danger">*</span></label>
                        <input type="email" class="form-control" name="email" id="textEmail" placeholder="Email Address">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Contact Number<span class="text-danger" title="Required">*</span></label>
                        <input type="text" class="form-control" name="contact_number" id="textContactNumber" placeholder="Contact Number">
                    </div>
                    <div class="mb-3">
                        <label for="username" class="form-label">Username<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="username" id="textUsername" placeholder="Username">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password<span class="text-danger">*</span></label>
                        <input type="password" class="form-control" name="password" id="textPassword" placeholder="Password">
                    </div>
                    <div class="mb-3">
                        <label for="confirmPassword" class="form-label">Confirm Password<span class="text-danger">*</span></label>
                        <input type="password" class="form-control" name="confirm_password" id="textConfirmPassword" placeholder="Confirm Password">
                    </div>
                    <div class="submit-button text-right mb-3">
                        <button class="btn btn-success" type="submit" id="btnAddUser"><i id="btnAddUserIcon" class="fa fa-check"></i> Request Access</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
</body>
</html>

@include('shared.js_links.js_links')

<script>
    $(document).ready(function(){
        $("#formAddUser").submit(function(event){
            event.preventDefault();
            addUser();
        });

    });
    
</script>