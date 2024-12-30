<?php
/**
 * 管理者用ページのコントローラ
 */
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        return view('admin.dashboard', [
            'user' => $request->user(),
        ]);
    }
}
