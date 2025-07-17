@if(isset(Auth::user()->id))
    <script type="text/javascript">
        window.location = "{{ url('dashboard') }}";
    </script>
@else
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
                    <li class="breadcrumb-item active">Change Password</li>
                </ol>
            </nav>
        </div>
        
        <div class="container">
            <div class="card">
                <div class="card-body">
                    <p>Change your password</p>
    
                    {{-- <form action="{{ route('change_pass') }}" method="post" id="formChangePassword"> --}}
                    <form action="" method="post" id="formChangePassword">
                        @csrf
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" name="username" placeholder="Username" id="txtChangePasswordUsername" value="{{ Auth::user()->username }}" readonly="">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-user"></span>
                                </div>
                            </div>
                        </div>
    
                        <div class="input-group mb-3">
                            <input type="password" class="form-control" name="password" placeholder="Default Password" id="txtChangePasswordPassword">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-lock"></span>
                                </div>
                            </div>
                        </div>
    
                        <div class="input-group mb-3">
                            <input type="password" class="form-control" name="new_password" placeholder="New Password" id="txtChangePasswordNewPassword">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-lock"></span>
                                </div>
                            </div>
                        </div>
    
                        <div class="input-group mb-3">
                            <input type="password" class="form-control" name="confirm_password" placeholder="Confirm Password" id="txtChangePasswordConfirmPassword">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-lock"></span>
                                </div>
                            </div>
                        </div>
    
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-dark btn-block" id="btnChangePass"><i class="fa fa-check" id="iBtnChangePassIcon"></i>Change Password</button>
                                <a id="btnLoginAnother" class="btn btn-default btn-block"><i class="fa fa-unlock" id="iBtnChangePassIcon"></i>Sign In</a>
                            </div>
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
            $("#formSignIn").submit(function(event){
                event.preventDefault();
                signIn();
            });

        });
        
    </script>
@endif


