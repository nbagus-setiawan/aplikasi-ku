<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notifikasi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotifikasiController extends Controller
{
    /**
     * Ambil data ringkasan notifikasi dan jumlah yang belum dibaca (untuk navbar lonceng).
     */
    public function index(): JsonResponse
    {
        $unreadCount = Notifikasi::belumDibaca()->count();

        $notifikasis = Notifikasi::terbaru()
            ->take(15)
            ->get()
            ->map(function ($notif) {
                return [
                    'id' => $notif->id,
                    'judul' => $notif->judul,
                    'pesan' => $notif->pesan,
                    'pesan_singkat' => \Illuminate\Support\Str::limit($notif->pesan, 75),
                    'tipe' => $notif->tipe,
                    'link' => $notif->link,
                    'data_tambahan' => $notif->data_tambahan,
                    'is_dibaca' => $notif->is_dibaca,
                    'waktu' => $notif->created_at->diffForHumans(),
                    'waktu_lengkap' => $notif->created_at->translatedFormat('l, d F Y - H:i') . ' WIB',
                ];
            });

        return response()->json([
            'success' => true,
            'unread_count' => $unreadCount,
            'notifikasis' => $notifikasis,
        ]);
    }

    /**
     * Tandai satu notifikasi sebagai telah dibaca dan ambil pratinjau detailnya.
     */
    public function markAsRead(Notifikasi $notifikasi): JsonResponse
    {
        if (!$notifikasi->is_dibaca) {
            $notifikasi->tandaiDibaca();
        }

        $unreadCount = Notifikasi::belumDibaca()->count();

        return response()->json([
            'success' => true,
            'unread_count' => $unreadCount,
            'notifikasi' => [
                'id' => $notifikasi->id,
                'judul' => $notifikasi->judul,
                'pesan' => $notifikasi->pesan,
                'tipe' => $notifikasi->tipe,
                'link' => $notifikasi->link,
                'data_tambahan' => $notifikasi->data_tambahan,
                'is_dibaca' => $notifikasi->is_dibaca,
                'waktu' => $notifikasi->created_at->diffForHumans(),
                'waktu_lengkap' => $notifikasi->created_at->translatedFormat('l, d F Y - H:i') . ' WIB',
            ],
        ]);
    }

    /**
     * Tandai seluruh notifikasi yang belum dibaca menjadi dibaca.
     */
    public function markAllRead(): JsonResponse
    {
        Notifikasi::belumDibaca()->update([
            'is_dibaca' => true,
            'dibaca_pada' => now(),
        ]);

        return response()->json([
            'success' => true,
            'unread_count' => 0,
            'message' => 'Semua notifikasi telah ditandai sebagai dibaca.',
        ]);
    }

    /**
     * Hapus satu notifikasi.
     */
    public function destroy(Notifikasi $notifikasi): JsonResponse
    {
        $notifikasi->delete();

        $unreadCount = Notifikasi::belumDibaca()->count();

        return response()->json([
            'success' => true,
            'unread_count' => $unreadCount,
            'message' => 'Notifikasi berhasil dihapus.',
        ]);
    }

    /**
     * Hapus banyak notifikasi sekaligus (batch delete).
     */
    public function destroyBatch(Request $request): JsonResponse
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:notifikasis,id',
        ]);

        $ids = $request->input('ids', []);
        Notifikasi::whereIn('id', $ids)->delete();

        $unreadCount = Notifikasi::belumDibaca()->count();

        return response()->json([
            'success' => true,
            'unread_count' => $unreadCount,
            'message' => count($ids) . ' notifikasi berhasil dihapus.',
        ]);
    }

    /**
     * Bersihkan seluruh notifikasi.
     */
    public function destroyAll(): JsonResponse
    {
        Notifikasi::query()->delete();

        return response()->json([
            'success' => true,
            'unread_count' => 0,
            'message' => 'Semua notifikasi berhasil dibersihkan.',
        ]);
    }
}
