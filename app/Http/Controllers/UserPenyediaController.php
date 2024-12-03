<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserPenyedia;
use App\Models\UserAdmin;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class UserPenyediaController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $penyedias = UserPenyedia::with([
                'user:id,name,email,whatsapp',
                'user.roles:id,name'// Ambil data role terkait dengan kolom tertentu
            ]) // Ambil data admin dengan user terkait
                ->select('id', 'user_id');

            if (!empty($request->search['value'])) {
            $searchValue = $request->search['value'];
            $penyedias->whereHas('user', function ($query) use ($searchValue) {
                $query->where('name', 'like', "%$searchValue%")
                      ->orWhere('email', 'like', "%$searchValue%")
                      ->orWhere('whatsapp', 'like', "%$searchValue%");
            });
        }

            return DataTables::of($penyedias)
                ->addIndexColumn()
                ->addColumn('user_name', function ($penyedia) {
                    return $penyedia->user ? $penyedia->user->name : 'N/A';
                })
                ->addColumn('email', function ($penyedia) {
                    return $penyedia->user ? $penyedia->user->email : 'N/A';
                })
                ->addColumn('whatsapp', function ($penyedia) {
                    return $penyedia->user ? $penyedia->user->whatsapp : 'N/A';
                })
                // ->addColumn('roles', function ($pencari) {
                //     // Menampilkan nama role
                //     if ($pencari->user && $pencari->user->roles->isNotEmpty()) {
                //         return $pencari->user->roles->pluck('name')->join(', ');
                //     }
                //     return 'N/A'; // Jika tidak ada role
                // })
                ->addColumn('options', function ($penyedia) {
                    // return '
                    //     <button class="btn btn-warning btn-sm" onclick="resetPassword(' . $pencari->id . ')">Reset Password</button>
                    //     <button class="btn btn-primary btn-sm" onclick="showEditModal(' . $pencari->id . ')">Edit</button>
                    //     <button class="btn btn-danger btn-sm" onclick="confirmDelete(' . $pencari->id . ')">Delete</button>
                    // ';
                    return '
                    <button class="btn btn-warning btn-sm" onclick="confirmReset(' . $penyedia->id . ')">Reset Password</button>
                    <button class="btn btn-danger btn-sm" onclick="confirmDelete(' . $penyedia->id . ')">Delete</button>
                ';
                })
                ->rawColumns(['options'])  // Pastikan menambahkan ini untuk kolom options
                ->make(true);
        }

        return view('backend.users.penyedia.index');
    }

    public function data(Request $request)
    {
        $id = Auth::user()->id; // Mendapatkan ID pengguna yang sedang login
        $userAdmin = UserAdmin::where('user_id', $id)->first(); // Mencari data UserAdmin berdasarkan user_id
        if ($request->ajax()) {


            $penyedias = UserPenyedia::with([
                'user:id,name,email,whatsapp',
                'user.roles:id,name',
                'provinsi:id,name',
                'kabkota:id,name',
                'kecamatan:id,name',
                'sektor:id,name',
            ]);
           

            // Filter for admin-kabkota or admin-kabkota-officer roles
            if (in_array(Auth::user()->roles[0]['name'], ['admin-kabkota', 'admin-kabkota-officer'])) {
                $penyedias->where('id_kota', $userAdmin->kabkota_id);
            }

            // Tambahkan filter pencarian
            if (!empty($request->search['value'])) {
                $searchValue = $request->search['value'];
                $penyedias->where(function ($query) use ($searchValue) {
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

            // Transform data using a helper function
            $penyedias = $penyedias->get()->map(function ($penyedia) {
                $penyedia->jenis_perusahaan = getFullCompanyType($penyedia->jenis_perusahaan);
                return $penyedia;
            });

            return DataTables::of($penyedias)
            ->addIndexColumn()
            ->rawColumns(['options']) // Pastikan menambahkan ini untuk kolom options
            ->make(true);
        }

        return view('backend.data.penyedia.index');
    }

    public function exportCsv(Request $request)
    {
        try {

            $id = Auth::user()->id; // Mendapatkan ID pengguna yang sedang login
            $userAdmin = UserAdmin::where('user_id', $id)->first(); // Mencari data UserAdmin berdasarkan user_id

            // Get the search parameter if available
            $search = $request->get('search', '');
    
            // Query the database based on the search parameter
            // $pencaris = UserPencari::with(['user', 'agama', 'provinsi', 'kabkota', 'kecamatan', 'pendidikan', 'jurusan'])
            $penyedias = UserPenyedia::with([
                'user:id,name,email,whatsapp',
                'user.roles:id,name',
                'provinsi:id,name',
                'kabkota:id,name',
                'kecamatan:id,name',
                'sektor:id,name',
            ])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->whereHas('user', function ($query) use ($search) {
                        $query->where('name', 'like', "%$search%")
                              ->orWhere('email', 'like', "%$search%")
                              ->orWhere('whatsapp', 'like', "%$search%");
                    })
                    ->orWhereHas('provinsi', function ($query) use ($search) {
                        $query->where('name', 'like', "%$search%");
                    })
                    ->orWhereHas('kabkota', function ($query) use ($search) {
                        $query->where('name', 'like', "%$search%");
                    })
                    ->orWhereHas('kecamatan', function ($query) use ($search) {
                        $query->where('name', 'like', "%$search%");
                    });
                });
            })
            ->when(in_array(Auth::user()->roles[0]['name'], ['admin-kabkota', 'admin-kabkota-officer']), function ($query) use ($userAdmin) {
                $query->where('id_kota', $userAdmin->kabkota_id);
            })
            ->get();
    
            // Prepare data to export
            $csvData = [];
            foreach ($penyedias as $penyedia) {
                $csvData[] = [
                    $penyedia->user->name ?? '',
                    $penyedia->user->email ?? '',
                    '"' . ($penyedia->user->whatsapp ?? '') . '"',  // Add quotes around the whatsapp
                    '"' . ($penyedia->nib ?? '') . '"',  // Add quotes around the ktp
                    $penyedia->sektor->name ?? '',
                    $penyedia->provinsi->name ?? '',
                    $penyedia->provinsi->name ?? '',
                    $penyedia->kabkota->name ?? '',
                    $penyedia->kecamatan->name ?? '',
                    $penyedia->alamat ?? '',
                    $penyedia->kodepos ?? '',
                    $penyedia->website ?? '',
                    $penyedia->telpon ?? '',
                    $penyedia->jenis_perusahaan ?? '',
                    $penyedia->jabatan ?? '',
                    $penyedia->created_at ?? ''
                ];
            }
    
            // Get the current date and time in the desired format
            $dateTime = now()->format('Y-m-d_H-i-s');

            // Prepare headers for the CSV response with a dynamic filename
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="data_penyedia_' . $dateTime . '.csv"',
            ];
    
            // CSV callback to write the data to the output
            $callback = function () use ($csvData) {
                $handle = fopen('php://output', 'w');
                
                // Add CSV headers
                fputcsv($handle, [
                    'Nama', 'Email', 'Whatsapp', 'NIB', 'Sektor', 'Provinsi', 'Kabkota', 'Kecamatan',
                    'Alamat', 'Kodepos', 'Website', 'Telepon', 'Jenis Perusahaan',
                    'PJ Akun (Jabatan)','Tanggal Daftar',
                ]);
    
                // Add data rows
                foreach ($csvData as $row) {
                    fputcsv($handle, $row, ';');  // Set separator to ':'
                    // fputcsv($handle, $row); // Set separator to ','
                }
    
                fclose($handle);
            };
    
            // Return the response with the stream
            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            Log::error("Export CSV failed: " . $e->getMessage());
            return response()->json(['error' => 'An error occurred while generating the CSV file.'], 500);
        }
    }    


    public function softdelete($id)
    {
        try {
            // Cari admin berdasarkan ID
            $admin = UserPenyedia::findOrFail($id);
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
            $admin = UserPenyedia::findOrFail($id);
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
