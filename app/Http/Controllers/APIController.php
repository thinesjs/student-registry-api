<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class APIController extends Controller
{
    /**
     * user login
     */
    public function userLogin(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if($validate->fails()) return response()->json(['status' => 'error', 'message' => 'invalid inputs'], 422);

        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            $user = Auth::user();
            $access_token = $user->createToken(env('JWTSecretKey'))->accessToken;

            return response()->json(['status' => 'success', 'access_token' => $access_token], 200);
        }
        return response()->json(['status' => 'error', 'message' => 'invalid email or password'], 401);
    }

    /**
     * user register
     */
    public function userRegister(Request $request): JsonResponse
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'confirm_password' => 'required|same:password',
        ]);

        if($validate->fails()) return response()->json(['status' => 'error', 'message' => 'invalid or missing parameters'], 422);

        $inputs = $request->all();
        $inputs['password'] = bcrypt($request->password);
        $user = User::create($inputs);

        $access_token = $user->createToken(env('JWTSecretKey'))->accessToken;
        return response()->json(['status' => 'success', 'access_token' => $access_token], 200);
    }

    /**
     * user logout
     */
    public function userLogout(User $user)
    {
        $accessToken = Auth::guard('api')->user()->token();
        \DB::table('oauth_refresh_tokens')
            ->where('access_token_id', $accessToken->id)
            ->update(['revoked' => true]);
        $accessToken->revoke();
        return response()->json(['status' => 'success'], 200);
    }
}
