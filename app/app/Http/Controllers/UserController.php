<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;

class UserController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(): object
    {
        $data = User::orderBy('id', 'DESC')->paginate(25);
        return $data;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request): object
    {
        $data = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);
        return $data;
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id): object
    {
        $data = User::findOrFail($id);
        return $data;
    }

}
