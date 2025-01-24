<?php
/**
 * 管理者用ページのコントローラ
 */
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Admin;


class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        return view('admin.dashboard', [
            'user' => $request->user(),
        ]);
    }

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
