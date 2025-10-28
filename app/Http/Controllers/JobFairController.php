<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EtamJobFair;
use App\Models\User;
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
        if ($request->ajax()) {
            $jobFairs = EtamJobFair::with([
                'penyelenggaraUser:id,name,email',
                'verifikator:id,name',
                'creator:id,name'
            ])->select('*');

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

            return DataTables::of($jobFairs)
                ->addIndexColumn()
                ->addColumn('penyelenggara_name', function ($jobFair) {
                    return $jobFair->penyelenggaraUser ? $jobFair->penyelenggaraUser->name : ($jobFair->penyelenggara ?? 'N/A');
                })
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
                ->addColumn('options', function ($jobFair) {
                    return '
                        <button class="btn btn-info btn-sm" onclick="showDetailModal(' . $jobFair->id . ')">Detail</button>
                        <button class="btn btn-primary btn-sm" onclick="showEditModal(' . $jobFair->id . ')">Edit</button>
                        <button class="btn btn-danger btn-sm" onclick="confirmDelete(' . $jobFair->id . ')">Delete</button>
                    ';
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
            $validator = Validator::make($request->all(), [
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

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

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
                'id_penyelenggara' => $request->id_penyelenggara,
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

            return response()->json([
                'success' => true,
                'message' => 'Job Fair berhasil ditambahkan',
                'data' => $jobFair
            ]);
        } catch (\Exception $e) {
            Log::error("Error creating job fair: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
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
            $validator = Validator::make($request->all(), [
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

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

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
                'id_penyelenggara' => $request->id_penyelenggara,
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

            return response()->json([
                'success' => true,
                'message' => 'Job Fair berhasil diupdate',
                'data' => $jobFair
            ]);
        } catch (\Exception $e) {
            Log::error("Error updating job fair: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
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

            return response()->json([
                'success' => true,
                'message' => 'Job Fair berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            Log::error("Error deleting job fair: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verifikasi job fair
     */
    public function verifikasi($id)
    {
        try {
            $jobFair = EtamJobFair::findOrFail($id);
            
            $jobFair->update([
                'status_verifikasi' => 1,
                'id_verifikator' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Job Fair berhasil diverifikasi'
            ]);
        } catch (\Exception $e) {
            Log::error("Error verifying job fair: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Batalkan verifikasi job fair
     */
    public function unverifikasi($id)
    {
        try {
            $jobFair = EtamJobFair::findOrFail($id);
            
            $jobFair->update([
                'status_verifikasi' => 0,
                'id_verifikator' => null,
                'updated_by' => Auth::id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Verifikasi Job Fair berhasil dibatalkan'
            ]);
        } catch (\Exception $e) {
            Log::error("Error unverifying job fair: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
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
}