<?php

namespace App\Http\Controllers;

use App\Models\UserIntegrasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class UserIntegrasiController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = UserIntegrasi::query()->select([
                'id',
                'username',
                'client_id',
                'api_key',
                'status',
                'keterangan',
                'created_by',
                'created_at',
                'updated_at',
            ]);

            if (! empty($request->search['value'])) {
                $search = $request->search['value'];
                $query->where(function ($q) use ($search) {
                    $q->where('username', 'like', "%{$search}%")
                        ->orWhere('client_id', 'like', "%{$search}%")
                        ->orWhere('api_key', 'like', "%{$search}%")
                        ->orWhere('keterangan', 'like', "%{$search}%");
                });
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('status_label', function ($row) {
                    return $row->status == 1
                        ? '<span class="badge bg-success">Aktif</span>'
                        : '<span class="badge bg-secondary">Nonaktif</span>';
                })
                ->addColumn('options', function ($row) {
                    return '
                        <button class="btn btn-primary btn-sm" onclick="showEditModal('.$row->id.')">Edit</button>
                        <button class="btn btn-warning btn-sm" onclick="regenerateKey('.$row->id.')">Regen Key</button>
                        <button class="btn btn-danger btn-sm" onclick="confirmDelete('.$row->id.')">Delete</button>
                    ';
                })
                ->rawColumns(['status_label', 'options'])
                ->make(true);
        }

        return view('backend.users.integrasi.index');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:100|unique:user_integrasi,username',
            'password' => 'required|string|min:6',
            'client_id' => 'nullable|string|max:64|unique:user_integrasi,client_id',
            'api_key' => 'nullable|string|max:128|unique:user_integrasi,api_key',
            'status' => 'required|in:0,1',
            'keterangan' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()]);
        }

        $adminId = Auth::id();

        UserIntegrasi::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'client_id' => $request->client_id ?: UserIntegrasi::generateClientId(),
            'api_key' => $request->api_key ?: UserIntegrasi::generateApiKey(),
            'status' => (int) $request->status,
            'keterangan' => $request->keterangan,
            'created_by' => $adminId,
            'updated_by' => $adminId,
        ]);

        return response()->json(['success' => true, 'message' => 'User integrasi berhasil ditambahkan']);
    }

    public function show($id)
    {
        $data = UserIntegrasi::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $data->id,
                'username' => $data->username,
                'client_id' => $data->client_id,
                'api_key' => $data->api_key,
                'status' => $data->status,
                'keterangan' => $data->keterangan,
            ],
        ]);
    }

    public function update(Request $request, $id)
    {
        $row = UserIntegrasi::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:100|unique:user_integrasi,username,'.$row->id,
            'password' => 'nullable|string|min:6',
            'client_id' => 'required|string|max:64|unique:user_integrasi,client_id,'.$row->id,
            'api_key' => 'required|string|max:128|unique:user_integrasi,api_key,'.$row->id,
            'status' => 'required|in:0,1',
            'keterangan' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()]);
        }

        $row->username = $request->username;
        $row->client_id = $request->client_id;
        $row->api_key = $request->api_key;
        $row->status = (int) $request->status;
        $row->keterangan = $request->keterangan;
        $row->updated_by = Auth::id();

        if ($request->filled('password')) {
            $row->password = Hash::make($request->password);
        }

        $row->save();

        return response()->json(['success' => true, 'message' => 'User integrasi berhasil diperbarui']);
    }

    public function regenerateKey($id)
    {
        $row = UserIntegrasi::findOrFail($id);
        $row->api_key = UserIntegrasi::generateApiKey();
        $row->updated_by = Auth::id();
        $row->access_token = null;
        $row->refresh_token = null;
        $row->access_token_expires_at = null;
        $row->refresh_token_expires_at = null;
        $row->save();

        return response()->json([
            'success' => true,
            'message' => 'API key berhasil digenerate ulang',
            'api_key' => $row->api_key,
        ]);
    }

    public function destroy($id)
    {
        $row = UserIntegrasi::findOrFail($id);
        $row->updated_by = Auth::id();
        $row->save();
        $row->delete();

        return response()->json(['success' => true, 'message' => 'User integrasi berhasil dihapus']);
    }

    public function generateCredentials()
    {
        return response()->json([
            'success' => true,
            'client_id' => UserIntegrasi::generateClientId(),
            'api_key' => UserIntegrasi::generateApiKey(),
        ]);
    }
}
