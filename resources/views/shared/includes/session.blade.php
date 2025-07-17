@php
    $isLogin = false;
    if(!isset($_SESSION)){
        session_start();
    }
    $asd;
    $layout = "";
    if(isset($_SESSION['rapidx_user_id'])){
        $isLogin = true;
        $user_level_id = $_SESSION['rapidx_user_level_id']; 
        $rapidx_user_id =  $_SESSION["rapidx_user_id"];
        $shuttle_allocation_user_role_id =  $_SESSION["shuttle_allocation_user_role_id"];
    }

    // echo $shuttle_allocation_user_role_id;
@endphp

@if($isLogin)
    @php
        if($shuttle_allocation_user_role_id == 1){
            $layout  = "layouts.admin_layout";
        }else if($shuttle_allocation_user_role_id == 2){
            $layout = "layouts.person_incharge_layout";
        }else if($shuttle_allocation_user_role_id == 3){
            $layout = "layouts.superior_layout";
        }
    @endphp

    @extends($layout)
@else
    <script type="text/javascript">
        toastr.error('Session Expired! Please login again.');
        window.location = "signin_page";
    </script>
@endif
