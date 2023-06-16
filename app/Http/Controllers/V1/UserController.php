<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\OauthClient;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

        return 'Login';
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

        $client = OauthClient::select('id', 'catalog_column_settings')->where('id', $id)->first()->toArray();
       

        return view('settings.users.user-settings')->with(compact('client', 'columns','id'));
    }

    public function updateUserColumnSettings(Request $request, $id)
    {
        
        try {
          
            $tableColumns = Schema::getColumnListing('catalog');
            $filteredColumns = array_diff($tableColumns, $request->column);
       
            OauthClient::where('id', $id)->update([
                'catalog_column_settings' => json_encode(array_values($filteredColumns))
            ]);

            return redirect()->route('users')->with('success_message', 'Catalog column access updated successfully!');
        } catch (\Exception $e) {
            // Handle the exception, log the error, etc.
            return redirect()->back()->with('error_message', 'Failed to update catalog column access.')->withInput();
        }
    }


    public function showLoginForm(){
        return view('login.login');
    }

    public function login(Request $request){
        

        if (Auth::attempt($request->only('email', 'password'))) {
            return redirect('/users');
        }else{
            return redirect('login');
        }
    }


    public function logout(Request $request){
        Auth::logout();

        return redirect('/login');
    }
}
