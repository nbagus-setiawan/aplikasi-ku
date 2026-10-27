<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Pengumuman extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pengumumans';

    protected $fillable = [
        'judul',
        'slug',
        'isi',
        'gambar',
        'posisi_gambar',
        'kategori',
        'tgl_tayang',
        'tgl_kedaluwarsa',
        'is_pinned',
        'status',
        'user_id',
    ];

    protected $casts = [
        'tgl_tayang' => 'date',
        'tgl_kedaluwarsa' => 'date',
        'is_pinned' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeTerbitPublik($query)
    {
        $today = Carbon::today()->toDateString();

        return $query->where('status', 'terbit')
            ->whereDate('tgl_tayang', '<=', $today)
            ->where(function ($q) use ($today) {
                $q->whereNull('tgl_kedaluwarsa')
                    ->orWhereDate('tgl_kedaluwarsa', '>=', $today);
            })
            ->orderByDesc('is_pinned')
            ->orderByDesc('tgl_tayang')
            ->orderByDesc('id');
    }

    public function getFormattedTglTayangAttribute()
    {
        return $this->tgl_tayang ? $this->tgl_tayang->format('d-m-Y') : '-';
    }

    public function getFormattedTglKedaluwarsaAttribute()
    {
        return $this->tgl_kedaluwarsa ? $this->tgl_kedaluwarsa->format('d-m-Y') : '-';
    }

    public function getFormattedIsiAttribute(): string
    {
        if (empty($this->isi)) {
            return '';
        }

        $text = $this->isi;

        // Ganti karakter tab dengan indentasi non-breaking spaces
        $text = str_replace("\t", '&nbsp;&nbsp;&nbsp;&nbsp;', $text);

        // Hapus elemen berbahaya bersarang beserta isinya
        $text = preg_replace('/<(script|style|iframe|object|embed|form|meta|link)\b[^>]*>.*?<\/\1>/is', '', $text);
        $text = preg_replace('/<(script|style|iframe|object|embed|form|meta|link)\b[^>]*\/?>/is', '', $text);

        // Bersihkan tag berbahaya dan hanya izinkan tag pemformatan aman
        $clean = strip_tags($text, '<b><strong><i><em><u><br><p><ul><ol><li><blockquote><span><div>');

        // Hapus atribut berbahaya (event handlers & javascript pseudo-protocol)
        $clean = preg_replace('/\s+on[a-z]+\s*=\s*(?:\"[^\"]*\"|\'[^\']*\'|[^\s>]+)/is', '', $clean);
        $clean = preg_replace('/javascript\s*:/i', '', $clean);

        // Terjemahkan format tag khusus [arab]...[/arab]
        $clean = preg_replace('/\[arab\](.*?)\[\/arab\]/is', '<div dir="rtl" class="text-right font-arabic text-[1.08rem] sm:text-[1.12rem] leading-relaxed my-2 text-slate-900">$1</div>', $clean);

        // Terjemahkan format perataan teks [center], [right], [left], [justify]
        // Jika di dalamnya terdapat tulisan Arab, terapkan dir="rtl" dan font-arabic agar harakat dan tanda baca tepat
        $clean = preg_replace_callback('/\[(center|right|left|justify)\](.*?)\[\/\1\]/is', function ($matches) {
            $align = $matches[1];
            $content = $matches[2];
            $isArabic = preg_match('/\p{Arabic}/u', $content);
            $classes = "text-{$align} my-1.5";
            if ($isArabic) {
                $classes .= " font-arabic text-[1.08rem] sm:text-[1.12rem] leading-relaxed";
                return '<div dir="rtl" class="' . $classes . '">' . $content . '</div>';
            }
            return '<div class="' . $classes . '">' . $content . '</div>';
        }, $clean);

        // Terjemahkan format markdown tebal **teks**
        $clean = preg_replace('/\*\*(.*?)\*\*/s', '<strong>$1</strong>', $clean);

        // Terjemahkan format markdown miring *teks* atau _teks_
        $clean = preg_replace('/(?<!\*)\*(?!\*)(.*?)(?<!\*)\*(?!\*)/s', '<em>$1</em>', $clean);
        $clean = preg_replace('/(?<!_)_(?!_)(.*?)(?<!_)_(?!_)/s', '<em>$1</em>', $clean);

        // Format kutipan maklumat > teks
        $clean = preg_replace('/^>\s*(.*)$/m', '<blockquote class="border-l-4 border-emerald-500 pl-3 my-2 italic text-gray-700 bg-emerald-50/50 py-1 rounded-r">$1</blockquote>', $clean);

        // Deteksi otomatis baris yang diawali teks Arab (belum dibungkus div/blockquote)
        $lines = explode("\n", $clean);
        $processedLines = [];
        foreach ($lines as $line) {
            $trimmed = trim($line);
            if ($trimmed !== '' && preg_match('/^\s*[\p{Arabic}]/u', $trimmed) && !preg_match('/^\s*<(div|blockquote)\b/i', $trimmed)) {
                $processedLines[] = '<div dir="rtl" class="text-right font-arabic text-[1.08rem] sm:text-[1.12rem] leading-relaxed my-1.5 text-slate-900">' . $line . '</div>';
            } else {
                $processedLines[] = $line;
            }
        }
        $clean = implode("\n", $processedLines);

        $formatted = nl2br($clean);
        $formatted = preg_replace('/(<div[^>]*>)\s*<br\s*\/?>/i', '$1', $formatted);
        $formatted = preg_replace('/<br\s*\/?>\s*(<\/div>)/i', '$1', $formatted);
        $formatted = preg_replace('/(<\/div>)\s*<br\s*\/?>/i', '$1', $formatted);
        $formatted = preg_replace('/(<blockquote[^>]*>)\s*<br\s*\/?>/i', '$1', $formatted);
        $formatted = preg_replace('/<br\s*\/?>\s*(<\/blockquote>)/i', '$1', $formatted);

        return $formatted;
    }

    public function getCuplikanAttribute(): string
    {
        return $this->cuplikan(120);
    }

    public function cuplikan(int $limit = 120): string
    {
        if (empty($this->isi)) {
            return '';
        }

        $text = $this->isi;

        // Hapus tag format [justify], [center], [left], [right], [arab] dan variannya
        $text = preg_replace('/\[\/?(justify|center|left|right|arab)\]/is', ' ', $text);
        $text = preg_replace('/\[.*?\]/', ' ', $text);

        // Hapus format markdown tebal **, miring *, garis bawah _, dan quote >
        $text = preg_replace('/(\*\*|\*|__|_|`)/', '', $text);
        $text = preg_replace('/^>\s*/m', '', $text);

        // Hapus elemen HTML
        $text = strip_tags($text);

        // Ganti spasi/baris baru berlebih dengan spasi tunggal
        $clean = trim(preg_replace('/\s+/', ' ', $text));

        // Jika awal teks mengulang judul warta persis, lewati judul tersebut agar langsung masuk ke isi
        if (!empty($this->judul)) {
            $cleanJudul = preg_quote(trim($this->judul), '/');
            $clean = trim(preg_replace('/^' . $cleanJudul . '[\s\:\-\?\.\!]*/iu', '', $clean));
        }

        return Str::limit($clean, $limit);
    }

    public function likes()
    {
        return $this->hasMany(PengumumanLike::class);
    }

    public function komentars()
    {
        return $this->hasMany(PengumumanKomentar::class)->where('is_tampil', true)->orderByDesc('created_at');
    }

    public function shares()
    {
        return $this->hasMany(PengumumanShare::class);
    }

    public function isLikedByIp(?string $ip): bool
    {
        if (!$ip) {
            return false;
        }

        return $this->likes()->where('ip_address', $ip)->exists();
    }
}
