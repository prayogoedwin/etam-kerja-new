<?php

namespace App\Http\Controllers;

use App\Models\EtamStruktur;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class StrukturController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $datas = EtamStruktur::query()
                ->select('id', 'tipe', 'kode_lokasi', 'kode_bidang', 'nama', 'slug', 'created_at')
                ->orderBy('kode_lokasi')
                ->orderBy('kode_bidang');

            return DataTables::of($datas)
                ->addIndexColumn()
                ->addColumn('tipe_label', function ($row) {
                    return $row->tipe_label;
                })
                ->addColumn('created_at_fmt', function ($row) {
                    return optional($row->created_at)->format('d-m-Y H:i');
                })
                ->make(true);
        }

        return view('backend.setting.struktur.index');
    }
}
