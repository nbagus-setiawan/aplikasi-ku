<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TemplateSurat extends Model
{
    use HasFactory;

    protected $table = 'template_surats';

    protected $fillable = [
        'nama',
        'kategori_id',
        'kode_jenis_surat',
        'tujuan_default',
        'perihal_default',
        'isi_template',
        'file_template',
        'is_aktif',
    ];

    protected $casts = [
        'is_aktif' => 'boolean',
    ];

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriSurat::class, 'kategori_id');
    }
}
