<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $roles = Role::query()->select('id', 'name', 'guard_name', 'created_at');

            return DataTables::of($roles)
                ->addIndexColumn()
                ->editColumn('name', function ($role) {
                    return str_replace('-', ' ', $role->name);
                })
                ->addColumn('created_at_fmt', function ($role) {
                    return optional($role->created_at)->format('d-m-Y H:i');
                })
                ->make(true);
        }

        return view('backend.setting.role.index');
    }
}
