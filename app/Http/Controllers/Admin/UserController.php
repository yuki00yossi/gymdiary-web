<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;


class UserController extends Controller
{
    //
    public function show(Request $request)
    {
        return view('admin/users/show', [
            'users' => User::paginate(50),
        ]);
    }

    public function detail(Request $request, string $id)
    {
        return view('admin/users/user/detail', ['user' => User::find($id)]);
    }
}
