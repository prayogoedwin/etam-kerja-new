<?php

namespace App\Http\Controllers\Api\Integrasi;

use App\Http\Controllers\Controller;
use App\Models\EtamAk1;
use App\Models\UserPencari;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class Ak1Controller extends Controller
{
    public function index(Request $request)
    {
        $idIntegration = $request->attributes->get('id_integration');
        $perPage = min(max((int) $request->input('per_page', 10), 1), 50);

        $query = EtamAk1::query()
            ->whereNull('deleted_at')
            ->orderByDesc('tanggal_cetak');

        if ($request->boolean('mine_only', true)) {
            $query->where('id_integration', $idIntegration);
        }

        if ($request->filled('id_user')) {
            $query->where('id_user', $request->id_user);
        }

        $paginator = $query->paginate($perPage);

        $data = collect($paginator->items())->map(fn ($item) => $this->formatAk1($item));

        return response()->json([
            'status' => true,
            'message' => 'Berhasil get data AK1',
            'id_integration' => $idIntegration,
            'data' => $data,
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
        ]);
    }

    public function show(Request $request, $id)
    {
        $idIntegration = $request->attributes->get('id_integration');

        $ak1 = EtamAk1::whereNull('deleted_at')->find($id);

        if (! $ak1) {
            return response()->json([
                'status' => false,
                'message' => 'Data AK1 tidak ditemukan',
                'data' => null,
            ], 404);
        }

        $pencari = UserPencari::with(['user:id,name,email,whatsapp', 'kabkota:id,name'])
            ->where('user_id', $ak1->id_user)
            ->first();

        return response()->json([
            'status' => true,
            'message' => 'Berhasil get detail AK1',
            'id_integration' => $idIntegration,
            'data' => array_merge($this->formatAk1($ak1), [
                'pencari' => $pencari ? [
                    'id' => $pencari->id,
                    'nik' => $pencari->ktp,
                    'nama' => $pencari->name,
                    'email' => optional($pencari->user)->email,
                    'kabkota' => optional($pencari->kabkota)->name,
                ] : null,
                'url_verifikasi' => route('ak1.view', $ak1->unik_kode),
            ]),
        ]);
    }

    public function store(Request $request)
    {
        $idIntegration = $request->attributes->get('id_integration');

        $validator = Validator::make($request->all(), [
            'id_pencari' => 'required_without:nik|integer',
            'nik' => 'required_without:id_pencari|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'id_pencari atau nik wajib diisi',
                'errors' => $validator->errors(),
                'data' => null,
            ], 422);
        }

        $pencariQuery = UserPencari::whereNull('deleted_at');
        if ($request->filled('id_pencari')) {
            $pencariQuery->where('id', $request->id_pencari);
        } else {
            $pencariQuery->where('ktp', $request->nik);
        }

        $pencari = $pencariQuery->first();

        if (! $pencari) {
            return response()->json([
                'status' => false,
                'message' => 'Pencari kerja tidak ditemukan',
                'data' => null,
            ], 404);
        }

        try {
            $existing = EtamAk1::where('id_user', $pencari->user_id)
                ->where('berlaku_hingga', '>', Carbon::now())
                ->whereNull('deleted_at')
                ->first();

            if ($existing) {
                return response()->json([
                    'status' => true,
                    'message' => 'AK1 masih berlaku, data existing dikembalikan',
                    'data' => $this->formatAk1($existing),
                ]);
            }

            $uniqueCode = md5($pencari->id.Carbon::now()->toDateTimeString());
            $expiredDate = Carbon::now()->addMonths(6);

            $ak1 = new EtamAk1;
            $ak1->id_user = $pencari->user_id;
            $ak1->tanggal_cetak = Carbon::now();
            $ak1->berlaku_hingga = $expiredDate;
            $ak1->status_cetak = '1';
            $ak1->unik_kode = $uniqueCode;
            $ak1->dicetak_oleh = null;
            $ak1->id_integration = $idIntegration;

            $qrData = route('ak1.view', $ak1->unik_kode);
            $qrCode = QrCode::size(200)->generate($qrData);
            $qrPath = 'qrcodes/'.$uniqueCode.'.svg';
            Storage::disk('public')->put($qrPath, $qrCode);
            $ak1->qr = $qrPath;
            $ak1->save();

            return response()->json([
                'status' => true,
                'message' => 'Berhasil menambah AK1',
                'data' => $this->formatAk1($ak1),
            ], 201);
        } catch (\Throwable $e) {
            Log::error('API AK1 store: '.$e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Gagal menambah AK1',
                'data' => null,
            ], 500);
        }
    }

    private function formatAk1(EtamAk1 $ak1): array
    {
        return [
            'id' => $ak1->id,
            'id_user' => $ak1->id_user,
            'tanggal_cetak' => $ak1->tanggal_cetak,
            'berlaku_hingga' => $ak1->berlaku_hingga,
            'status_cetak' => $ak1->status_cetak,
            'unik_kode' => $ak1->unik_kode,
            'qr' => $ak1->qr ? asset('storage/'.$ak1->qr) : null,
            'id_integration' => $ak1->id_integration,
        ];
    }
}
