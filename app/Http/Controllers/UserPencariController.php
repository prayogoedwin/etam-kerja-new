<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserPencari;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class UserPencariController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $pencaris = UserPencari::with([
                'user:id,name,email,whatsapp',
                'user.roles:id,name'// Ambil data role terkait dengan kolom tertentu
            ]) // Ambil data admin dengan user terkait
                ->select('id', 'user_id');

                    // Tambahkan filter pencarian
        if (!empty($request->search['value'])) {
            $searchValue = $request->search['value'];
            $pencaris->whereHas('user', function ($query) use ($searchValue) {
                $query->where('name', 'like', "%$searchValue%")
                      ->orWhere('email', 'like', "%$searchValue%")
                      ->orWhere('whatsapp', 'like', "%$searchValue%");
            });
        }


            return DataTables::of($pencaris)
                ->addIndexColumn()
                ->addColumn('user_name', function ($pencari) {
                    return $pencari->user ? $pencari->user->name : 'N/A';
                })
                ->addColumn('email', function ($pencari) {
                    return $pencari->user ? $pencari->user->email : 'N/A';
                })
                ->addColumn('whatsapp', function ($pencari) {
                    return $pencari->user ? $pencari->user->whatsapp : 'N/A';
                })
                // ->addColumn('roles', function ($pencari) {
                //     // Menampilkan nama role
                //     if ($pencari->user && $pencari->user->roles->isNotEmpty()) {
                //         return $pencari->user->roles->pluck('name')->join(', ');
                //     }
                //     return 'N/A'; // Jika tidak ada role
                // })
                ->addColumn('options', function ($pencari) {
                    // return '
                    //     <button class="btn btn-warning btn-sm" onclick="resetPassword(' . $pencari->id . ')">Reset Password</button>
                    //     <button class="btn btn-primary btn-sm" onclick="showEditModal(' . $pencari->id . ')">Edit</button>
                    //     <button class="btn btn-danger btn-sm" onclick="confirmDelete(' . $pencari->id . ')">Delete</button>
                    // ';
                    return '
                    <button class="btn btn-warning btn-sm" onclick="confirmReset(' . $pencari->id . ')">Reset Password</button>
                    <button class="btn btn-danger btn-sm" onclick="confirmDelete(' . $pencari->id . ')">Delete</button>
                ';
                })
                ->rawColumns(['options'])  // Pastikan menambahkan ini untuk kolom options
                ->make(true);
        }

        return view('backend.users.pencari.index');
    }

    public function data(Request $request)
    {
        if ($request->ajax()) {
            $pencaris = UserPencari::with([
                'user:id,name,email,whatsapp',
                'user.roles:id,name',
                'provinsi:id,name',
                'kabkota:id,name',
                'kecamatan:id,name',
                'pendidikan:id,name',
                'jurusan:id,nama',
                'agama:id,name'
            ]);
    
            // Tambahkan filter pencarian
            if (!empty($request->search['value'])) {
                $searchValue = $request->search['value'];
                $pencaris->where(function ($query) use ($searchValue) {
                    // Filter berdasarkan user
                    $query->whereHas('user', function ($query) use ($searchValue) {
                        $query->where('name', 'like', "%$searchValue%")
                              ->orWhere('email', 'like', "%$searchValue%")
                              ->orWhere('whatsapp', 'like', "%$searchValue%");
                    })
                    // Filter berdasarkan provinsi
                    ->orWhereHas('provinsi', function ($query) use ($searchValue) {
                        $query->where('name', 'like', "%$searchValue%");
                    })

                    // Filter berdasarkan provinsi
                    ->orWhereHas('kabkota', function ($query) use ($searchValue) {
                        $query->where('name', 'like', "%$searchValue%");
                    })

                     // Filter berdasarkan provinsi
                     ->orWhereHas('kecamatan', function ($query) use ($searchValue) {
                        $query->where('name', 'like', "%$searchValue%");
                    });
                });
            }
    
            return DataTables::of($pencaris)
            ->addIndexColumn()
            ->addColumn('options', function ($pencari) {
                return '
                    <button class="btn btn-primary btn-sm" 
                        onclick="window.location.href=\'' . url('dapur/ak1/existing') . '?ktp=' . $pencari->ktp . '\'">
                        Edit
                    </button>
                ';
            })
            ->rawColumns(['options']) // Pastikan menambahkan ini untuk kolom options
            ->make(true);
        

        }
    
        return view('backend.data.pencari.index');
    }

    public function softdelete($id)
    {
        try {
            // Cari admin berdasarkan ID
            $admin = UserPencari::findOrFail($id);
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
            $admin = UserPencari::findOrFail($id);
            $user = User::findOrFail($admin->user_id);

            $user = $admin->user;  // Ambil user yang terkait dengan admin ini
            $user->update([
                'password' => bcrypt($user->email),
            ]);

            return response()->json(['success' => true, 'message' => 'Reset data berhasil']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }


    

}
?>
