<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EtamJobFair;
use App\Models\EtamJobFairPerush;
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

class JobFairController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
         // Mendapatkan ID pengguna yang sedang login
        if ($request->ajax()) {

            $user = Auth::user();

            $jobFairs = EtamJobFair::with([
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
                $jobFairs->where(function ($query) use ($searchValue) {
                    $query->where('nama_job_fair', 'like', "%$searchValue%")
                        ->orWhere('penyelenggara', 'like', "%$searchValue%")
                        ->orWhere('lokasi_penyelenggaraan', 'like', "%$searchValue%")
                        ->orWhereHas('penyelenggaraUser', function ($q) use ($searchValue) {
                            $q->where('name', 'like', "%$searchValue%");
                        });
                });
            }

            if ($user->roles[0]['name'] == 'pencari-kerja'){
                $jobFairs->where('status_verifikasi', 1);
                $jobFairs->where('status', 1);
            }

            if ($user->roles[0]['name'] == 'penyedia-kerja'){
                $jobFairs->where('status_verifikasi', 1);
                $jobFairs->where('status', 1);
            }

            if ($user->roles[0]['name'] == 'admin-kabkota'){
                $jobFairs->where('id_penyelenggara', $user->id);
            }

            return DataTables::of($jobFairs)
                ->addIndexColumn()
                ->addColumn('jenis_penyelenggara_text', function ($jobFair) {
                    return $jobFair->jenis_penyelenggara == 0 ? 'Pemerintah' : 'Swasta';
                })
                ->addColumn('tipe_job_fair_text', function ($jobFair) {
                    return $jobFair->tipe_job_fair == 0 ? 'Online' : 'Offline';
                })
                ->addColumn('tipe_partnership_text', function ($jobFair) {
                    return $jobFair->tipe_partnership == 0 ? 'Tertutup' : 'Open';
                })
                ->addColumn('status_verifikasi_badge', function ($jobFair) {
                    if ($jobFair->status_verifikasi == 1) {
                        return '<span class="badge bg-success">Terverifikasi</span>';
                    } else {
                        return '<span class="badge bg-warning">Belum Verifikasi</span>';
                    }
                })
                ->addColumn('status_badge', function ($jobFair) {
                    if ($jobFair->status == 1) {
                        return '<span class="badge bg-success">Aktif</span>';
                    } else {
                        return '<span class="badge bg-secondary">Tidak Aktif</span>';
                    }
                })
                ->editColumn('tanggal_mulai', function ($jobFair) {
                    return $jobFair->tanggal_mulai ? $jobFair->tanggal_mulai->format('d M Y') : '-';
                })
                ->editColumn('tanggal_selesai', function ($jobFair) {
                    return $jobFair->tanggal_selesai ? $jobFair->tanggal_selesai->format('d M Y') : '-';
                })
                ->addColumn('options', function ($jobFair) use ($user) {
                    $html = '<div class="btn-group-vertical" role="group">';

                    if ($user->roles[0]['name'] == 'pencari-kerja'){

                        // Button Detail
                        $html .= '<button class="btn btn-info btn-sm mb-1" onclick="showDetailModal(' . $jobFair->id . ')">Detail</button>';
                        $html .= '
                        <form action="' . route('jobfair.perusahaan', $jobFair->id) . '" method="GET" style="display:inline;">
                            <button type="submit" class="btn btn-primary btn-sm mb-1 w-100">🏢 Lihat Perusahaan</button>
                        </form>
                        ';

                    }

                    if ($user->roles[0]['name'] == 'penyedia-kerja'){

                        // Button Detail
                        $html .= '<button class="btn btn-info btn-sm mb-1" onclick="showDetailModal(' . $jobFair->id . ')">Detail</button>';
                        $html .= '
                        <form action="' . route('jobfair.perusahaan', $jobFair->id) . '" method="GET" style="display:inline;">
                            <button type="submit" class="btn btn-primary btn-sm mb-1 w-100">🏢 Lihat Keikutsertaan</button>
                        </form>
                        ';
                    
                    }

                    if ($user->roles[0]['name'] == 'admin-provinsi' || $user->roles[0]['name'] == 'super-admin'){

                         // Button Detail
                        $html .= '<button class="btn btn-info btn-sm mb-1" onclick="showDetailModal(' . $jobFair->id . ')">Detail</button>';
                        
                        // Button Edit
                        $html .= '<button class="btn btn-primary btn-sm mb-1" onclick="showEditModal(' . $jobFair->id . ')">Edit</button>';
                        
                        // Button Verifikasi / Batal Verifikasi - HANYA untuk super-admin dan admin-provinsi
                        $user = Auth::user();
                        $allowedRoles = ['super-admin', 'admin-provinsi'];
                        $userRoles = $user->roles->pluck('name')->toArray();
                        $hasVerifyPermission = !empty(array_intersect($allowedRoles, $userRoles));
                        
                        if ($hasVerifyPermission) {
                            if ($jobFair->status_verifikasi == 0) {
                                // Belum diverifikasi - tampilkan tombol Verifikasi
                                $html .= '<form action="' . route('jobfair.verifikasi', $jobFair->id) . '" method="POST" style="display:inline;" onsubmit="return confirm(\'Yakin ingin verifikasi job fair ini?\')">
                                    ' . csrf_field() . '
                                    <button type="submit" class="btn btn-success btn-sm mb-1 w-100">✓ Verifikasi</button>
                                </form>';
                            } else {
                                // Sudah diverifikasi - tampilkan tombol Batal Verifikasi
                                $html .= '<form action="' . route('jobfair.unverifikasi', $jobFair->id) . '" method="POST" style="display:inline;" onsubmit="return confirm(\'Yakin ingin membatalkan verifikasi?\')">
                                    ' . csrf_field() . '
                                    <button type="submit" class="btn btn-warning btn-sm mb-1 w-100">✗ Batal Verifikasi</button>
                                </form>';
                            }
                        }

                        $html .= '
                            <form action="' . route('jobfair.perusahaan', $jobFair->id) . '" method="GET" style="display:inline;">
                                <button type="submit" class="btn btn-primary btn-sm mb-1 w-100">🏢 Lihat Perusahaan</button>
                            </form>
                        ';

                        
                        // Button Delete
                        $html .= '<form action="' . route('jobfair.destroy', $jobFair->id) . '" method="POST" style="display:inline;" onsubmit="return confirm(\'Yakin ingin menghapus?\')">
                            ' . csrf_field() . '
                            ' . method_field('DELETE') . '
                            <button type="submit" class="btn btn-danger btn-sm w-100">Delete</button>
                        </form>';

                    }

                    if ($user->roles[0]['name'] == 'admin-kabkota' || Auth::user()->roles[0]['name'] == 'admin-kabkota-officer'){

                        // Button Detail
                        $html .= '<button class="btn btn-info btn-sm mb-1" onclick="showDetailModal(' . $jobFair->id . ')">Detail</button>';
                        $html .= '
                        <form action="' . route('jobfair.perusahaan', $jobFair->id) . '" method="GET" style="display:inline;">
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

        return view('backend.jobfair.index');
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
                'nama_job_fair' => 'required|string|max:255',
                'penyelenggara' => 'nullable|string|max:255',
                'deskripsi' => 'nullable|string',
                'poster' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'tipe_job_fair' => 'required|integer|in:0,1',
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
            $slug = Str::slug($request->nama_job_fair);
            $slugCount = EtamJobFair::where('slug', 'like', $slug . '%')->count();
            if ($slugCount > 0) {
                $slug = $slug . '-' . ($slugCount + 1);
            }

            // Upload poster jika ada
            $posterPath = null;
            if ($request->hasFile('poster')) {
                $posterPath = $request->file('poster')->store('job-fair-posters', 'public');
            }

            // Create job fair
            $jobFair = EtamJobFair::create([
                'jenis_penyelenggara' => $request->jenis_penyelenggara,
                'id_penyelenggara' => Auth::id(),
                'nama_job_fair' => $request->nama_job_fair,
                'slug' => $slug,
                'penyelenggara' => $request->penyelenggara,
                'deskripsi' => $request->deskripsi,
                'poster' => $posterPath,
                'tipe_job_fair' => $request->tipe_job_fair,
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

            return redirect()->route('jobfair.index')->with('success', 'Job Fair berhasil ditambahkan!');
        } catch (\Exception $e) {
            Log::error("Error creating job fair: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $jobFair = EtamJobFair::with([
                'penyelenggaraUser:id,name,email',
                'verifikator:id,name',
                'creator:id,name',
                'updater:id,name'
            ])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $jobFair
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $jobFair = EtamJobFair::findOrFail($id);

            // Validasi input
            $validated = $request->validate([
                'jenis_penyelenggara' => 'required|integer|in:0,1',
                'nama_job_fair' => 'required|string|max:255',
                'penyelenggara' => 'nullable|string|max:255',
                'deskripsi' => 'nullable|string',
                'poster' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'tipe_job_fair' => 'required|integer|in:0,1',
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
            $slug = $jobFair->slug;
            if ($request->nama_job_fair !== $jobFair->nama_job_fair) {
                $slug = Str::slug($request->nama_job_fair);
                $slugCount = EtamJobFair::where('slug', 'like', $slug . '%')
                    ->where('id', '!=', $id)
                    ->count();
                if ($slugCount > 0) {
                    $slug = $slug . '-' . ($slugCount + 1);
                }
            }

            // Upload poster baru jika ada
            $posterPath = $jobFair->poster;
            if ($request->hasFile('poster')) {
                // Hapus poster lama jika ada
                if ($posterPath && Storage::disk('public')->exists($posterPath)) {
                    Storage::disk('public')->delete($posterPath);
                }
                $posterPath = $request->file('poster')->store('job-fair-posters', 'public');
            }

            // Update job fair
            $jobFair->update([
                'jenis_penyelenggara' => $request->jenis_penyelenggara,
                'id_penyelenggara' => $request->id_penyelenggara ?? Auth::id(),
                'nama_job_fair' => $request->nama_job_fair,
                'slug' => $slug,
                'penyelenggara' => $request->penyelenggara,
                'deskripsi' => $request->deskripsi,
                'poster' => $posterPath,
                'tipe_job_fair' => $request->tipe_job_fair,
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

            return redirect()->route('jobfair.index')->with('success', 'Job Fair berhasil diupdate!');
        } catch (\Exception $e) {
            Log::error("Error updating job fair: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage (Soft Delete).
     */
    public function destroy($id)
    {
        try {
            $jobFair = EtamJobFair::findOrFail($id);
            
            // Set deleted_by sebelum soft delete
            $jobFair->deleted_by = Auth::id();
            $jobFair->save();
            
            // Soft delete
            $jobFair->delete();

            return redirect()->route('jobfair.index')->with('success', 'Job Fair berhasil dihapus!');
        } catch (\Exception $e) {
            Log::error("Error deleting job fair: " . $e->getMessage());
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Verifikasi job fair
     */
    public function verifikasi($id)
    {
        try {
            // Authorization check - hanya super-admin dan admin-provinsi
            $user = Auth::user();
            $allowedRoles = ['super-admin', 'admin-provinsi'];
            $userRoles = $user->roles->pluck('name')->toArray();
            $hasPermission = !empty(array_intersect($allowedRoles, $userRoles));
            
            if (!$hasPermission) {
                return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk verifikasi job fair!');
            }
            
            $jobFair = EtamJobFair::findOrFail($id);
            
            $jobFair->update([
                'status_verifikasi' => 1,
                'id_verifikator' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            return redirect()->route('jobfair.index')->with('success', 'Job Fair berhasil diverifikasi!');
        } catch (\Exception $e) {
            Log::error("Error verifying job fair: " . $e->getMessage());
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Batalkan verifikasi job fair
     */
    public function unverifikasi($id)
    {
        try {
            // Authorization check - hanya super-admin dan admin-provinsi
            $user = Auth::user();
            $allowedRoles = ['super-admin', 'admin-provinsi'];
            $userRoles = $user->roles->pluck('name')->toArray();
            $hasPermission = !empty(array_intersect($allowedRoles, $userRoles));
            
            if (!$hasPermission) {
                return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk membatalkan verifikasi job fair!');
            }
            
            $jobFair = EtamJobFair::findOrFail($id);
            
            $jobFair->update([
                'status_verifikasi' => 0,
                'id_verifikator' => null,
                'updated_by' => Auth::id(),
            ]);

            return redirect()->route('jobfair.index')->with('success', 'Verifikasi Job Fair berhasil dibatalkan!');
        } catch (\Exception $e) {
            Log::error("Error unverifying job fair: " . $e->getMessage());
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Toggle status job fair
     */
    public function toggleStatus($id)
    {
        try {
            $jobFair = EtamJobFair::findOrFail($id);
            
            $newStatus = $jobFair->status == 1 ? 0 : 1;
            
            $jobFair->update([
                'status' => $newStatus,
                'updated_by' => Auth::id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Status Job Fair berhasil diubah',
                'status' => $newStatus
            ]);
        } catch (\Exception $e) {
            Log::error("Error toggling job fair status: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get list of users for dropdown
     */
    public function getUsers()
    {
        try {
            $users = User::select('id', 'name', 'email')
                ->where('is_deleted', 0)
                ->orderBy('name', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $users
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }


/**
     * Halaman daftar perusahaan job fair
     */
    public function perusahaan($jobfairId, Request $request)
    {
        try {

            $user = Auth::user();
            $jobFair = EtamJobFair::findOrFail($jobfairId);

            if ($request->ajax()) {

                $perusahaan = EtamJobFairPerush::with([
                    'user:id,name,email',
                    'creator:id,name'
                ])
                ->where('jobfair_id', $jobfairId)
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
                                    $html .= '<form action="' . route('jobfair.lowongan', [$item->jobfair_id, $item->user_id]) . '" method="GET" style="display:inline;">
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

                                $html .= '<form action="' . route('jobfair.perusahaan.destroy', [$item->jobfair_id, $item->id]) . '" method="POST" style="display:inline;" onsubmit="return confirm(\'Yakin ingin menghapus?\')">
                                    ' . csrf_field() . '
                                    ' . method_field('DELETE') . '
                                    <button type="submit" class="btn btn-danger btn-sm w-100">Batal Daftar</button>
                                </form>';
                            }
                           

                        }

                        if($user->roles[0]['name'] == 'pencari-kerja'){
                            $html .= '<form action="' . route('jobfair.lowongan', [$item->jobfair_id, $item->user_id]) . '" method="GET" style="display:inline;">
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

                            $html .= '<form action="' . route('jobfair.lowongan', [$item->jobfair_id, $item->user_id]) . '" method="GET" style="display:inline;">
                                        <button type="submit" class="btn btn-info btn-sm mb-1 w-100">📋 Lowongan</button>
                                    </form>';
                            
                            // Button Delete
                            $html .= '<form action="' . route('jobfair.perusahaan.destroy', [$item->jobfair_id, $item->id]) . '" method="POST" style="display:inline;" onsubmit="return confirm(\'Yakin ingin menghapus?\')">
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

            return view('backend.jobfair.perusahaan', compact('jobFair'));
        } catch (\Exception $e) {
            Log::error("Error loading perusahaan page: " . $e->getMessage());
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Store perusahaan ke job fair
     */
    public function storePerusahaan($jobfairId, Request $request)
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'status' => 'required|integer|in:0,1,2',
            ]);

            // Cek apakah perusahaan sudah terdaftar di job fair ini
            $exists = EtamJobFairPerush::where('jobfair_id', $jobfairId)
                ->where('user_id', $request->user_id)
                ->where('deleted_at', NULL)
                ->exists();

            if ($exists) {
                return redirect()->back()->with('error', 'Perusahaan sudah terdaftar di job fair ini!');
            }

            EtamJobFairPerush::create([
                'jobfair_id' => $jobfairId,
                'user_id' => $request->user_id,
                'status' => $request->status,
                'created_by' => Auth::id(),
            ]);

            return redirect()->route('jobfair.perusahaan', $jobfairId)
                ->with('success', 'Perusahaan berhasil ditambahkan!');
        } catch (\Exception $e) {
            Log::error("Error storing perusahaan: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function joinJobFair($jobfairId)
    {
        try {
            $userId = Auth::id();

            $jobFair = EtamJobFair::findOrFail($jobfairId);

            if($jobFair->tipe_partnership == 0){
                 return redirect()->back()->with('error', 'Silahkan hubungi penyelenggara terlebih dahulu');
            }
            
            // Cek apakah sudah terdaftar
            $exists = EtamJobFairPerush::where('jobfair_id', $jobfairId)
                ->where('user_id', $userId)
                ->where('deleted_at', NULL)
                ->exists();

            if ($exists) {
                return redirect()->back()->with('error', 'Anda sudah terdaftar di job fair ini!');
            }

            EtamJobFairPerush::create([
                'jobfair_id' => $jobfairId,
                'user_id' => $userId,
                'status' => 0, // default pending
                'created_by' => $userId,
            ]);

            return redirect()->back()
                ->with('success', 'Berhasil mendaftar job fair! Menunggu persetujuan admin.');
        } catch (\Exception $e) {
            Log::error("Error joining job fair: " . $e->getMessage());
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
}

    /**
     * Get detail perusahaan
     */
    public function showPerusahaan($jobfairId, $id)
    {
        try {
            $perusahaan = EtamJobFairPerush::with(['user', 'creator'])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $perusahaan
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update perusahaan
     */
    public function updatePerusahaan($jobfairId, $id, Request $request)
    {
        try {
            $perusahaan = EtamJobFairPerush::findOrFail($id);

            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'status' => 'required|integer|in:0,1,2',
            ]);

            // Cek apakah perusahaan sudah terdaftar di job fair ini (kecuali yang sedang diedit)
            $exists = EtamJobFairPerush::where('jobfair_id', $jobfairId)
                ->where('user_id', $request->user_id)
                ->where('id', '!=', $id)
                ->exists();

            if ($exists) {
                return redirect()->back()->with('error', 'Perusahaan sudah terdaftar di job fair ini!');
            }

            $perusahaan->update([
                'user_id' => $request->user_id,
                'status' => $request->status,
                'updated_by' => Auth::id(),
            ]);

            return redirect()->route('jobfair.perusahaan', $jobfairId)
                ->with('success', 'Perusahaan berhasil diupdate!');
        } catch (\Exception $e) {
            Log::error("Error updating perusahaan: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Delete perusahaan dari job fair
     */
    public function destroyPerusahaan($jobfairId, $id)
    {
        try {
            $perusahaan = EtamJobFairPerush::findOrFail($id);
            
            // Set deleted_by sebelum soft delete
            $perusahaan->deleted_by = Auth::id();
            $perusahaan->save();
            
            // Soft delete
            $perusahaan->delete();

            return redirect()->route('jobfair.perusahaan', $jobfairId)
                ->with('success', 'Perusahaan berhasil dihapus!');
        } catch (\Exception $e) {
            Log::error("Error deleting perusahaan: " . $e->getMessage());
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Change status perusahaan
     */
    public function changeStatusPerusahaan($jobfairId, $id, Request $request)
    {
        try {
            $perusahaan = EtamJobFairPerush::findOrFail($id);
            
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

    /**
     * Get users dengan role penyedia-kerja (with search)
     */
    public function getPenyediaKerja(Request $request)
    {
        try {
            $search = $request->get('search', '');
            $page = $request->get('page', 1);
            $perPage = 10;

            $query = User::whereHas('roles', function ($query) {
                    $query->where('name', 'penyedia-kerja');
                })
                ->where('is_deleted', 0);

            // Filter berdasarkan search (minimal 4 karakter)
            if (strlen($search) >= 4) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%")
                      ->orWhere('email', 'like', "%$search%");
                });
            }

            // Pagination
            $total = $query->count();
            $users = $query->select('id', 'name', 'email')
                ->orderBy('name', 'asc')
                ->skip(($page - 1) * $perPage)
                ->take($perPage)
                ->get();

            // Format untuk Select2
            $results = $users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'text' => $user->name . ' (' . $user->email . ')'
                ];
            });

            return response()->json([
                'results' => $results,
                'pagination' => [
                    'more' => ($page * $perPage) < $total
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'results' => [],
                'pagination' => ['more' => false]
            ], 500);
        }
    }

    public function getPenyediaKerjaList(Request $request)
    {
        try {
            $search = $request->get('search', '');
            $page = $request->get('page', 1);
            $perPage = 10;

            // Query users dengan role penyedia-kerja
            $query = User::whereHas('roles', function ($query) {
                    $query->where('name', 'penyedia-kerja');
                })
                ->where('is_deleted', 0);

            // Filter berdasarkan search (minimal 4 karakter)
            if (strlen($search) >= 4) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%")
                      ->orWhere('email', 'like', "%$search%");
                });
            }

            // Pagination
            $total = $query->count();
            $users = $query->select('id', 'name', 'email')
                ->orderBy('name', 'asc')
                ->skip(($page - 1) * $perPage)
                ->take($perPage)
                ->get();

            // Format untuk Select2
            $results = $users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'text' => $user->name . ' (' . $user->email . ')'
                ];
            });

            return response()->json([
                'results' => $results,
                'pagination' => [
                    'more' => ($page * $perPage) < $total
                ]
            ]);
        } catch (\Exception $e) {
            Log::error("Error getting penyedia kerja: " . $e->getMessage());
            return response()->json([
                'results' => [],
                'pagination' => ['more' => false],
                'error' => $e->getMessage()
            ], 200); // Return 200 supaya Select2 tidak error
        }
    }


    /**
     * Halaman lowongan perusahaan di job fair
     */
    public function lowongan($jobfairId, $userId, Request $request)
    {
        try {
            $user = Auth::user();
            $jobFair = EtamJobFair::findOrFail($jobfairId);
            $perusahaan = User::findOrFail($userId);
            
            // Cek apakah perusahaan terdaftar di job fair ini
            $perusahaanJobFair = EtamJobFairPerush::where('jobfair_id', $jobfairId)
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
                ->where('tipe_lowongan', 1) // Lowongan job fair
                ->where('jobfair_id', $jobfairId)
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
                    // ->addColumn('options', function ($item) use ($jobfairId, $userId) {
                    //     $html = '<div class="btn-group-vertical" role="group">';
                        
                    //     // Button Edit
                    //     $html .= '<button class="btn btn-primary btn-sm mb-1" onclick="showEditLowonganModal(' . $item->id . ')">Edit</button>';
                        
                    //     // Button Delete
                    //     $html .= '<form action="' . route('jobfair.lowongan.destroy', [$jobfairId, $userId, $item->id]) . '" method="POST" style="display:inline;" onsubmit="return confirm(\'Yakin ingin menghapus?\')">
                    //         ' . csrf_field() . '
                    //         ' . method_field('DELETE') . '
                    //         <button type="submit" class="btn btn-danger btn-sm w-100">Delete</button>
                    //     </form>';
                        
                    //     $html .= '</div>';

                   

                    ->addColumn('options', function ($item) use ($jobfairId, $userId, $user) {
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

            return view('backend.lowongan.lowongan', compact('jobFair', 'perusahaan', 'jobfairId', 'userId', 'sektors', 'pendidikans', 'kabkotas', 'jabatans'));
        } catch (\Exception $e) {
            Log::error("Error loading lowongan page: " . $e->getMessage());
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Store lowongan job fair
     */
    public function storeLowongan($jobfairId, $userId, Request $request)
    {
        try {
            // $validated = $request->validate([
            //     'is_lowongan_disabilitas' => 'required|integer|in:0,1',
            //     'jabatan_id' => 'required|exists:etam_jabatan,id',
            //     'sektor_id' => 'required|exists:etam_sektor,id',
            //     'pendidikan_id' => 'required|exists:etam_pendidikan,id',
            //     'jurusan_id' => 'nullable|exists:etam_jurusan,id',
            //     'tanggal_start' => 'required|date',
            //     'tanggal_end' => 'required|date|after_or_equal:tanggal_start',
            //     'judul_lowongan' => 'required|string|max:500',
            //     'kabkota_id' => 'required|string|max:10',
            //     'lokasi_penempatan_text' => 'nullable|string|max:255',
            //     'kisaran_gaji' => 'nullable|numeric',
            //     'kisaran_gaji_akhir' => 'nullable|numeric',
            //     'jumlah_pria' => 'nullable|integer|min:0',
            //     'jumlah_wanita' => 'nullable|integer|min:0',
            //     'deskripsi' => 'nullable|string',
            //     'marital_id' => 'required|exists:etam_marital,id',
            // ]);

            $validator = Validator::make($request->all(), [
                'jobfair_id' => 'required|integer',
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
                'jobfair_id' => $jobfairId,
                'tipe_lowongan' => 1, // Lowongan job fair
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

            return redirect()->route('jobfair.lowongan', [$jobfairId, $userId])
                ->with('success', 'Lowongan berhasil ditambahkan!');
        } catch (\Exception $e) {
            Log::error("Error storing lowongan: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Get detail lowongan
     */
    public function showLowongan($jobfairId, $userId, $id)
    {
        try {
            $lowongan = Lowongan::with(['sektor', 'pendidikan', 'jurusan', 'kabkota', 'marital'])
                ->where('id', $id)
                ->where('user_id', $userId)
                ->where('jobfair_id', $jobfairId)
                ->where('tipe_lowongan', 1)
                ->firstOrFail();

            return response()->json([
                'success' => true,
                'data' => $lowongan
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update lowongan job fair
     */
    public function updateLowongan($jobfairId, $userId, $id, Request $request)
    {
        try {
            $lowongan = Lowongan::where('id', $id)
                ->where('posted_by', $userId)
                ->where('jobfair_id', $jobfairId)
                ->where('tipe_lowongan', 1)
                ->firstOrFail();

           $validator = Validator::make($request->all(), [
                'jobfair_id' => 'required|integer',
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

            return redirect()->route('jobfair.lowongan', [$jobfairId, $userId])
                ->with('success', 'Lowongan berhasil diupdate!');
        } catch (\Exception $e) {
            Log::error("Error updating lowongan: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Delete lowongan job fair
     */
    public function destroyLowongan($jobfairId, $userId, $id)
    {
        try {
            $lowongan = Lowongan::where('id', $id)
                ->where('user_id', $userId)
                ->where('jobfair_id', $jobfairId)
                ->where('tipe_lowongan', 1)
                ->firstOrFail();
            
            // Set deleted_by sebelum soft delete
            $lowongan->deleted_by = Auth::id();
            $lowongan->save();
            
            // Soft delete
            $lowongan->delete();

            return redirect()->route('jobfair.lowongan', [$jobfairId, $userId])
                ->with('success', 'Lowongan berhasil dihapus!');
        } catch (\Exception $e) {
            Log::error("Error deleting lowongan: " . $e->getMessage());
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Get jurusan by pendidikan untuk dropdown
     */
    public function getJurusanByPendidikan($pendidikanId)
    {
        try {
            $jurusan = Jurusan::where('pendidikan_id', $pendidikanId)
                ->where('is_deleted', 0)
                ->orderBy('nama')
                ->get(['id', 'nama']);

            return response()->json($jurusan);
        } catch (\Exception $e) {
            Log::error("Error getting jurusan: " . $e->getMessage());
            return response()->json([
                'error' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all pendidikan untuk dropdown
     */
    public function getAllPendidikan()
    {
        try {
            $pendidikan = Pendidikan::where('is_deleted', 0)
                ->orderBy('name')
                ->get(['id', 'name']);

            return response()->json($pendidikan);
        } catch (\Exception $e) {
            Log::error("Error getting pendidikan: " . $e->getMessage());
            return response()->json([
                'error' => 'Error: ' . $e->getMessage()
            ], 500);
        }
}


}