<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;

class TestMailController extends Controller
{
    public function send()
    {
        try {
            Mail::raw('Test email dari Etam Kerja - SMTP berhasil!', function ($message) {
                $message->to('edwin@ezrapratama.co.id') // ganti dengan email tujuan
                        ->subject('Test SMTP Laravel');
            });

            return response()->json([
                'success' => true,
                'message' => 'Email berhasil dikirim!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal kirim email',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}