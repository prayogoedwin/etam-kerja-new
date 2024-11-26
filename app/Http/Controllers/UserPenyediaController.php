<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserPenyedia;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class UserPenyediaController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $penyedias = UserPenyedia::with([
                'user:id,name,email,whatsapp',
                'user.roles:id,name'// Ambil data role terkait dengan kolom tertentu
            ]) // Ambil data admin dengan user terkait
                ->select('id', 'user_id');

            return DataTables::of($penyedias)
                ->addIndexColumn()
                ->addColumn('user_name', function ($penyedia) {
                    return $penyedia->user ? $penyedia->user->name : 'N/A';
                })
                ->addColumn('email', function ($penyedia) {
                    return $penyedia->user ? $penyedia->user->email : 'N/A';
                })
                ->addColumn('whatsapp', function ($pencari) {
                    return $penyedia->user ? $penyedia->user->whatsapp : 'N/A';
                })
                // ->addColumn('roles', function ($pencari) {
                //     // Menampilkan nama role
                //     if ($pencari->user && $pencari->user->roles->isNotEmpty()) {
                //         return $pencari->user->roles->pluck('name')->join(', ');
                //     }
                //     return 'N/A'; // Jika tidak ada role
                // })
                ->addColumn('options', function ($penyedia) {
                    // return '
                    //     <button class="btn btn-warning btn-sm" onclick="resetPassword(' . $pencari->id . ')">Reset Password</button>
                    //     <button class="btn btn-primary btn-sm" onclick="showEditModal(' . $pencari->id . ')">Edit</button>
                    //     <button class="btn btn-danger btn-sm" onclick="confirmDelete(' . $pencari->id . ')">Delete</button>
                    // ';
                    return '
                    <button class="btn btn-warning btn-sm" onclick="confirmReset(' . $penyedia->id . ')">Reset Password</button>
                    <button class="btn btn-danger btn-sm" onclick="confirmDelete(' . $penyedia->id . ')">Delete</button>
                ';
                })
                ->rawColumns(['options'])  // Pastikan menambahkan ini untuk kolom options
                ->make(true);
        }

        return view('backend.users.penyedia.index');
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
