<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Configuracion extends Model
{
    use HasFactory;
    protected $table = "configuracion";

    protected $primaryKey = "id";
    public $timestamps = true;
    protected $fillable = [
        'user_id',
        'max_porcentaje_gasto_total',
        'max_incremento_mensual'

    ];

    public function user(): BelongsTo{
        return $this->belongsTo(User::class);
    }

}
