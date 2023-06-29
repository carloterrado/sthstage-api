<?php

namespace App\Http\Controllers\V1;

use App\Models\Catalog;
use App\Models\UserRole;
use App\Models\OauthClient;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Middleware\CheckEndpointAccessMiddleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;


class UserController extends Controller
{
    public function userLogin(Request $request)
    {

        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'email' => 'required',
                'password' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()->all()], 422);
            }

            if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                $user = Auth::user();
                $token = $user->createToken('My Token')->accessToken;

                return response()->json(['token' => $token]);
            }
        }

        return response()->json(['error' => 'Invalid credentials'], Response::HTTP_UNAUTHORIZED);
    }

    public function getUsers()
    {

        $users = DB::table('oauth_clients')->where('user_id', '!=', null)
            ->get()->toArray();
        return view('settings.users.users')->with(compact('users'));
    }

    public function showUserCatalogSettings($id)
    {
        $columns = [
            "id",
            "unq_id",
            "category",
            "sub_category",
            "discontinued",
            "legacy_brand",
            "brand",
            "mspn",
            "external_id",
            "external_id_type",
            "brand_id",
            "model",
            "manufacturer",
            "lt_p",
            "size_dimensions",
            "full_size",
            "full_bolt_patterns",
            "full_bolt_pattern_1",
            "full_bolt_pattern_2",
            "c_z_rated",
            "rft",
            "vast_description",
            "description",
            "long_description",
            "notes",
            "features",
            "install_time",
            "length_val",
            "section_width",
            "section_width_unit_id",
            "aspect_ratio",
            "rim_diameter",
            "rim_diameter_unit_id",
            "overall_diameter",
            "overall_diameter_unit_id",
            "weight_tire",
            "weight_tire_unit_id",
            "length_package",
            "length_unit_id",
            "width_package",
            "width_unit_id",
            "height_package",
            "height_unit_id",
            "weight_package",
            "weight_unit_id",
            "wheel_finish",
            "simple_finish",
            "side_wall_style",
            "load_index_1",
            "load_index_2",
            "speed_rating",
            "load_range",
            "load_rating",
            "back_spacing",
            "offset",
            "center_bore",
            "ply",
            "tread_depth",
            "tread_depth_unit_id",
            "rim_width",
            "rim_width_unit_id",
            "max_rim_width",
            "max_rim_width_unit_id",
            "min_rim_width",
            "min_rim_width_unit_id",
            "utqg",
            "tread_wear",
            "traction",
            "temperature",
            "warranty_type",
            "warranty_in_miles",
            "max_psi",
            "max_load_lb",
            "image_url_full",
            "image_url_quarter",
            "image_side",
            "image_url_tread",
            "image_kit_1",
            "image_kit_2",
            "season",
            "tire_type_performance",
            "car_type",
            "country",
            "quality_tier",
            "construction",
            "source",
            "oem_fitments",
            "status",
            "msct",
            "wheel_diameter",
            "wheel_width",
            "bolt_pattern_1",
            "bolt_circle_diameter_1",
            "bolt_pattern_2",
            "bolt_circle_diameter_2",
        ];

        $endpoints = [
            'api/v1/catalog/tires',
            'api/v1/catalog/wheels',
            'api/v1/catalog/vehicles',
            'api/v1/catalog/vehicle/size',
            'api/v1/catalog/vehicle/tires',
            'api/v1/catalog/vehicle/wheels',
            'api/v1/catalog/location',
            'api/v1/catalog/inventory',
            'api/v1/catalog/order/status'
        ];


        // return view('settings.users.user-settings', compact('endpoints'));

        // $client = OauthClient::select('id', 'access')->where('id', $id)->first()->toArray();


        // return view('settings.users.user-settings')->with(compact('client', 'columns', 'id'));

        $client = UserRole::select('id', 'access', 'endpoint_access')->where('id', $id)->first()->toArray();


        return view('settings.users.user-settings')->with(compact('client', 'columns', 'id', 'endpoints'));
    }

    public function updateUserColumnSettings(Request $request, $id)
    {
        $tableColumns = Schema::getColumnListing('catalog');
        $filteredColumns = array_diff($tableColumns, $request->column);
        DB::table('user_roles')
            ->select('id', 'role', 'access')
            ->where('id', $id)
            ->update([
                'access' => json_encode(array_values($filteredColumns))
            ]);

        return redirect()->back();
    }

    public function updateUserEndpointSettings(Request $request, $id){

        $allowedEndpoints = $request->endpoint;

        DB::table('user_roles')->where('id', $id)->update([
            'endpoint_access' => $allowedEndpoints
        ]);


        return redirect()->back();
    }

    public function userManagementPage()
    {
        
        $roles = DB::table('user_roles')->where('id', '!=', null)
            ->get()->toArray();

        $users = DB::table('users')
            ->leftJoin('user_roles', 'users.role', '=', 'user_roles.role')
            ->select('user_roles.*', 'users.*')
            ->orderBy('users.created_at', 'desc')
            ->paginate(10);

        // dd($users);
        return view('settings.userManagement.userManagement')->with(compact('users', 'roles'));
    }

    public function addUser(Request $request)
    {

        $userData = DB::table('users')
            ->insert([
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'api_token' => NULL,
                'role' => $request->role,
                'status' => $request->status,
                'seenlog' => $request->seenlog,
                'display_user' => $request->display_user,
                'remember_token' => NULL,
                'session_id' => ''
            ]);

        return redirect()->back();
    }

    public function deleteUser($id)
    {
        DB::table('users')->where('id', $id)->delete();

        return redirect()->back();
    }


    public function editUser(Request $request, $id)
    {
        DB::table('users')
            ->where('id', $id)
            ->update([
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'status' => (int) $request->status,
                'seenlog' => (int) $request->seenlog,
                'display_user' => (int) $request->display_user,
            ]);

        return redirect()->back();
    }

    public function showLoginForm()
    {

        return view('login.login');
    }

    public function login(Request $request)
    {


        if (Auth::attempt($request->only('email', 'password'))) {
            return redirect('/users');
        } else {
            return redirect('login');
        }
    }


    public function logout(Request $request)
    {
        Auth::logout();

        return redirect('/login');
    }

    public function getRole()
    {
        $roles = DB::table('user_roles')->where('id', '!=', null)
            ->get()->toArray();

        return view('settings.users.users')->with(compact('roles'));
    }

    public function searchRole(Request $request)
    {
        $searchTerm = $request->input('search');

        $roles = DB::table('user_roles')
            ->where('id', '!=', null)
            ->where(function ($query) use ($searchTerm) {
                $query->where('role', 'LIKE', '%' . $searchTerm . '%');
            })
            ->get()
            ->toArray();

        return view('settings.users.users')->with(compact('roles'));
    }

    public function addRole(Request $request)
    {

        $roleData = DB::table('user_roles')
            ->insert([
                'role' => $request->role,
                'access' => $request->access,
            ]);

        return redirect()->back();
    }

    public function deleteRole($id)
    {
        DB::table('user_roles')->where('id', $id)->delete();

        return redirect()->back();
    }

    public function searchUser(Request $request)
    {
        $searchTerm = $request->input('search');

        $roles = DB::table('user_roles')->where('id', '!=', null)
            ->get()->toArray();

        $searchTerm = $request->input('search');

        $users = DB::table('users')
            ->leftJoin('user_roles', 'users.role', '=', 'user_roles.role')
            ->select('user_roles.*', 'users.*')
            ->where(function ($query) use ($searchTerm) {
                $query->where(DB::raw("CONCAT(users.firstname, ' ', users.lastname)"), 'LIKE', '%' . $searchTerm . '%')
                    ->orWhere('users.email', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhere('users.role', 'LIKE', '%' . $searchTerm . '%');
            })
            ->orderBy('users.created_at', 'desc')
            ->paginate(10);


        return view('settings.userManagement.userManagement')->with(compact('users', 'roles'));
    }

    public function roleController($id)
    {

        $role = DB::table('user_roles')->select('id', 'role', 'access')->where('id', $id)->first();
        $columns = Schema::getColumnListing('catalog');
        return view('settings.users.controller')->with(compact('role', 'columns', 'id'));
    }
}