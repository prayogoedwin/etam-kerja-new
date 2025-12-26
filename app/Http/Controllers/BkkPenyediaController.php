<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserBkk;
use Yajra\DataTables\Facades\DataTables;

class BkkPenyediaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $bkks = UserBkk::whereNull('deleted_at')->get();

            return DataTables::of($bkks)
                ->addIndexColumn()
                ->addColumn('options', function ($bkk) {
                    //<button class="btn btn-danger btn-sm" onclick="confirmDelete(' . $loker->id . ')">Delete</button>
                    return '
                        <a href="' . route('lowongan.pelamar', encode_url($bkk->id)) . '" class="btn btn-info btn-sm"><i class="fa fa-users"></i></a>
                    ';
                })
                ->rawColumns(['options'])  // Pastikan menambahkan ini untuk kolom options
                ->make(true);
        }

        return view('backend.bkkpenyedia.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
