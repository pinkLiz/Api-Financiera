<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Configuracion;
use Illuminate\Http\Request;

class ConfiguracionController extends Controller
{

    public function show(Request $request)
    {
        $user = $request->user();

        $config = Configuracion::where('user_id', $user->id)->first();

        if (! $config) {
            $config = Configuracion::create([
                'user_id'                    => $user->id,
                'max_porcentaje_gasto_total' => 30.00,
                'max_incremento_mensual'     => 20.00,
            ]);
        }

        return response()->json([
            'estatus'      => 1,
            'mensaje'      => 'Configuración encontrada',
            'configuracion' => $config,
        ]);
    }


    public function update(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'max_porcentaje_gasto_total' => ['sometimes', 'numeric', 'min:0', 'max:100'],
            'max_incremento_mensual'     => ['sometimes', 'numeric', 'min:0', 'max:100'],
        ]);

        $config = Configuracion::where('user_id', $user->id)->first();

        if (! $config) {
            $config = Configuracion::create([
                'user_id'                    => $user->id,
                'max_porcentaje_gasto_total' => 30.00,
                'max_incremento_mensual'     => 20.00,
            ]);
        }

        $config->update($request->only([
            'max_porcentaje_gasto_total',
            'max_incremento_mensual',
        ]));

        return response()->json([
            'estatus'      => 1,
            'mensaje'      => 'Configuración actualizada',
            'configuracion' => $config,
        ]);
    }
}
