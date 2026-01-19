<?php

// app/Http/Controllers/RoleController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserAdmin;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{

        public function index(Request $request)
        {

            
            if ($request->ajax()) {

                $id = Auth::user()->id; // Mendapatkan ID pengguna yang sedang login
                $userAdmin = UserAdmin::where('user_id', $id)->first(); // Mencari data UserAdmin berdasarkan user_id

                if(Auth::user()->roles[0]['name'] == 'admin-kabkota'){

                    $admins = UserAdmin::with([
                        'user:id,name,email,whatsapp',
                        'user.roles:id,name'// Ambil data role terkait dengan kolom tertentu
                    ]) // Ambil data admin dengan user terkait 
                    ->whereNotNull('kabkota_id') // Kondisi whereNotNull
                    ->where('kabkota_id',  $userAdmin->kabkota_id) // Tambahkan kondisi where
                    ->select('id', 'user_id',  'jabatan', 'province_id', 'kabkota_id', 'kecamatan_id', 'created_by', 'updated_by', 'is_deleted'); 
                    
                }else if(Auth::user()->roles[0]['name'] == 'admin-provinsi' || Auth::user()->roles[0]['name'] == 'super-admin'){

                    $admins = UserAdmin::with([
                        'user:id,name,email,whatsapp',
                        'user.roles:id,name'// Ambil data role terkait dengan kolom tertentu
                    ]) // Ambil data admin dengan user terkait 
                    ->select('id', 'user_id', 'province_id', 'kabkota_id', 'kecamatan_id', 'jabatan',  'created_by', 'updated_by', 'is_deleted'); 

                }
    
                // Tambahkan filter manual jika ada pencarian
                if (!empty($request->search['value'])) {
                    $searchValue = $request->search['value'];
                    $admins->whereHas('user', function ($query) use ($searchValue) {
                        $query->where('name', 'like', "%$searchValue%")
                            ->orWhere('email', 'like', "%$searchValue%")
                            ->orWhere('whatsapp', 'like', "%$searchValue%");
                    });
                    $admins->orWhereHas('user.roles', function ($query) use ($searchValue) {
                        $query->where('name', 'like', "%$searchValue%");
                    });
                }

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

                     ->addColumn('jabatan', function ($admin) {
                        return $admin->jabatan;
                    })
                    ->addColumn('roles', function ($admin) {
                        // Menampilkan nama role
                        if ($admin->user && $admin->user->roles->isNotEmpty()) {
                            return $admin->user->roles->pluck('name')->join(', ');
                        }
                        return 'N/A'; // Jika tidak ada role
                    })
                    ->addColumn('kabkota', function ($admin) {
                        // Menampilkan nama kabkota
                        return $admin->kabkota ? $admin->kabkota->name : 'N/A';
                    })
                    ->addColumn('kecamatan', function ($admin) {
                        // Menampilkan nama kabkota
                        return $admin->kecamatan ? $admin->kecamatan->name : 'N/A';
                    })
                    ->addColumn('options', function ($admin) {
                        return '
                            <button class="btn btn-warning btn-sm" onclick="confirmReset(' . $admin->id . ')">Reset Password</button>
                            <button class="btn btn-primary btn-sm" onclick="showEditModal(' . $admin->id . ')">Edit</button>
                            <button class="btn btn-danger btn-sm" onclick="confirmDelete(' . $admin->id . ')">Delete</button>
                        ';
                    })
                    ->rawColumns(['options'])  // Pastikan menambahkan ini untuk kolom options
                    ->make(true);
            }

            // Ambil data roles untuk dikirim ke view
            // $roles = Role::select('id', 'name')->whereIn('name', ['super-admin', 'admin-provinsi', 'admin-kabkota-officer', 'admin-kabkota'])->get();
            $roles = Role::select('id', 'name')->whereNotIn('name', ['pencari-kerja', 'penyedia-kerja', 'admin-bkk'])->get();
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
                'password' => bcrypt($request->email) // Set password default atau sesuai logika Anda
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
                'kecamatan_id' => $request->kecamatan_id,
                'jabatan' => $request->jabatan,
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
                    ->select('id', 'user_id', 'province_id', 'kabkota_id', 'jabatan', 'kecamatan_id', 'created_by', 'updated_by', 'is_deleted')
                    ->findOrFail($id);

                return response()->json(['success' => true, 'data' => $admin]);
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
            }
        }

        public function update(Request $request, $id)
        {
            try {
                // Validasi untuk 'name'
                $request->validate([
                    'name' => 'required|string|max:255',
                ]);

                $admin = UserAdmin::findOrFail($id);
                $user = User::findOrFail($admin->user_id);

                // Validasi email jika berubah
                if ($user->email != $request->email) {
                    $request->validate([
                        'email' => 'required|email|max:255|unique:users,email,' . $user->id,
                    ]);
                }

                // Validasi WhatsApp jika berubah
                if ($user->whatsapp != $request->whatsapp) {
                    $request->validate([
                        'whatsapp' => 'required|string|unique:users,whatsapp|max:15',
                    ]);
                }

                // Validasi untuk 'role_id'
                $request->validate([
                    'role_id' => 'required|exists:roles,id',
                ]);

                // Validasi 'kabkota_id' hanya jika role_id == 4
                if ($request->role_id == 4) {
                    // $request->validate([
                    //     'kabkota_id' => 'required|exists:kabkota,id',
                    // ]);
                }

                // Update data pada tabel users
                $user->update([
                    'name' => $request->name,
                    'email' => $request->email,
                    'whatsapp' => $request->whatsapp,
                    'jabatan' => $request->jabatan,
                ]);

                // Perbarui role untuk user terkait
                $user->roles()->sync([$request->role_id]);

                // Update data kabkota_id pada tabel user_admins jika role_id == 4
                if ($request->role_id == 4) {
                    $admin->kabkota_id = $request->kabkota_id;
                } else {
                    $admin->kabkota_id = null; // Hapus kabkota jika role bukan 4
                }

                $admin->save();

                return response()->json(['success' => true, 'message' => 'Update data berhasil.']);
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

        public function reset($id)
        {
            try {

                $admin = UserAdmin::findOrFail($id);
    
                $user = User::where('id', $admin->user_id)->first();  // Sesuaikan relasi dengan tabel User jika ada
                if ($user) {
                    $user->update([
                        'password' => bcrypt($user->email),
                    ]);
                }

                return response()->json(['success' => true, 'message' => 'Reset data berhasil']);
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
            }
        }

   
}

