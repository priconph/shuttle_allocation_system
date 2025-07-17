<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Auth; // or use Illuminate\Support\Facades\Auth;
use DataTables;

/**
 * Import Models here
 */
use App\Models\User;
use App\Models\UserLevel;
use App\Models\RapidXUser;
use App\Models\UserRole;
use App\Models\Masterlist;
use App\Models\Routes;
use App\Models\ShuttleProvider;

class UserController extends Controller
{
    public function signIn(Request $request)
    {
        $data = array(
            'username' => $request->username,
            'password' => $request->password,
            // 'is_deleted' => 0
        );
        // return $data;

        $validator = Validator::make($data, [
            'username' => 'required',
            'password' => 'required|min:8'
        ]);

        if ($validator->passes()) {
            if (Auth::attempt($data)) {
                if(Auth::user()->is_deleted == 1){
                    Auth::logout();
                    return response()->json(['isDeleted' => 1, 'error_message' => 'Your account was already deleted!']);
                }
                else if(Auth::user()->is_authenticated == 0){
                    Auth::logout();
                    return response()->json(['isAuthenticated' => 0, 'error_message' => 'Your account was already registered. Kindly wait for the approval of the Administrator']);
                }
                else if(Auth::user()->status == 0){
                    Auth::logout();
                    return response()->json(['inactive' => 0, 'error_message' => 'Your account is currently deactivated. Kindly contact the Administrator']);
                }
                // else if (Auth::user()->is_password_changed == 0) {
                //     return response()->json(['isPasswordChanged' => 0, 'error_message' => 'Change Password!']);
                // }
                else {
                    session_start();
                    $_SESSION["session_user_id"] = Auth::user()->id;
                    $_SESSION["session_user_level_id"] = Auth::user()->user_level_id;
                    $_SESSION["session_username"] = Auth::user()->username;
                    $_SESSION["session_firstname"] = Auth::user()->firstname;
                    $_SESSION["session_lastname"] = Auth::user()->lastname;
                    $_SESSION["session_email"] = Auth::user()->email;

                    return response()->json(['hasError' => 0]);
                }
            } else {
                return response()->json(['hasError' => 1,  'error_message' => 'We do not recognize your username and/or password. Please try again.']);
            }
        } else {
            return response()->json(['validationHasError' => 1, 'error' => $validator->messages()]);
        }
    }

