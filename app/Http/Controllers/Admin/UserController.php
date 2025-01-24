<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Admin;


class UserController extends Controller
{
    //
    public function show(Request $request)
    {
        return view('admin/users/show', [
            'users' => Admin::paginate(50),
        ]);
    }

    public function detail(Request $request, string $id)
    {
        return view('admin/users/admin/detail', ['user' => Admin::find($id)]);
    }
}
