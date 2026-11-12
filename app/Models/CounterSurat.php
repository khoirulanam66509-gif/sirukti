<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CounterSurat extends Model
{
    protected $table = 'counter_surat';

    public $incrementing = false;

    protected $primaryKey = null;

    public $timestamps = false;

    protected $fillable = [
        'id_layanan',
        'tahun',
        'nomor_terakhir',
    ];

    public function layanan(): BelongsTo
    {
        return $this->belongsTo(Layanan::class, 'id_layanan', 'id_layanan');
    }
}