    public function addUser(Request $request){
        date_default_timezone_set('Asia/Manila');

        $data = $request->all();

        /* For Insert */
        if(!isset($request->user_id)){
            $validator = Validator::make($data, [
                'name' => 'required', // or regex:/^[a-zA-Z ]+$/
                'username' => 'required', // or regex:/^[a-zA-Z ]+$/
                'email' => 'required',
                'department' => 'required',
            ]);
    
            if ($validator->fails()) {
                return response()->json(['validationHasError' => 1, 'error' => $validator->messages()]);
            } else {
                DB::beginTransaction();
                try {
                    $userId = User::insertGetId([
                        'rapidx_user_id' => $request->rapidx_user,
                        'name' => $request->name,
                        'username' => $request->username,
                        'email' => $request->email,
                        'department' => $request->department,
                        'user_role_id' => $request->user_roles,
                        'created_at' => date('Y-m-d H:i:s'),
                        'is_deleted' => 0
                    ]);
    
                    // User::where('id', $userId)->update(['created_by' => $userId]);
                    DB::commit();
                    return response()->json(['hasError' => 0]);
                } catch (\Exception $e) {
                    DB::rollback();
                    return response()->json(['hasError' => 1, 'exceptionError' => $e]);
                }
            }
        }
        else{ /* For Update */
            $validator = Validator::make($data, [
                'name' => 'required', // or regex:/^[a-zA-Z ]+$/
                'username' => 'required', // or regex:/^[a-zA-Z ]+$/
                'email' => 'required',
                'department' => 'required',
            ]);
    
            if ($validator->fails()) {
                return response()->json(['validationHasError' => 1, 'error' => $validator->messages()]);
            } else {
                DB::beginTransaction();
                try {
                    User::where('id', $request->user_id)->update([
                        'rapidx_user_id' => $request->rapidx_user,
                        'name' => $request->name,
                        'username' => $request->username,
                        'email' => $request->email,
                        'department' => $request->department,
                        'user_role_id' => $request->user_roles,
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
    
                    // User::where('id', $userId)->update(['created_by' => $userId]);
                    DB::commit();
                    return response()->json(['hasError' => 0]);
                } catch (\Exception $e) {
                    DB::rollback();
                    return response()->json(['hasError' => 1, 'exceptionError' => $e]);
                }
            }
        }
    }

    public function getRapidxUsers(Request $request){
        $rapidxUsers = RapidXUser::with('department')->where('user_stat', '!=', 0)->get();
        // return $rapidxUsers;
        return response()->json(['rapidxUsers' => $rapidxUsers]);
    }

    public function getUserRoles(Request $request){
        $userRoles = UserRole::where('is_deleted', 0)->get();
        // return $userRoles;
        return response()->json(['userRoles' => $userRoles]);
    }

    public function viewUsers(){
        $userDetails = User::with('user_roles')->where('status', 1)->where('is_deleted', 0)->get();
        
        return DataTables::of($userDetails)
            ->addColumn('status', function($userDetail){
                $result = "";
                if($userDetail->status == 1){
                    $result .= '<center><span class="badge badge-pill badge-success">Active</span></center>';
                }
                else{
                    $result .= '<center><span class="badge badge-pill text-secondary" style="background-color: #E6E6E6">Inactive</span></center>';
                }
                return $result;
            })
            ->addColumn('action', function($userDetail){
                if($userDetail->status == 1){
                    $result =   '<center>';
                    $result .=            '<button type="button" class="btn btn-primary btn-xs text-center actionEditUser mr-1" user-id="' . $userDetail->id . '" data-bs-toggle="modal" data-bs-target="#modalAddUser" title="Edit User Details">';
                    $result .=                '<i class="fa fa-xl fa-edit"></i> ';
                    $result .=            '</button>';

                    // if($userDetail->user_level_id != 1){
                    //     $result .=            '<button type="button" class="btn btn-danger btn-xs text-center actionEditUserStatus mr-1" user-id="' . $userDetail->id . '" user-status="' . $userDetail->status . '" data-bs-toggle="modal" data-bs-target="#modalEditUserStatus" title="Deactivate User">';
                    //     $result .=                '<i class="fa-solid fa-xl fa-ban"></i>';
                    //     $result .=            '</button>';
                    // }

                    $result .=        '</center>';
                }
                else{
                    $result =   '<center>
                                <button type="button" class="btn btn-primary btn-xs text-center actionEditUser mr-1" user-id="' . $userDetail->id . '" data-bs-toggle="modal" data-bs-target="#modalAddUser" title="Edit User Details">
                                    <i class="fa fa-xl fa-edit"></i> 
                                </button>
                                <button type="button" class="btn btn-warning btn-xs text-center actionEditUserStatus mr-1" user-id="' . $userDetail->id . '" user-status="' . $userDetail->status . '" data-bs-toggle="modal" data-bs-target="#modalEditUserStatus" title="Activate User">
                                    <i class="fa-solid fa-xl fa-arrow-rotate-right"></i>
                                </button>
                            </center>';
                }
                return $result;
            })
        ->rawColumns(['status', 'action'])
        ->make(true);
    }

    public function getUserById(Request $request){
        $userDetails = User::with('user_roles')->where('id', $request->userId)->get();
        // echo $userDetails;
        return response()->json(['userDetails' => $userDetails]);
    }

    public function editUserStatus(Request $request){        
        date_default_timezone_set('Asia/Manila');
        session_start();

        $data = $request->all(); // collect all input fields

        $validator = Validator::make($data, [
            'user_id' => 'required',
            'status' => 'required',
        ]);

        if($validator->passes()){
            if($request->status == 1){
                User::where('id', $request->user_id)
                    ->update([
                            'status' => 0,
                            'last_updated_by' => $_SESSION['session_user_id'],
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]
                    );
                $status = User::where('id', $request->user_id)->value('status');
                return response()->json(['hasError' => 0, 'status' => (int)$status]);
            }else{
                User::where('id', $request->user_id)
                    ->update([
                            'status' => 1,
                            'last_updated_by' => $_SESSION['session_user_id'],
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]
                    );
                $status = User::where('id', $request->user_id)->value('status');
                return response()->json(['hasError' => 0, 'status' => (int)$status]);
            }
                
        }
        else{
            return response()->json(['validationHasError' => 1, 'error' => $validator->messages()]);
        }
    }

    public function logout(){
        session_start();
        session_unset();
        session_destroy();
        Auth::logout();
        return response()->json(['result' => "1"]);
    }

    public function checkSession(){
        session_start();
        $session = $_SESSION;
        return response()->json(['session' => $session]);
    }

    public function getUserLevels(Request $request){
        $userLevels = UserLevel::where('is_deleted', 0)->get();
        return response()->json(['userLevels' => $userLevels]);
    }

    public function getUsers(Request $request){
        $users = User::where('is_deleted', 0) // 0-Active
                ->where('status', '=', '1') // 1-Active
                ->where('is_authenticated', '=', '1') // 1-Yes
                // ->where('user_level_id', '!=', '1') // 1-Admin
                ->get();
        return response()->json(['users' => $users]);
    }

    public function getDataForDashboard(){
        date_default_timezone_set('Asia/Manila');
        $totalUsers = User::where('is_deleted', 0)->where('status', 1)->get();
        $totalMasterlist = Masterlist::where('is_deleted', 0)->where('masterlist_status', 1)->get();
        $totalRoutes = Routes::where('is_deleted', 0)->where('status', 1)->get();
        $totalShuttleProvider = ShuttleProvider::where('is_deleted', 0)->where('shuttle_provider_status', 1)->get();
        return response()->json([
            'totalUsers' => count($totalUsers), 
            'totalMasterlist' => count($totalMasterlist), 
            'totalRoutes' => count($totalRoutes), 
            'totalShuttleProvider' => count($totalShuttleProvider), 
        ]);
    }

    public function getShuttleAllocationUser(Request $request){
        $userData = User::where('rapidx_user_id', $request->rapidxUserId)->where('is_deleted', 0)->get();
        // return $userData;
        return response()->json(['userData' => $userData]);
    }
}
