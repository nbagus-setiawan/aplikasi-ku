<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengurus extends Model
{
    use HasFactory;

    protected $table = 'penguruses';

    protected $fillable = [
        'nama',
        'jabatan',
        'divisi',
        'foto',
        'no_hp',
        'periode_mulai',
        'periode_selesai',
        'is_aktif',
    ];

    protected $casts = [
        'is_aktif' => 'boolean',
    ];

    /**
     * Label teks periode kepengurusan.
     */
    public function getPeriodeTeksAttribute(): string
    {
        if ($this->periode_mulai && $this->periode_selesai) {
            return "{$this->periode_mulai} - {$this->periode_selesai}";
        }
        return $this->periode_mulai ?: ($this->periode_selesai ?: '-');
    }
}
