<?php

// app/Http/Controllers/RoleController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserAdmin;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $admins = UserAdmin::with([
                'user:id,name,email,whatsapp',
                'user.roles:id,name'// Ambil data role terkait dengan kolom tertentu
            ]) // Ambil data admin dengan user terkait
                        ->select('id', 'user_id', 'province_id', 'kabkota_id', 'kecamatan_id', 'created_by', 'updated_by', 'is_deleted')
                        ;

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
                ->addColumn('roles', function ($admin) {
                    // Menampilkan nama role
                    if ($admin->user && $admin->user->roles->isNotEmpty()) {
                        return $admin->user->roles->pluck('name')->join(', ');
                    }
                    return 'N/A'; // Jika tidak ada role
                })
                ->addColumn('options', function ($admin) {
                    return '
                        <button class="btn btn-primary btn-sm" onclick="showEditModal(' . $admin->id . ')">Edit</button>
                        <button class="btn btn-danger btn-sm" onclick="confirmDelete(' . $admin->id . ')">Delete</button>
                    ';
                })
                ->rawColumns(['options'])  // Pastikan menambahkan ini untuk kolom options
                ->make(true);
        }

         // Ambil data roles untuk dikirim ke view
        $roles = Role::select('id', 'name')->whereIn('name', ['super-admin', 'admin-provinsi', 'pimpinan', 'admin-kabkota'])->get();
        return view('backend.users.admin.index',  compact('roles'));
    }

        // Method untuk menyimpan data user baru
        public function store(Request $request)
        {
            $userId = auth()->user()->id;
            // Validasi input
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'whatsapp' => 'required|string|unique:users|max:15',
                'role_id' => 'required|exists:roles,id',
            ]);
    
            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()]);
            }
            
    
            // Menyimpan data ke tabel users
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'whatsapp' => $request->whatsapp,
                'password' => bcrypt($request->name), // Set password default atau sesuai logika Anda
                'kabkota_id' => $request->kabkota_id, // Set password default atau sesuai logika Anda
            ]);
    
            // Menambahkan role ke user
            $role = Role::find($request->role_id);
            $user->assignRole($role);
    
            // Menyimpan ke tabel user_admin jika diperlukan
            UserAdmin::create([
                'user_id' => $user->id,
                // 'role_id' => $role->id,
                'province_id' => 64,
                'kabkota_id' => $request->kabkota_id,
                'created_by' => $userId,
                'updated_by' => $userId
                // Tambahkan kolom lainnya jika diperlukan
            ]);
    
            return response()->json(['success' => true]);
        }

        public function getAdmin($id)
        {
            try {
                $admin = UserAdmin::with([
                        'user:id,name,email,whatsapp',
                        'user.roles:id,name'// Ambil data role terkait dengan kolom tertentu
                    ])
                    ->select('id', 'user_id', 'province_id', 'kabkota_id', 'kecamatan_id', 'created_by', 'updated_by', 'is_deleted')
                    ->findOrFail($id);

                return response()->json(['success' => true, 'data' => $admin]);
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
            }
        }

        public function update(Request $request, $id)
        {
            try {

                //Validasi untuk 'name'
                $request->validate([
                    'name' => 'required|string|max:255',
                ]);

                $admin = UserAdmin::findOrFail($id);
                $user = User::findOrFail($admin->user_id);

                if($user->email != $request->email){
                     // Validasi untuk 'email'
                    $request->validate([
                        'email' => 'required|email|max:255|unique:users,email,' . $id, // Pastikan email unik kecuali untuk user ini
                    ]);

                }


                if($user->whatsapp != $request->whatsapp ){
                    // Validasi untuk 'wa'
                    $request->validate([
                        'whatsapp' => 'required|string|unique:users|max:15', // Sesuaikan dengan format whatsapp
                    ]);

               }

                // Validasi untuk 'role_id'
                $request->validate([
                    'role_id' => 'required|exists:roles,id', // Pastikan role ada di tabel roles
                ]);
                

                // Cari admin berdasarkan ID
                // $admin = UserAdmin::findOrFail($id);

                // Perbarui data user terkait (user yang memiliki ID user_id di UserAdmin)
                $user = $admin->user;  // Ambil user yang terkait dengan admin ini
                $user->update([
                    'name' => $request->name,
                    'email' => $request->email,
                    'whatsapp' => $request->whatsapp,
                ]);

                // Perbarui role untuk user terkait
                $user->roles()->sync([$request->role_id]);  // Sinkronkan role baru dengan user

                return response()->json(['success' => true, 'message' => 'update data berhasil.']);
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
            }
        }

        public function softdelete($id)
        {
            try {
                // Cari admin berdasarkan ID
                $admin = UserAdmin::findOrFail($id);
    
                // Set is_deleted = 1 untuk soft delete admin
                $admin->is_deleted = 1;
                $admin->save();  // Simpan perubahan
                $admin->delete();
    
                // Soft delete juga user yang terkait dengan admin ini (misalnya, jika memiliki relasi)
                $user = User::where('id', $admin->user_id)->first();  // Sesuaikan relasi dengan tabel User jika ada
                if ($user) {
                    // Set is_deleted = 1 untuk soft delete user
                    $user->is_deleted = 1;
                    $user->save();  // Simpan perubahan
                    $user->delete(); 
                }
    
                return response()->json(['success' => true, 'message' => 'Hapus data berhasil']);
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
            }
        }

   
}

