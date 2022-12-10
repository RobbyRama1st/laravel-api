<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Api\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    /**
     * Create a new users.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function signUp(Request $request)
    {
        try {
            $rules = [
                'name' => [
                    'required',
                    'string',
                    'min:3',
                    'max:30'
                ],
                'email' => [
                    'required',
                    'email',
                    'max:255',
                    'unique:users,email'
                ],
                'password' => [
                    'required',
                    'string',
                    Password::min(8)
                        ->mixedCase()
                        ->numbers()
                        ->symbols()
                ]
            ];

            $this->validate($request, $rules);

            $status = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
            ]);

            if ($status) {
                $response = array(
                    "error" => false,
                    "status_code" => 200,
                    "message" => "User created successfully.",
                );
                return response()->json($response);
            } else {
                $response = array(
                    "error" => true,
                    "status_code" => 500,
                    "message" => "Failed created user.",
                );
                return response()->json($response, 500);
            }
        } catch (ValidationException $e) {
            $response = array(
                "error" => true,
                "status_code" => 400,
                "message" => $e->getMessage(),
            );
            return response()->json($response, 400);
        }
    }

    /**
     * Login users.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request){
        try {
            $rules = [
                'email' => [
                    'required',
                    'email',
                    'max:255',
                ],
                'password' => [
                    'required',
                    'string',
                ]
            ];

            $this->validate($request, $rules);

            $credentials = $request->only('email', 'password');

            try {
                if ($token = auth()->attempt($credentials)) {
                    $response = array(
                        "error" => false,
                        "status_code" => 200,
                        "message" => "Login successfully.",
                        "data" =>  array(
                            'access_token' => $token,
                            'token_type' => 'bearer',
                            'expires_in' => auth('api')->factory()->getTTL() * 60,
                        )
                    );
                    return response()->json($response);
                } else {
                    $response = array(
                        "error" => true,
                        "status_code" => 400,
                        "message" => "Incorrect email or password",
                        "data" => null
                    );
                    return response()->json($response, 400);
                }
            } catch (JWTException $e) {
                $response = array(
                    "error" => true,
                    "status_code" => 400,
                    "message" => $e->getMessage(),
                    "data" => null
                );
                return response()->json($response, 400);
            }
        } catch (ValidationException $e){
            $response = array(
                "error" => true,
                "status_code" => 400,
                "message" => $e->getMessage(),
                "data" => null
            );
            return response()->json($response, 400);
        }
    }
}
