<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserPencari;
use App\Models\UserAdmin;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

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

        $id = Auth::user()->id; // Mendapatkan ID pengguna yang sedang login
        $userAdmin = UserAdmin::where('user_id', $id)->first(); // Mencari data UserAdmin berdasarkan user_id

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


            //Filter for admin-kabkota role
            if (Auth::user()->roles[0]['name'] === 'admin-kabkota' || Auth::user()->roles[0]['name'] === 'admin-kabkota-officer') {
                $pencaris->where('id_kota', $userAdmin->kabkota_id);
            }

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
            ->editColumn('disabilitas', function ($pencari) {
                $disbb = '-';
                if($pencari->disabilitas == 1){
                    $disbb = 'Ya';
                }else{
                    $disbb = 'Tidak';
                }
                return $disbb;
            })
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

    public function exportCsv(Request $request)
    {
        try {

            $id = Auth::user()->id; // Mendapatkan ID pengguna yang sedang login
            $userAdmin = UserAdmin::where('user_id', $id)->first(); // Mencari data UserAdmin berdasarkan user_id

            // Get the search parameter if available
            $search = $request->get('search', '');

            // Query the database based on the search parameter
            // $pencaris = UserPencari::with(['user', 'agama', 'provinsi', 'kabkota', 'kecamatan', 'pendidikan', 'jurusan'])
            $pencaris = UserPencari::with([
                'user:id,name,email,whatsapp',
                'user.roles:id,name',
                'provinsi:id,name',
                'kabkota:id,name',
                'kecamatan:id,name',
                'pendidikan:id,name',
                'jurusan:id,nama',
                'agama:id,name'
            ])
            ->where(function ($query) use ($search) {  // Use $search here
                // Filter berdasarkan user
                $query->whereHas('user', function ($query) use ($search) {
                    $query->where('name', 'like', "%$search%")
                          ->orWhere('email', 'like', "%$search%")
                          ->orWhere('whatsapp', 'like', "%$search%");
                })
                // Filter berdasarkan provinsi
                ->orWhereHas('provinsi', function ($query) use ($search) {
                    $query->where('name', 'like', "%$search%");
                })
                // Filter berdasarkan kabkota
                ->orWhereHas('kabkota', function ($query) use ($search) {
                    $query->where('name', 'like', "%$search%");
                })
                // Filter berdasarkan kecamatan
                ->orWhereHas('kecamatan', function ($query) use ($search) {
                    $query->where('name', 'like', "%$search%");
                });
            });

             // Filter for admin-kabkota or admin-kabkota-officer roles
             if (in_array(Auth::user()->roles[0]['name'], ['admin-kabkota', 'admin-kabkota-officer'])) {
                $pencaris->where('id_kota', $userAdmin->kabkota_id);
            }

            $pencaris = $pencaris->get(); // Eksekusi query


            // Prepare data to export
            $csvData = [];
            foreach ($pencaris as $pencari) {
                $csvData[] = [
                    $pencari->user->name ?? '',
                    $pencari->user->email ?? '',
                    '"' . ($pencari->user->whatsapp ?? '') . '"',  // Add quotes around the whatsapp
                    '"' . ($pencari->ktp ?? '') . '"',  // Add quotes around the ktp
                    $pencari->tempat_lahir ?? '',
                    $pencari->tanggal_lahir ?? '',
                    $pencari->gender ?? '',
                    $pencari->id_status_perkawinan ?? '',
                    $pencari->agama->name ?? '',
                    $pencari->provinsi->name ?? '',
                    $pencari->kabkota->name ?? '',
                    $pencari->kecamatan->name ?? '',
                    $pencari->alamat ?? '',
                    $pencari->kodepos ?? '',
                    $pencari->pendidikan->name ?? '',
                    $pencari->jurusan->nama ?? '',
                    $pencari->tahun_lulus ?? '',
                    $pencari->medsos ?? '',
                    $pencari->is_diterima ?? '',
                    $pencari->created_at ?? '',
                ];
            }

            // Get the current date and time in the desired format
            $dateTime = now()->format('Y-m-d_H-i-s');

            // Prepare headers for the CSV response with a dynamic filename
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="data_pencari_' . $dateTime . '.csv"',
            ];

            // CSV callback to write the data to the output
            $callback = function () use ($csvData) {
                $handle = fopen('php://output', 'w');

                // Add CSV headers
                fputcsv($handle, [
                    'Nama', 'Email', 'Whatsapp', 'NIK', 'Tempat Lahir', 'Tanggal Lahir',
                    'Gender', 'Status Perkawinan', 'Agama', 'Provinsi', 'Kabkota', 'Kecamatan',
                    'Alamat', 'Kodepos', 'Pendidikan Terakhir', 'Jurusan', 'Tahun Lulus',
                    'Medsos', 'Status Kerja', 'Tanggal Daftar'
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
