<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class TrackVisitors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->ip();
        $userAgent = $request->header('User-Agent');
        $currentDate = now()->toDateString();

        // Cek apakah ada record untuk IP ini di hari ini
        $visitor = DB::table('visitors')
            ->where('ip_address', $ip)
            ->whereDate('created_at', $currentDate)
            ->first();
        if ($visitor) {
            // Jika ada, update user agent dan waktu
            DB::table('visitors')
                ->where('id', $visitor->id)
                ->update([
                    'updated_at' => now(),
                ]);
        } else {
            // Jika tidak ada, buat record baru
            DB::table('visitors')->insert([
                'ip_address' => $ip,
                'user_agent' => $userAgent,
                'created_at' => now(),
            ]);
        }
        return $next($request);
    }
}
