<?php

namespace App\Http\Controllers\BLK;

use App\Http\Controllers\Controller;
use App\Models\BLK\EtamBlk;
use App\Models\BLK\UserBlk;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class UserBlkController extends Controller
{
    use ManagesBlkAccess;

    public function index(Request $request)
    {
        if (! $this->canManageBlkUsers()) {
            abort(403);
        }

        if ($request->ajax()) {
            $datas = UserBlk::query()
                ->with([
                    'user:id,name,email,whatsapp',
                    'blk:id,nama_lembaga',
                ])
                ->select('id', 'user_id', 'tipe_akun', 'blk_id');

            $blkIds = $this->accessibleBlkIds();
            if ($blkIds !== null) {
                $datas->where(function ($query) use ($blkIds) {
                    $query->whereIn('blk_id', $blkIds)
                        ->orWhere(function ($inner) {
                            $inner->where('tipe_akun', UserBlk::TIPE_ALL)->where('blk_id', 0);
                        });
                });
            }

            if (! empty($request->search['value'])) {
                $searchValue = $request->search['value'];
                $datas->where(function ($query) use ($searchValue) {
                    $query->whereHas('user', function ($userQuery) use ($searchValue) {
                        $userQuery->where('name', 'like', "%{$searchValue}%")
                            ->orWhere('email', 'like', "%{$searchValue}%")
                            ->orWhere('whatsapp', 'like', "%{$searchValue}%");
                    })->orWhereHas('blk', function ($blkQuery) use ($searchValue) {
                        $blkQuery->where('nama_lembaga', 'like', "%{$searchValue}%");
                    });
                });
            }

            return DataTables::of($datas)
                ->addIndexColumn()
                ->addColumn('user_name', function (UserBlk $data) {
                    return $data->user->name ?? 'N/A';
                })
                ->addColumn('email', function (UserBlk $data) {
                    return $data->user->email ?? 'N/A';
                })
                ->addColumn('whatsapp', function (UserBlk $data) {
                    return $data->user->whatsapp ?? 'N/A';
                })
                ->addColumn('blk_nama', function (UserBlk $data) {
                    return $data->blk->nama_lembaga ?? '-';
                })
                ->addColumn('tipe_akun_nama', function (UserBlk $data) {
                    return UserBlk::tipeAkunLabels()[(int) $data->tipe_akun] ?? '-';
                })
                ->addColumn('options', function (UserBlk $data) {
                    return '
                        <button class="btn btn-warning btn-sm" onclick="confirmReset('.$data->id.')">Reset Password</button>
                        <button class="btn btn-primary btn-sm" onclick="showEditModal('.$data->id.')">Edit</button>
                        <button class="btn btn-danger btn-sm" onclick="confirmDelete('.$data->id.')">Delete</button>
                    ';
                })
                ->rawColumns(['options'])
                ->make(true);
        }

        $blkOptions = $this->blkOptions();
        $tipeAkun = UserBlk::tipeAkunLabels();

        return view('backend.blk.users.index', compact('blkOptions', 'tipeAkun'));
    }

    public function store(Request $request)
    {
        if (! $this->canManageBlkUsers()) {
            abort(403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'whatsapp' => 'required|string|max:20|unique:users,whatsapp',
            'tipe_akun' => 'required|in:0,1,2,3,4',
            'blk_id' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()]);
        }

        $blkId = (int) ($request->blk_id ?: 0);
        if ((int) $request->tipe_akun !== UserBlk::TIPE_ALL && ! $this->canAccessBlk($blkId)) {
            return response()->json(['success' => false, 'message' => 'Anda tidak berhak menambahkan user untuk BLK ini']);
        }

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'whatsapp' => $request->whatsapp,
                'password' => bcrypt($request->email),
                'is_finished' => 1,
            ]);

            $role = Role::where('name', 'admin-blk')->firstOrFail();
            $user->assignRole($role);

            UserBlk::create([
                'user_id' => $user->id,
                'tipe_akun' => $request->tipe_akun,
                'blk_id' => $blkId,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            DB::commit();

            return response()->json(['success' => true, 'message' => 'User BLK berhasil ditambahkan']);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json(['success' => false, 'message' => 'Error: '.$e->getMessage()]);
        }
    }

    public function show(string $id)
    {
        if (! $this->canManageBlkUsers()) {
            abort(403);
        }

        $data = UserBlk::with(['user:id,name,email,whatsapp'])->findOrFail($id);

        if ((int) $data->tipe_akun !== UserBlk::TIPE_ALL && ! $this->canAccessBlk((int) $data->blk_id)) {
            abort(403);
        }

        return response()->json(['success' => true, 'data' => $data]);
    }

    public function update(Request $request, string $id)
    {
        if (! $this->canManageBlkUsers()) {
            abort(403);
        }

        $data = UserBlk::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$data->user_id,
            'whatsapp' => 'required|string|max:20|unique:users,whatsapp,'.$data->user_id,
            'tipe_akun' => 'required|in:0,1,2,3,4',
            'blk_id' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()]);
        }

        $blkId = (int) ($request->blk_id ?: 0);
        if ((int) $request->tipe_akun !== UserBlk::TIPE_ALL && ! $this->canAccessBlk($blkId)) {
            return response()->json(['success' => false, 'message' => 'Anda tidak berhak mengubah user untuk BLK ini']);
        }

        $data->user->update([
            'name' => $request->name,
            'email' => $request->email,
            'whatsapp' => $request->whatsapp,
        ]);

        $data->update([
            'tipe_akun' => $request->tipe_akun,
            'blk_id' => $blkId,
            'updated_by' => Auth::id(),
        ]);

        return response()->json(['success' => true, 'message' => 'User BLK berhasil diupdate']);
    }

    public function destroy(string $id)
    {
        if (! $this->canManageBlkUsers()) {
            abort(403);
        }

        try {
            $admin = UserBlk::findOrFail($id);
            if ((int) $admin->tipe_akun !== UserBlk::TIPE_ALL && ! $this->canAccessBlk((int) $admin->blk_id)) {
                abort(403);
            }

            $admin->deleted_by = Auth::id();
            $admin->save();
            $admin->delete();

            $user = User::find($admin->user_id);
            if ($user) {
                $user->is_deleted = 1;
                $user->save();
                $user->delete();
            }

            return response()->json(['success' => true, 'message' => 'Hapus data berhasil']);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Error: '.$e->getMessage()]);
        }
    }

    public function reset(string $id)
    {
        if (! $this->canManageBlkUsers()) {
            abort(403);
        }

        try {
            $admin = UserBlk::findOrFail($id);
            $user = User::findOrFail($admin->user_id);
            $user->update([
                'password' => bcrypt($user->email),
            ]);

            return response()->json(['success' => true, 'message' => 'Reset password berhasil']);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Error: '.$e->getMessage()]);
        }
    }

    /**
     * @return \Illuminate\Support\Collection<int, EtamBlk>
     */
    private function blkOptions()
    {
        $query = EtamBlk::query()->orderBy('nama_lembaga');
        $ids = $this->accessibleBlkIds();
        if ($ids !== null) {
            $query->whereIn('id', $ids);
        }

        return $query->get(['id', 'nama_lembaga']);
    }
}
