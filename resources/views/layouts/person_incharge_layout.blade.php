@php
    $isLogin = false;
    if(!isset($_SESSION)){
        session_start();
    }
    if(isset($_SESSION['rapidx_user_id'])){
        $isLogin = true;
        $rapidx_user_id = $_SESSION["rapidx_user_id"];
    }else {
        echo    '<script type="text/javascript">
                    window.location = "/";
                </script>';
    }
@endphp

@if($isLogin)
    <!DOCTYPE html>
    <html>
        <head>
            <meta charset="utf-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <title>Shuttle Bus Allocation System | @yield('title')</title>
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <link rel="shortcut icon" type="image/png" href="">
            <meta name="csrf-token" content="{{ csrf_token() }}">

            <!-- CSS LINKS -->
            @include('shared.css_links.css_links')
            <style>
                .modal-xl-custom{
                    width: 95% !important;
                    min-width: 90% !important;
                }
                table.dataTable.display tbody tr.odd>.sorting_1, table.dataTable.order-column.stripe tbody tr.odd>.sorting_1 {
                    background-color: none !important;
                }

                /* select[readonly].select2+.select2-container--default .select2-selection--multiple {
                    background-color: #eceeef;
                }

                select[readonly].select2+.select2-container--default .select2-selection--single {
                    background-color: #eceeef;
                    border: 1px solid #aaa;
                    border-radius: 4px;
                }
                
                select[readonly].select2+.select2-container {
                    pointer-events: none;
                    touch-action: none;
                    background: #eceeef;
                } */

                select[readonly]{
                    pointer-events: none;
                    touch-action: none;
                    background: #e9ecef;
                    /* color: #6c757d; */
                }
            </style>
        </head>
        <body class="hold-transition sidebar-mini">
            <div class="wrapper">
                    @include('shared.pages.admin_header')
                    @include('shared.pages.user_nav')
                    @include('shared.pages.admin_footer')

                <!-- Global Spinner -->
                <div class="modal fade" id="modalSpinner">
                    <div class="modal-dialog">
                        <div class="modal-content pt-3">
                            <p class="spinner-border spinner-border-xl text-center mx-auto"></p>
                            <p class="mx-auto">Logging out...</p>
                        </div>
                    </div>
                </div>
                <input type="hidden" value="{{ $rapidx_user_id }}" id="txtGlobalUserId">
                @yield('content_page')
            </div>

            <!-- JS LINKS -->
            @include('shared.js_links.js_links')
            @yield('js_content')

            <script type="text/javascript">
                $(document).ready(function(){
                    function UserLogout(){
                        $.ajax({
                            url: "logout",
                            method: "get",
                            dataType: "json",
                            beforeSend: function(){
                            },
                            success: function(reponse){
                                if(reponse['result'] == 1){
                                    window.location = '/';
                                }
                                else{
                                    alert('Logout error!');
                                }
                            }
                        });
                    }

                    $("#btnLogout").click(function(event){
                        $('#modalLogout').modal('hide');
                        $('#modalSpinner').modal('show');
                        setTimeout(() => {
                            UserLogout();
                            console.log("Logging out...")
                        }, 300);
                        
                    });
                });
        
                
            </script>
        </body>
    </html>
@else
    <script type="text/javascript">
        toastr.error('Session Expired! Please login again.');
        window.location = "signin_page";
    </script>
@endif

