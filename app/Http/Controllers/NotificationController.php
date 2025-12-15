<?php
// app/Http/Controllers/NotificationController.php

namespace App\Http\Controllers;

use App\Models\EtamNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Halaman semua notifikasi
     */
    public function index()
    {
        $notifications = EtamNotification::where('to_user', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Ambil notifikasi untuk dropdown
     */
    public function getNotifications()
    {
        $notifications = EtamNotification::where('to_user', auth()->id())
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $unreadCount = EtamNotification::where('to_user', auth()->id())
            ->where('is_open', 0)
            ->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount
        ]);
    }

    /**
     * Mark satu notifikasi sebagai dibaca
     */
    public function markAsRead($id)
    {
        $notification = EtamNotification::where('id', $id)
            ->where('to_user', auth()->id())
            ->first();

        if ($notification) {
            $notification->update(['is_open' => 1]);
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 404);
    }

    /**
     * Mark semua notifikasi sebagai dibaca
     */
    public function markAllAsRead()
    {
        EtamNotification::where('to_user', auth()->id())
            ->where('is_open', 0)
            ->update(['is_open' => 1]);

        return response()->json(['success' => true]);
    }

    /**
     * Hapus notifikasi
     */
    public function destroy($id)
    {
        $notification = EtamNotification::where('id', $id)
            ->where('to_user', auth()->id())
            ->first();

        if ($notification) {
            $notification->delete();
            return response()->json(['success' => true, 'message' => 'Notifikasi dihapus']);
        }

        return response()->json(['success' => false, 'message' => 'Notifikasi tidak ditemukan'], 404);
    }
}