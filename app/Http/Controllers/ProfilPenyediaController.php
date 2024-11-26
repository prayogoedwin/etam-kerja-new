<?php

namespace App\Http\Controllers;

use App\Models\ProfilPenyedia;
use Illuminate\Http\Request;

class ProfilPenyediaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userid = auth()->user()->id;
        $data['sektors'] = getSektor();
        $data['provinsis'] = getProvinsi();
        $data['profil'] = ProfilPenyedia::where('user_id', $userid)->first();

        return view('backend.profilpenyedia.index', $data);
        // echo json_encode($data);
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
