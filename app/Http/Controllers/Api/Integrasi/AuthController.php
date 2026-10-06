<?php

namespace App\Http\Controllers\Api\Integrasi;

use App\Http\Controllers\Controller;
use App\Models\UserIntegrasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function token(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'client_id' => 'required|string',
            'api_key' => 'required_without:x-api-key|string',
            'x-api-key' => 'required_without:api_key|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'client_id dan api_key / x-api-key wajib diisi',
                'errors' => $validator->errors(),
                'data' => null,
            ], 422);
        }

        $apiKey = $request->input('api_key', $request->input('x-api-key'));
        $apiKey = $apiKey ?: $request->header('X-Api-Key');

        $user = UserIntegrasi::where('client_id', $request->client_id)
            ->where('api_key', $apiKey)
            ->where('status', 1)
            ->whereNull('deleted_at')
            ->first();

        if (! $user) {
            return response()->json([
                'status' => false,
                'message' => 'Kombinasi client_id / x-api-key salah',
                'data' => null,
            ], 401);
        }

        $tokens = $user->issueTokens();

        return response()->json([
            'status' => true,
            'message' => 'Berhasil mendapatkan token',
            'data' => array_merge($tokens, [
                'id_integration' => $user->id,
                'keterangan' => 'access_token kadaluarsa dalam 24 jam; refresh_token dalam 30 hari',
            ]),
        ]);
    }

    public function refresh(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'refresh_token' => 'required|string',
            'client_id' => 'required|string',
            'api_key' => 'required_without:x-api-key|string',
            'x-api-key' => 'required_without:api_key|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'refresh_token, client_id, dan api_key wajib diisi',
                'errors' => $validator->errors(),
                'data' => null,
            ], 422);
        }

        $apiKey = $request->input('api_key', $request->input('x-api-key'));
        $apiKey = $apiKey ?: $request->header('X-Api-Key');

        $user = UserIntegrasi::where('client_id', $request->client_id)
            ->where('api_key', $apiKey)
            ->where('status', 1)
            ->whereNull('deleted_at')
            ->first();

        if (! $user || ! $user->isRefreshTokenValid($request->refresh_token)) {
            return response()->json([
                'status' => false,
                'message' => 'Refresh token tidak valid atau sudah kadaluarsa',
                'data' => null,
            ], 401);
        }

        $tokens = $user->issueTokens();

        return response()->json([
            'status' => true,
            'message' => 'Berhasil refresh token',
            'data' => array_merge($tokens, [
                'id_integration' => $user->id,
            ]),
        ]);
    }

    public function docsLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = UserIntegrasi::where('username', $request->username)
            ->where('status', 1)
            ->whereNull('deleted_at')
            ->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return back()->with('error', 'Username atau password salah')->withInput();
        }

        $request->session()->put('docs_integrasi_id', $user->id);
        $request->session()->put('docs_integrasi_username', $user->username);

        return redirect()->route('docs.api.index');
    }

    public function docsLogout(Request $request)
    {
        $request->session()->forget(['docs_integrasi_id', 'docs_integrasi_username']);

        return redirect()->route('docs.api.login');
    }
}
