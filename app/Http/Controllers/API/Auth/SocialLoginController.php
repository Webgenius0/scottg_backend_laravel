<?php

namespace App\Http\Controllers\API\Auth;

use App\Models\User;
use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialLoginController extends Controller
{

    public function googleLogin(Request $request)
    {

        try {

            $token = $request->input('token');

            $googleUser = Socialite::driver('google')->stateless()->userFromToken($token);


            $user = User::firstOrCreate(
                ['email' => $googleUser->getEmail()],
                [
                    'name' => $googleUser->getName(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'password' => bcrypt($googleUser->getId())
                ]
            );



            Auth::login($user);



            $token = auth('api')->login($user);

            $userData = [

                'id' => $user['id'],
                'google_id' => $user['google_id'] ? true : false,
                'name' => $user['name'],
                'email' => $user['email'],
                'avatar' => $user['avatar'],
                'role' => $user['role'],
                'token' => $token,
                'expires_in' => auth('api')->factory()->getTTL() * 60,
            ];

            return response()->json([
                'status' => true,
                'message' => 'Login Successfully',
                'code' => 200,
                'data' => $userData
            ]);
        } catch (\Exception $e) {

            Log::error($e->getMessage());
            return Helper::jsonErrorResponse($e->getMessage(), 500);
        }
    }
}
