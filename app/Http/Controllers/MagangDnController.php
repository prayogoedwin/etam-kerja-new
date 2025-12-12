<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EtamMagangDn;
use App\Models\EtamMagangDnPerush;
use App\Models\User;

use App\Models\Lowongan;
use App\Models\Jabatan;
use App\Models\Sektor;
use App\Models\Pendidikan;
use App\Models\Jurusan;
use App\Models\Kabkota;
// use App\Models\Marital;

use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MagangDnController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Mendapatkan ID pengguna yang sedang login
        if ($request->ajax()) {

        $user = Auth::user();

        $magangs = EtamMagangDn::with([
            'penyelenggaraUser:id,name,email',
            'verifikator:id,name',
            'creator:id,name'
        ])
        ->where('deleted_at', NULL)
        ->orderBy('created_at', 'desc') // atau column lain yang mau di-sort
        ->select('*');

        // Tambahkan filter pencarian
        if (!empty($request->search['value'])) {
            $searchValue = $request->search['value'];
            $magangs->where(function ($query) use ($searchValue) {
                $query->where('nama_magang', 'like', "%$searchValue%")
                    ->orWhere('penyelenggara', 'like', "%$searchValue%")
                    ->orWhere('lokasi_penyelenggaraan', 'like', "%$searchValue%")
                    ->orWhereHas('penyelenggaraUser', function ($q) use ($searchValue) {
                        $q->where('name', 'like', "%$searchValue%");
                    });
            });
        }

        if ($user->roles[0]['name'] == 'pencari-kerja'){
            $magangs->where('status_verifikasi', 1);
            $magangs->where('status', 1);
        }

        if ($user->roles[0]['name'] == 'penyedia-kerja'){
            $magangs->where('status_verifikasi', 1);
            $magangs->where('status', 1);
        }

        if ($user->roles[0]['name'] == 'admin-kabkota'){
            $magangs->where('id_penyelenggara', $user->id);
        }

        return DataTables::of($magangs)
            ->addIndexColumn()
            ->addColumn('jenis_penyelenggara_text', function ($magang) {
                return $magang->jenis_penyelenggara == 0 ? 'Pemerintah' : 'Swasta';
            })
            ->addColumn('tipe_magang_text', function ($magang) {
                return $magang->tipe_magang == 0 ? 'Online' : 'Offline';
            })
            ->addColumn('tipe_partnership_text', function ($magang) {
                return $magang->tipe_partnership == 0 ? 'Tertutup' : 'Open';
            })
            ->addColumn('status_verifikasi_badge', function ($magang) {
                if ($magang->status_verifikasi == 1) {
                    return '<span class="badge bg-success">Terverifikasi</span>';
                } else {
                    return '<span class="badge bg-warning">Belum Verifikasi</span>';
                }
            })
            ->addColumn('status_badge', function ($magang) {
                if ($magang->status == 1) {
                    return '<span class="badge bg-success">Aktif</span>';
                } else {
                    return '<span class="badge bg-secondary">Tidak Aktif</span>';
                }
            })
            ->editColumn('tanggal_mulai', function ($magang) {
                return $magang->tanggal_mulai ? $magang->tanggal_mulai->format('d M Y') : '-';
            })
            ->editColumn('tanggal_selesai', function ($magang) {
                return $magang->tanggal_selesai ? $magang->tanggal_selesai->format('d M Y') : '-';
            })
            ->addColumn('options', function ($magang) use ($user) {
                $html = '<div class="btn-group-vertical" role="group">';

                if ($user->roles[0]['name'] == 'pencari-kerja'){

                    // Button Detail
                    $html .= '<button class="btn btn-info btn-sm mb-1" onclick="showDetailModal(' . $magang->id . ')">Detail</button>';
                    $html .= '
                    <form action="' . route('magang_dn.perusahaan', $magang->id) . '" method="GET" style="display:inline;">
                        <button type="submit" class="btn btn-primary btn-sm mb-1 w-100">🏢 Lihat Perusahaan</button>
                    </form>
                    ';

                }

                if ($user->roles[0]['name'] == 'penyedia-kerja'){

                    // Button Detail
                    $html .= '<button class="btn btn-info btn-sm mb-1" onclick="showDetailModal(' . $magang->id . ')">Detail</button>';
                    $html .= '
                    <form action="' . route('magang_dn.perusahaan', $magang->id) . '" method="GET" style="display:inline;">
                        <button type="submit" class="btn btn-primary btn-sm mb-1 w-100">🏢 Lihat Keikutsertaan</button>
                    </form>
                    ';

                }

                if ($user->roles[0]['name'] == 'admin-provinsi' || $user->roles[0]['name'] == 'super-admin'){

                        // Button Detail
                    $html .= '<button class="btn btn-info btn-sm mb-1" onclick="showDetailModal(' . $magang->id . ')">Detail</button>';

                    // Button Edit
                    $html .= '<button class="btn btn-primary btn-sm mb-1" onclick="showEditModal(' . $magang->id . ')">Edit</button>';

                    // Button Verifikasi / Batal Verifikasi - HANYA untuk super-admin dan admin-provinsi
                    $user = Auth::user();
                    $allowedRoles = ['super-admin', 'admin-provinsi'];
                    $userRoles = $user->roles->pluck('name')->toArray();
                    $hasVerifyPermission = !empty(array_intersect($allowedRoles, $userRoles));

                    if ($hasVerifyPermission) {
                        if ($magang->status_verifikasi == 0) {
                            // Belum diverifikasi - tampilkan tombol Verifikasi
                            $html .= '<form action="' . route('magang_dn.verifikasi', $magang->id) . '" method="POST" style="display:inline;" onsubmit="return confirm(\'Yakin ingin verifikasi magang ini?\')">
                                ' . csrf_field() . '
                                <button type="submit" class="btn btn-success btn-sm mb-1 w-100">✓ Verifikasi</button>
                            </form>';
                        } else {
                            // Sudah diverifikasi - tampilkan tombol Batal Verifikasi
                            $html .= '<form action="' . route('magang_dn.unverifikasi', $magang->id) . '" method="POST" style="display:inline;" onsubmit="return confirm(\'Yakin ingin membatalkan verifikasi?\')">
                                ' . csrf_field() . '
                                <button type="submit" class="btn btn-warning btn-sm mb-1 w-100">✗ Batal Verifikasi</button>
                            </form>';
                        }
                    }

                    $html .= '
                        <form action="' . route('magang_dn.perusahaan', $magang->id) . '" method="GET" style="display:inline;">
                            <button type="submit" class="btn btn-primary btn-sm mb-1 w-100">🏢 Lihat Perusahaan</button>
                        </form>
                    ';


                    // Button Delete
                    $html .= '<form action="' . route('magang_dn.destroy', $magang->id) . '" method="POST" style="display:inline;" onsubmit="return confirm(\'Yakin ingin menghapus?\')">
                        ' . csrf_field() . '
                        ' . method_field('DELETE') . '
                        <button type="submit" class="btn btn-danger btn-sm w-100">Delete</button>
                    </form>';

                }

                if ($user->roles[0]['name'] == 'admin-kabkota' || Auth::user()->roles[0]['name'] == 'admin-kabkota-officer'){

                    // Button Detail
                    $html .= '<button class="btn btn-info btn-sm mb-1" onclick="showDetailModal(' . $magang->id . ')">Detail</button>';
                    $html .= '
                    <form action="' . route('magang_dn.perusahaan', $magang->id) . '" method="GET" style="display:inline;">
                        <button type="submit" class="btn btn-primary btn-sm mb-1 w-100">🏢 Lihat Perusahaan</button>
                    </form>
                    ';

                }



                $html .= '</div>';

                return $html;
            })
            ->rawColumns(['options', 'status_verifikasi_badge', 'status_badge'])
            ->make(true);
        }

        return view('backend.magang_dn.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validasi input
            $validated = $request->validate([
                'jenis_penyelenggara' => 'required|integer|in:0,1',
                'nama_magang' => 'required|string|max:255',
                'penyelenggara' => 'nullable|string|max:255',
                'deskripsi' => 'nullable|string',
                'poster' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'tipe_magang' => 'required|integer|in:0,1',
                'kota' => 'required|string|max:10',
                'lokasi_penyelenggaraan' => 'nullable|string|max:255',
                'tanggal_open_pendaftaran_tenant' => 'nullable|date',
                'tipe_partnership' => 'required|integer|in:0,1',
                'tanggal_close_pendaftaran_tenant' => 'nullable|date',
                'tanggal_mulai' => 'nullable|date',
                'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
                'status' => 'required|integer|in:0,1',
            ]);

            // Generate slug
            $slug = Str::slug($request->nama_magang);
            $slugCount = EtamMagangDn::where('slug', 'like', $slug . '%')->count();
            if ($slugCount > 0) {
                $slug = $slug . '-' . ($slugCount + 1);
            }

            // Upload poster jika ada
            $posterPath = null;
            if ($request->hasFile('poster')) {
                $posterPath = $request->file('poster')->store('magang-posters', 'public');
            }

            // Create magang
            $magang = EtamMagangDn::create([
                'jenis_penyelenggara' => $request->jenis_penyelenggara,
                'id_penyelenggara' => Auth::id(),
                'nama_magang' => $request->nama_magang,
                'slug' => $slug,
                'penyelenggara' => $request->penyelenggara,
                'deskripsi' => $request->deskripsi,
                'poster' => $posterPath,
                'tipe_magang' => $request->tipe_magang,
                'kota' => $request->kota,
                'lokasi_penyelenggaraan' => $request->lokasi_penyelenggaraan,
                'tanggal_open_pendaftaran_tenant' => $request->tanggal_open_pendaftaran_tenant,
                'tipe_partnership' => $request->tipe_partnership,
                'tanggal_close_pendaftaran_tenant' => $request->tanggal_close_pendaftaran_tenant,
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai,
                'status_verifikasi' => 0,
                'status' => $request->status,
                'created_by' => Auth::id(),
            ]);

            return redirect()->route('magang_dn.index')->with('success', 'Magang berhasil ditambahkan!');
        } catch (\Exception $e) {
            Log::error("Error creating magang dn: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $magang = EtamMagangDn::with([
                'penyelenggaraUser:id,name,email',
                'verifikator:id,name',
                'creator:id,name',
                'updater:id,name'
            ])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $magang
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $magang = EtamMagangDn::findOrFail($id);

            // Validasi input
            $validated = $request->validate([
                'jenis_penyelenggara' => 'required|integer|in:0,1',
                'nama_magang' => 'required|string|max:255',
                'penyelenggara' => 'nullable|string|max:255',
                'deskripsi' => 'nullable|string',
                'poster' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'tipe_magang' => 'required|integer|in:0,1',
                'kota' => 'required|string|max:10',
                'lokasi_penyelenggaraan' => 'nullable|string|max:255',
                'tanggal_open_pendaftaran_tenant' => 'nullable|date',
                'tipe_partnership' => 'required|integer|in:0,1',
                'tanggal_close_pendaftaran_tenant' => 'nullable|date',
                'tanggal_mulai' => 'nullable|date',
                'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
                'status' => 'required|integer|in:0,1',
            ]);

            // Generate slug jika nama job fair berubah
            $slug = $magang->slug;
            if ($request->nama_magang !== $magang->nama_magang) {
                $slug = Str::slug($request->nama_magang);
                $slugCount = EtamMagangDn::where('slug', 'like', $slug . '%')
                    ->where('id', '!=', $id)
                    ->count();
                if ($slugCount > 0) {
                    $slug = $slug . '-' . ($slugCount + 1);
                }
            }

            // Upload poster baru jika ada
            $posterPath = $magang->poster;
            if ($request->hasFile('poster')) {
                // Hapus poster lama jika ada
                if ($posterPath && Storage::disk('public')->exists($posterPath)) {
                    Storage::disk('public')->delete($posterPath);
                }
                $posterPath = $request->file('poster')->store('job-fair-posters', 'public');
            }

            // Update job fair
            $magang->update([
                'jenis_penyelenggara' => $request->jenis_penyelenggara,
                'id_penyelenggara' => $request->id_penyelenggara ?? Auth::id(),
                'nama_magang' => $request->nama_magang,
                'slug' => $slug,
                'penyelenggara' => $request->penyelenggara,
                'deskripsi' => $request->deskripsi,
                'poster' => $posterPath,
                'tipe_magang' => $request->tipe_magang,
                'kota' => $request->kota,
                'lokasi_penyelenggaraan' => $request->lokasi_penyelenggaraan,
                'tanggal_open_pendaftaran_tenant' => $request->tanggal_open_pendaftaran_tenant,
                'tipe_partnership' => $request->tipe_partnership,
                'tanggal_close_pendaftaran_tenant' => $request->tanggal_close_pendaftaran_tenant,
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai,
                'status' => $request->status,
                'updated_by' => Auth::id(),
            ]);

            return redirect()->route('magang_dn.index')->with('success', 'Magang berhasil diupdate!');
        } catch (\Exception $e) {
            Log::error("Error updating magang dn: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $magang = EtamMagangDn::findOrFail($id);

            // Set deleted_by sebelum soft delete
            $magang->deleted_by = Auth::id();
            $magang->save();

            // Soft delete
            $magang->delete();

            return redirect()->route('magang_dn.index')->with('success', 'Magang berhasil dihapus!');
        } catch (\Exception $e) {
            Log::error("Error deleting magang dn: " . $e->getMessage());
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function verifikasi($id)
    {
        try {
            // Authorization check - hanya super-admin dan admin-provinsi
            $user = Auth::user();
            $allowedRoles = ['super-admin', 'admin-provinsi'];
            $userRoles = $user->roles->pluck('name')->toArray();
            $hasPermission = !empty(array_intersect($allowedRoles, $userRoles));

            if (!$hasPermission) {
                return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk verifikasi magang!');
            }

            $magang = EtamMagangDn::findOrFail($id);

            $magang->update([
                'status_verifikasi' => 1,
                'id_verifikator' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            return redirect()->route('magang_dn.index')->with('success', 'Magang berhasil diverifikasi!');
        } catch (\Exception $e) {
            Log::error("Error verifying Magang: " . $e->getMessage());
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function unverifikasi($id)
    {
        try {
            // Authorization check - hanya super-admin dan admin-provinsi
            $user = Auth::user();
            $allowedRoles = ['super-admin', 'admin-provinsi'];
            $userRoles = $user->roles->pluck('name')->toArray();
            $hasPermission = !empty(array_intersect($allowedRoles, $userRoles));

            if (!$hasPermission) {
                return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk membatalkan verifikasi magang!');
            }

            $magang = EtamMagangDn::findOrFail($id);

            $magang->update([
                'status_verifikasi' => 0,
                'id_verifikator' => null,
                'updated_by' => Auth::id(),
            ]);

            return redirect()->route('magang_dn.index')->with('success', 'Verifikasi Magang berhasil dibatalkan!');
        } catch (\Exception $e) {
            Log::error("Error unverifying magang dn: " . $e->getMessage());
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function perusahaan($magangId, Request $request)
    {
        try {

            $user = Auth::user();
            $magang = EtamMagangDn::findOrFail($magangId);

            if ($request->ajax()) {

                $perusahaan = EtamMagangDnPerush::with([
                    'user:id,name,email',
                    'creator:id,name'
                ])
                ->where('magang_id', $magangId)
                ->where('deleted_at', NULL)
                ->select('*');

                return DataTables::of($perusahaan)
                    ->addIndexColumn()
                    ->addColumn('user_name', function ($item) {
                        return $item->user ? $item->user->name : '-';
                    })
                    ->addColumn('user_email', function ($item) {
                        return $item->user ? $item->user->email : '-';
                    })
                    ->addColumn('status_badge', function ($item) {
                        return $item->status_badge;
                    })
                    ->editColumn('created_at', function ($item) {
                        return $item->created_at ? $item->created_at->format('d M Y H:i') : '-';
                    })
                    ->addColumn('options', function ($item) use ($user) {
                        $html = '<div class="btn-group-vertical" role="group">';

                        if ($user->roles[0]['name'] == 'penyedia-kerja'){

                            if($item->status == 1){

                                if($item->user_id == $user->id){
                                    $html .= '<form action="' . route('magang_dn.lowongan', [$item->magang_id, $item->user_id]) . '" method="GET" style="display:inline;">
                                            <button type="submit" class="btn btn-info btn-sm mb-1 w-100">📋 Lowongan</button>
                                        </form>';


                                    $html .= '<button class="btn btn-danger btn-sm mb-1" onclick="event.preventDefault(); Swal.fire({
                                                icon: \'warning\',
                                                title: \'Tidak Dapat Dibatalkan\',
                                                text: \'Status sudah di-approve. Untuk pembatalan, silakan hubungi penyelenggara.\',
                                                confirmButtonText: \'OK\'
                                            });">Batalkan</button>';
                                }

                            }else  if($item->status == 2){

                                $html .= '<button class="btn btn-warning btn-sm mb-1" onclick="changeStatus(' . $item->id . ', 0)">Request Verif Ulang</button>';

                            }else{

                                $html .= '<form action="' . route('magang_dn.perusahaan.destroy', [$item->magang_id, $item->id]) . '" method="POST" style="display:inline;" onsubmit="return confirm(\'Yakin ingin menghapus?\')">
                                    ' . csrf_field() . '
                                    ' . method_field('DELETE') . '
                                    <button type="submit" class="btn btn-danger btn-sm w-100">Batal Daftar</button>
                                </form>';
                            }


                        }

                        if($user->roles[0]['name'] == 'pencari-kerja'){
                            $html .= '<form action="' . route('magang_dn.lowongan', [$item->magang_id, $item->user_id]) . '" method="GET" style="display:inline;">
                                        <button type="submit" class="btn btn-info btn-sm mb-1 w-100">📋 Lowongan</button>
                                    </form>';
                        }

                        if ($user->roles[0]['name'] == 'admin-provinsi' || $user->roles[0]['name'] == 'super-admin' || $user->roles[0]['name'] == 'admin-kabkota' ){
                            // Button Edit
                            // $html .= '<button class="btn btn-primary btn-sm mb-1" onclick="showEditPerusahaanModal(' . $item->id . ')">Edit</button>';

                            // Button Change Status
                            if ($item->status == 0) {
                                // Pending - bisa approve atau reject
                                $html .= '<button class="btn btn-success btn-sm mb-1" onclick="changeStatus(' . $item->id . ', 1)">Approve</button>';
                                $html .= '<button class="btn btn-warning btn-sm mb-1" onclick="changeStatus(' . $item->id . ', 2)">Reject</button>';
                            } elseif ($item->status == 1) {
                                // Approved - bisa reject
                                $html .= '<button class="btn btn-warning btn-sm mb-1" onclick="changeStatus(' . $item->id . ', 2)">Reject</button>';
                            } elseif ($item->status == 2) {
                                // Rejected - bisa approve
                                $html .= '<button class="btn btn-success btn-sm mb-1" onclick="changeStatus(' . $item->id . ', 1)">Approve</button>';
                            }

                            $html .= '<form action="' . route('magang_dn.lowongan', [$item->magang_id, $item->user_id]) . '" method="GET" style="display:inline;">
                                        <button type="submit" class="btn btn-info btn-sm mb-1 w-100">📋 Lowongan</button>
                                    </form>';

                            // Button Delete
                            $html .= '<form action="' . route('magang_dn.perusahaan.destroy', [$item->magang_id, $item->id]) . '" method="POST" style="display:inline;" onsubmit="return confirm(\'Yakin ingin menghapus?\')">
                                ' . csrf_field() . '
                                ' . method_field('DELETE') . '
                                <button type="submit" class="btn btn-danger btn-sm w-100">Delete</button>
                            </form>';
                        }

                        $html .= '</div>';

                        return $html;
                    })
                    ->rawColumns(['options', 'status_badge'])
                    ->make(true);
            }

            return view('backend.magang_dn.perusahaan', compact('magang'));
        } catch (\Exception $e) {
            Log::error("Error loading perusahaan page: " . $e->getMessage());
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function storePerusahaan($magangId, Request $request)
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'status' => 'required|integer|in:0,1,2',
            ]);

            // Cek apakah perusahaan sudah terdaftar di job fair ini
            $exists = EtamMagangDnPerush::where('magang_id', $magangId)
                ->where('user_id', $request->user_id)
                ->where('deleted_at', NULL)
                ->exists();

            if ($exists) {
                return redirect()->back()->with('error', 'Perusahaan sudah terdaftar di magang ini!');
            }

            EtamMagangDnPerush::create([
                'magang_id' => $magangId,
                'user_id' => $request->user_id,
                'status' => $request->status,
                'created_by' => Auth::id(),
            ]);

            return redirect()->route('magang_dn.perusahaan', $magangId)
                ->with('success', 'Perusahaan berhasil ditambahkan!');
        } catch (\Exception $e) {
            Log::error("Error storing perusahaan: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function joinMagang($magangId)
    {
        try {
            $userId = Auth::id();

            $magang = EtamMagangDn::findOrFail($magangId);

            if($magang->tipe_partnership == 0){
                 return redirect()->back()->with('error', 'Silahkan hubungi penyelenggara terlebih dahulu');
            }

            // Cek apakah sudah terdaftar
            $exists = EtamMagangDnPerush::where('magang_id', $magangId)
                ->where('user_id', $userId)
                ->where('deleted_at', NULL)
                ->exists();

            if ($exists) {
                return redirect()->back()->with('error', 'Anda sudah terdaftar di magang ini!');
            }

            EtamMagangDnPerush::create([
                'magang_id' => $magangId,
                'user_id' => $userId,
                'status' => 0, // default pending
                'created_by' => $userId,
            ]);

            return redirect()->back()
                ->with('success', 'Berhasil mendaftar magang! Menunggu persetujuan admin.');
        } catch (\Exception $e) {
            Log::error("Error joining magang dn: " . $e->getMessage());
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function lowongan($magangId, $userId, Request $request)
    {
        try {
            $user = Auth::user();
            $magang = EtamMagangDn::findOrFail($magangId);
            $perusahaan = User::findOrFail($userId);

            // Cek apakah perusahaan terdaftar di magang ini
            $perusahaanMagang = EtamMagangDnPerush::where('magang_id', $magangId)
                ->where('user_id', $userId)
                ->firstOrFail();

            if ($request->ajax()) {
                $lowongan = Lowongan::with([
                    'jabatan:id,nama',
                    'sektor:id,name',
                    'pendidikan:id,name',
                    'jurusan:id,nama',
                    'kabkota:id,name',
                    'progress:kode,name'
                    // 'marital:id,name'
                ])
                ->where('posted_by', $userId)
                ->where('tipe_lowongan', 4) // Lowongan magang pemerintah
                ->where('magangpemerintah_id', $magangId)
                ->whereNull('etam_lowongan.deleted_at') // Memastikan data tidak terhapus
                ->select('*');

                return DataTables::of($lowongan)
                    ->addIndexColumn()
                    ->addColumn('jabatan_nama', function ($item) {
                        return $item->jabatan ? $item->jabatan->nama : '-';
                    })
                    ->addColumn('sektor_nama', function ($item) {
                        return $item->sektor ? $item->sektor->name : '-';
                    })
                    ->addColumn('lokasi', function ($item) {
                        return $item->kabkota ? $item->kabkota->name : '-';
                    })
                    ->addColumn('status_badge', function ($item) {
                        return $item->status_id ? $item->progress->name : 'Menunggu';
                    })
                    ->editColumn('tanggal_start', function ($item) {
                        return $item->tanggal_start ? date('d M Y', strtotime($item->tanggal_start)) : '-';
                    })
                    ->editColumn('tanggal_end', function ($item) {
                        return $item->tanggal_end ? date('d M Y', strtotime($item->tanggal_end)) : '-';
                    })
                    // ->addColumn('options', function ($item) use ($magangId, $userId) {
                    //     $html = '<div class="btn-group-vertical" role="group">';

                    //     // Button Edit
                    //     $html .= '<button class="btn btn-primary btn-sm mb-1" onclick="showEditLowonganModal(' . $item->id . ')">Edit</button>';

                    //     // Button Delete
                    //     $html .= '<form action="' . route('magang_dn.lowongan.destroy', [$magangId, $userId, $item->id]) . '" method="POST" style="display:inline;" onsubmit="return confirm(\'Yakin ingin menghapus?\')">
                    //         ' . csrf_field() . '
                    //         ' . method_field('DELETE') . '
                    //         <button type="submit" class="btn btn-danger btn-sm w-100">Delete</button>
                    //     </form>';

                    //     $html .= '</div>';



                    ->addColumn('options', function ($item) use ($magangId, $userId, $user) {
                        // <button class="btn btn-primary btn-sm" onclick="showEditModal(' . $data->id . ')">Edit</button>
                         if ($user->roles[0]['name'] == 'admin-provinsi' || $user->roles[0]['name'] == 'super-admin' || $user->roles[0]['name'] == 'admin-kabkota'  || $user->roles[0]['name'] == 'penyedia-kerja' ){
                            return '
                                <a href="' . route('lowongan.pelamar', encode_url($item->id)) . '" class="btn btn-info btn-sm">Lihat Pelamar</a>
                                <a href="javascript:void(0)" onclick="showData(' . $item->id . ')" class="btn btn-warning btn-sm">Edit</a>
                                <button class="btn btn-danger btn-sm" onclick="confirmDelete(' . $item->id . ')">Delete</button>
                            ';
                        }

                        if($user->roles[0]['name'] == 'pencari-kerja'){

                                return '
                                    <button class="btn btn-warning btn-sm" onclick="showEditModal(' . $item->id . ')">Lihat</button>
                                ';

                         }


                    })
                    ->rawColumns(['options', 'status_badge'])
                    ->make(true);
            }

            // Data untuk dropdown
            // $jabatans = Jabatan::where('is_deleted', 0)->orderBy('nama')->get();
            $jabatans = getJabatan();
            $sektors = Sektor::orderBy('name')->get();
            $pendidikans = Pendidikan::orderBy('name')->get();
            $kabkotas = getKabkota();
            // $maritals = Marital::orderBy('name')->get();

            return view('backend.lowongan.magang_dn', compact('magang', 'perusahaan', 'magangId', 'userId', 'sektors', 'pendidikans', 'kabkotas', 'jabatans'));
            // echo json_encode(compact('magang', 'perusahaan', 'magangId', 'userId', 'sektors', 'pendidikans', 'kabkotas', 'jabatans'));
        } catch (\Exception $e) {
            Log::error("Error loading lowongan page: " . $e->getMessage());
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function storeLowongan($magangId, $userId, Request $request)
    {
        // $arr = array(
        //     'cekmagangid' => $magangId
        // );
        // echo json_encode($arr);
        // die();
        try {

            $validator = Validator::make($request->all(), [
                // 'magangpemerintah_id' => 'required|integer',
                'is_lowongan_disabilitas' => 'required|integer',
                'jabatan_id' => 'required|exists:etam_jabatan,id',
                'sektor_id' => 'required|exists:etam_sektor,id',
                'pendidikan_id' => 'required|exists:etam_pendidikan,id',
                'tanggal_start' => 'required|date',
                'tanggal_end' => 'required|date|after_or_equal:tanggal_start',
                'judul_lowongan' => 'required|string|max:255',
                'kabkota_id' => 'required|integer',
                'lokasi_penempatan_text' => 'required|string',
                'kisaran_gaji' => 'required|integer',
                'kisaran_gaji_akhir' => 'nullable|integer',
                'jumlah_pria' => 'nullable|integer|min:0',
                'jumlah_wanita' => 'nullable|integer|min:0',
                'deskripsi' => 'required|string',
            ]);

             Lowongan::create([
                'magangpemerintah_id' => $magangId,
                'tipe_lowongan' => 4, // Lowongan magang pemerintah
                'jabatan_id' => $request->jabatan_id,
                'sektor_id' => $request->sektor_id,
                'tanggal_start' => $request->tanggal_start,
                'tanggal_end' => $request->tanggal_end,
                'judul_lowongan' => $request->judul_lowongan,
                'kabkota_id' => $request->kabkota_id,
                'lokasi_penempatan_text' => $request->lokasi_penempatan_text,
                'kisaran_gaji' => $request->kisaran_gaji,
                'kisaran_gaji_akhir' => $request->kisaran_gaji_akhir,
                'jumlah_pria' => $request->jumlah_pria ?? 0,
                'jumlah_wanita' => $request->jumlah_wanita ?? 0,
                'deskripsi' => $request->deskripsi,
                'pendidikan_id' => $request->pendidikan_id,
                'jurusan_id' => $request->jurusan_id,
                'marital_id' => $request->marital_id,
                'is_lowongan_disabilitas' => $request->is_lowongan_disabilitas,
                'status_id' => 1,
                'posted_by' => $userId,
                'updated_by' => $userId,
            ]);

            return redirect()->route('magang_dn.lowongan', [$magangId, $userId])
                ->with('success', 'Lowongan berhasil ditambahkan!');
        } catch (\Exception $e) {
            Log::error("Error storing lowongan: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function updateLowongan($magangId, $userId, $id, Request $request)
    {
        try {
            $lowongan = Lowongan::where('id', $id)
                ->where('posted_by', $userId)
                ->where('magangpemerintah_id', $magangId)
                ->where('tipe_lowongan', 4)
                ->firstOrFail();

           $validator = Validator::make($request->all(), [
                // 'magangpemerintah_id' => 'required|integer',
                'is_lowongan_disabilitas' => 'required|integer',
                'jabatan_id' => 'required|exists:etam_jabatan,id',
                'sektor_id' => 'required|exists:etam_sektor,id',
                'pendidikan_id' => 'required|exists:etam_pendidikan,id',
                'tanggal_start' => 'required|date',
                'tanggal_end' => 'required|date|after_or_equal:tanggal_start',
                'judul_lowongan' => 'required|string|max:255',
                'kabkota_id' => 'required|integer',
                'lokasi_penempatan_text' => 'required|string',
                'kisaran_gaji' => 'required|integer',
                'kisaran_gaji_akhir' => 'nullable|integer',
                'jumlah_pria' => 'nullable|integer|min:0',
                'jumlah_wanita' => 'nullable|integer|min:0',
                'deskripsi' => 'required|string',
            ]);

            $lowongan->update([
                'jabatan_id' => $request->jabatan_id,
                'sektor_id' => $request->sektor_id,
                'tanggal_start' => $request->tanggal_start,
                'tanggal_end' => $request->tanggal_end,
                'judul_lowongan' => $request->judul_lowongan,
                'kabkota_id' => $request->kabkota_id,
                'lokasi_penempatan_text' => $request->lokasi_penempatan_text,
                'kisaran_gaji' => $request->kisaran_gaji,
                'kisaran_gaji_akhir' => $request->kisaran_gaji_akhir,
                'jumlah_pria' => $request->jumlah_pria ?? 0,
                'jumlah_wanita' => $request->jumlah_wanita ?? 0,
                'deskripsi' => $request->deskripsi,
                'pendidikan_id' => $request->pendidikan_id,
                'jurusan_id' => $request->jurusan_id,
                'marital_id' => $request->marital_id,
                'is_lowongan_disabilitas' => $request->is_lowongan_disabilitas,
                'updated_by' => Auth::id(),
            ]);

            return redirect()->route('magang_dn.lowongan', [$magangId, $userId])
                ->with('success', 'Lowongan berhasil diupdate!');
        } catch (\Exception $e) {
            Log::error("Error updating lowongan: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function destroyPerusahaan($magangId, $id)
    {
        try {
            $perusahaan = EtamMagangDnPerush::findOrFail($id);

            // Set deleted_by sebelum soft delete
            $perusahaan->deleted_by = Auth::id();
            $perusahaan->save();

            // Soft delete
            $perusahaan->delete();

            return redirect()->route('magang_dn.perusahaan', $magangId)
                ->with('success', 'Perusahaan berhasil dihapus!');
        } catch (\Exception $e) {
            Log::error("Error deleting perusahaan: " . $e->getMessage());
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function showPerusahaan(){
        echo 'showPerusahaan';
    }

    public function updatePerusahaan(){
        echo 'updatePerusahaan';
    }

    public function changeStatusPerusahaan($magangId, $id, Request $request)
    {
        try {
            $perusahaan = EtamMagangDnPerush::findOrFail($id);

            $validated = $request->validate([
                'status' => 'required|integer|in:0,1,2',
            ]);

            $perusahaan->update([
                'status' => $request->status,
                'updated_by' => Auth::id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Status berhasil diubah!',
                'status' => $perusahaan->status
            ]);
        } catch (\Exception $e) {
            Log::error("Error changing status: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
