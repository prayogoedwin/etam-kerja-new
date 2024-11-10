<?php

// app/Http/Controllers/RoleController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserAdmin;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class AdminController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $admins = UserAdmin::with('user:id,name,email,whatsapp')  // Ambil data admin dengan user terkait
                        ->select('id', 'user_id', 'province_id', 'kabkota_id', 'kecamatan_id', 'created_by', 'updated_by', 'is_deleted');

            return DataTables::of($admins)
                ->addIndexColumn()
                ->addColumn('user_name', function ($admin) {
                    return $admin->user ? $admin->user->name : 'N/A';
                })
                ->addColumn('email', function ($admin) {
                    return $admin->user ? $admin->user->email : 'N/A';
                })
                ->addColumn('whatsapp', function ($admin) {
                    return $admin->user ? $admin->user->whatsapp : 'N/A';
                })
                ->addColumn('options', function ($admin) {
                    return '
                        <button class="btn btn-primary btn-sm">Edit</button>
                        <button class="btn btn-danger btn-sm" onclick="confirmDelete(' . $admin->id . ')">Delete</button>
                    ';
                })
                ->rawColumns(['options'])  // Pastikan menambahkan ini untuk kolom options
                ->make(true);
        }

         // Ambil data roles untuk dikirim ke view
        $roles = Role::select('id', 'name')->whereIn('id', [1, 3, 4, 8])->get();
        return view('backend.users.admin.index',  compact('roles'));
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

