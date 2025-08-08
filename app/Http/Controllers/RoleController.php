<?php

// app/Http/Controllers/RoleController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\Facades\DataTables;  // Mengimpor DataTables

class RoleController extends Controller
{

    public function getRoles(Request $request)
    {
        $roles = Role::select('id', 'name', 'guard_name')->get();
    
        return DataTables::of($roles)
            ->addIndexColumn()
            ->addColumn('options', function ($role) {
                return '<button class="btn btn-primary btn-sm">Edit</button> <button class="btn btn-danger btn-sm">Delete</button>';
            })
            ->rawColumns(['options'])  // Pastikan menambahkan ini untuk kolom options
            ->make(true);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
           
            $roles = Role::select('id', 'name', 'guard_name')->get();

            return DataTables::of($roles)
                ->addIndexColumn()
                ->addColumn('options', function ($roles) {
                    return '
                        <button class="btn btn-primary btn-sm">Edit</button>
                        <button class="btn btn-danger btn-sm" onclick="confirmDelete(' . $roles->id . ')">Delete</button>
                    ';
                })
                ->rawColumns(['options'])  // Pastikan menambahkan ini untuk kolom options
                ->make(true);
        }
        return view('backend.setting.role.index');
    }
    
    

    // Menampilkan form untuk membuat role
    public function create()
    {
        $permissions = Permission::all();
        return view('roles.create', compact('permissions'));
    }

    // Menyimpan role baru
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name',
            'guard_name' => 'required|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        // Membuat role
        $role = Role::create([
            'name' => $request->name,
            'guard_name' => $request->guard_name,
        ]);

        // Menambahkan permissions ke role
        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return redirect()->route('roles.index')->with('success', 'Role created successfully.');
    }

    // Menampilkan form untuk mengedit role
    public function edit(Role $role)
    {
        $permissions = Permission::all();
        $rolePermissions = $role->permissions->pluck('id')->toArray();
        return view('roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    // Mengupdate role
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name,' . $role->id,
            'guard_name' => 'required|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        // Mengupdate role
        $role->update([
            'name' => $request->name,
            'guard_name' => $request->guard_name,
        ]);

        // Sinkronisasi permissions
        $role->syncPermissions($request->permissions);

        return redirect()->route('roles.index')->with('success', 'Role updated successfully.');
    }

    // Menghapus role
    public function destroy(Role $role)
    {
        $role->delete();
        return redirect()->route('roles.index')->with('success', 'Role deleted successfully.');
    }
}

