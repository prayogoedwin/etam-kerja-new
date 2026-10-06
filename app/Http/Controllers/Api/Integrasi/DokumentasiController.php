<?php

namespace App\Http\Controllers\Api\Integrasi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DokumentasiController extends Controller
{
    public function loginForm()
    {
        if (session()->has('docs_integrasi_id')) {
            return redirect()->route('docs.api.index');
        }

        return view('docs.api.login');
    }

    public function index(Request $request)
    {
        if (! $request->session()->has('docs_integrasi_id')) {
            return redirect()->route('docs.api.login');
        }

        return view('docs.api.index', [
            'username' => $request->session()->get('docs_integrasi_username'),
            'openapiUrl' => route('docs.api.openapi'),
        ]);
    }

    public function openapi(Request $request): Response
    {
        if (! $request->session()->has('docs_integrasi_id')) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        $path = storage_path('api-docs/integrasi-openapi.json');

        if (! is_file($path)) {
            return response()->json([
                'status' => false,
                'message' => 'OpenAPI spec tidak ditemukan',
            ], 404);
        }

        $spec = json_decode(file_get_contents($path), true);

        $spec['servers'] = [
            [
                'url' => rtrim($request->getSchemeAndHttpHost(), '/').'/api/v1/integrasi',
                'description' => 'Server saat ini',
            ],
        ];

        return response()->json($spec)
            ->header('Content-Type', 'application/json')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate');
    }
}
