<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserBkk;
use App\Models\User;
use App\Models\UserAdmin;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class UserBkkController extends Controller
{
    public function index(Request $request)
    {

        $user = Auth::user();
        $role = $user->roles->first()->name ?? null;

        $userAdmin = UserAdmin::where('user_id', $user->id)->first();

        if ($request->ajax()) {
            $pencaris = UserBkk::with([
                'user:id,name,email,whatsapp',
                'user.roles:id,name'// Ambil data role terkait dengan kolom tertentu
            ]) // Ambil data admin dengan user terkait
            ->select('id', 'user_id')
            ->when(
                in_array($role, ['admin-kabkota', 'admin-kabkota-officer']) && $userAdmin,
                fn ($q) => $q->where('id_kota', $userAdmin->kabkota_id)
            )
            ->get();


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
                ->addColumn('options', function ($pencari) {
                    return '
                    <button class="btn btn-warning btn-sm" onclick="confirmReset(' . $pencari->id . ')">Reset Password</button>
                    <button class="btn btn-danger btn-sm" onclick="confirmDelete(' . $pencari->id . ')">Delete</button>
                ';
                })
                ->rawColumns(['options'])  // Pastikan menambahkan ini untuk kolom options
                ->make(true);
        }

        return view('backend.users.bkk.index');
    }

    public function softdelete($id)
    {
        try {
            // Cari admin berdasarkan ID
            $admin = UserBkk::findOrFail($id);
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
            $admin = UserBkk::findOrFail($id);
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
