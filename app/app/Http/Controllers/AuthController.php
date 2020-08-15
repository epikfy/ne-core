<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\UserController;
use App\Http\Requests\UserRequest;
use Illuminate\Routing\Controller as BaseController;

class AuthController extends BaseController
{

    protected $user;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(UserController $user)
    {
        $this->user = $user;
    }

    /**
     * Handles Registration Request
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(UserRequest $request) : object
    {
        try {
            $data = $this->user->store($request);
            $token = $data->createToken($request->email)->accessToken;
            return response()->json(['token' => $token], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Handles Login Request
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(UserRequest $request) : object
    {
        $credentials = [
            'email' => $request->email,
            'password' => $request->password
        ];
        if (auth()->attempt($credentials)) {
            $user = auth()->user();
            $token = $user->createToken($request->email)->accessToken;
            return response()->json(['token' => $token, 'id' => $user->id, 'name' => $user->name], 200);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    /**
     * Logout current close session
     * @param  Request $request [description]
     * @return [type]           [description]
     */
	public function logout(Request $request) : object
    {
        $request->user()->token()->revoke();
        return response()->json(['message' => 'Successfully logged out'], 200);
    }

    /**
     * Display the loggued user information.
     *
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        return response()->json(['data' => $this->user->show(auth()->user()->id)], 200);
    }


}
