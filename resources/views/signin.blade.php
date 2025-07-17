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
                    <li class="breadcrumb-item active">Sign In</li>
                </ol>
            </nav>
        </div>
        
        <div class="container d-flex align-items-center" style="height: calc(100vh - 61.5px)">
            <div class="row mx-auto align-items-center">
                <div class="col-lg-6">
                    <img class="svg-images w-75 img-fluid d-none d-lg-block" src="{{ asset('/images/svg/undraw_social_interaction.svg') }}">
                </div>
                <div class="col-lg-6 shadow p-4 rounded">
                    <h1 class="fw-bold text-left">Sign In</h1>
                    <p class="text-left">Sign in to personalize your account and easy access to your most important information from the website.</p>
                    <form id="formSignIn">
                        <div class="mb-3">
                            <label for="exampleFormControlInput1" class="form-label">Username<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="username" id="textUsername" placeholder="Username">
                        </div>
                        <div class="mb-3">
                            <label for="exampleFormControlInput1" class="form-label">Password<span class="text-danger">*</span></label>
                            <input type="password" class="form-control" name="password" id="textPassword" placeholder="Password">
                        </div>
                        <div class="submit-button text-right">
                            <button class="btn btn-success" id="btnSignIn" type="submit"><i id="btnSignInIcon" class="fa fa-check"></i> Login</button>
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


