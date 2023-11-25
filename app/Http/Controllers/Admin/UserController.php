<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;


class UserController extends Controller
{
    public function index(Request $request){

        $users = User::latest()->get();

        return $users;

    }

    public function store(Request $request){

        $request->merge(['password' =>  bcrypt($request->password)]);

        $user = User::create($request->all());

        return $user;
    }
}
