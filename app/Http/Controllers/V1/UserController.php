<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\OauthClient;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function userLogin(Request $request)
    {
        if($request->isMethod('post'))
        {
            $validator = Validator::make($request->all(),[
                'email' => 'required',
                'password' => 'required'
            ]);
    
            if($validator->fails())
            {
                return response()->json(['error' => $validator->errors()->all()], 422);
            }
    
            if(Auth::attempt(['email' => $request->email, 'password' => $request->password]))
            {
                $user = Auth::user();
                $token = $user->createToken('My Token')->accessToken;
    
                return response()->json(['token' => $token]);
            }
        }
       
        return 'Login';
    }

    public function getUsers()
    {
        $catalog = OauthClient::where('user_id',1)->get()->toArray();
        $users = User::where('id',1)->with('catalogSettings')->get()->toArray();
        dd($users);
    }
}
