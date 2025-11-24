<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EtamJobFair;
use App\Models\EtamJobFairPerush;
use App\Models\User;

use App\Models\Lowongan;
// use App\Models\Jabatan;
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

}
?>