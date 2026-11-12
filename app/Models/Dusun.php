<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dusun extends Model
{
    use HasFactory;

    protected $table = 'dusun';

    protected $primaryKey = 'id_dusun';

    public $timestamps = false;

    protected $fillable = [
        'nama_dusun',
    ];

    public function warga(): HasMany
    {
        return $this->hasMany(Warga::class, 'id_dusun', 'id_dusun');
    }
}
