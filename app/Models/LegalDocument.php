<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LegalDocument extends Model
{
    use HasFactory;

    protected $table = 'legal_documents';

    protected $fillable = [
        'nama_dokumen',
        'nomor',
        'penerbit',
        'berlaku_sampai',
        'status',
        'catatan',
        'urutan',
    ];

    protected function casts(): array
    {
        return [
            'berlaku_sampai' => 'date',
            'urutan' => 'integer',
        ];
    }
}
