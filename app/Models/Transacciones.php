<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transacciones extends Model
{
    use HasFactory;
    protected $table = "transacciones";

    protected $primaryKey = "id";
    public $timestamps = true;
    protected $fillable = [
        'user_id',
        'categoria_id',
        'tipo',
        'monto',
        'fecha',
        'descripcion'
    ];

    protected $casts = [
        'fecha' => 'date'
    ];

    public function user(): BelongsTo{
        return $this->belongsTo(User::class);
    }

    public function categoria(): BelongsTo {
        return $this->belongsTo(Categorias::class, 'categoria_id');
    }

}
