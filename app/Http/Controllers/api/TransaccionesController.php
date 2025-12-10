<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transacciones;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class TransaccionesController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $monthParam = $request->query('month');
        $date = $monthParam
            ? Carbon::parse($monthParam . '-01')
            : now();

        $start = $date->copy()->startOfMonth()->toDateString();
        $end   = $date->copy()->endOfMonth()->toDateString();

        $transacciones = Transacciones::with('categoria')
            ->where('user_id', $user->id)
            ->whereBetween('fecha', [$start, $end])
            ->orderBy('fecha', 'desc')
            ->get();

        return response()->json([
            'estatus' => 1,
            'transacciones' => $transacciones,
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'categoria_id' => ['required', 'exists:categorias,id'],
            'tipo'         => ['required', 'in:ingreso,egreso'],
            'monto'        => ['required', 'numeric', 'min:0'],
            'fecha'        => ['required', 'date'],
            'descripcion'  => ['nullable', 'string'],
        ]);

        $data['user_id'] = $user->id;

        $transaccion = Transacciones::create($data);

        $transaccion->load('categoria');

        return response()->json([
            'estatus'     => 1,
            'mensaje'     => 'Transacción creada correctamente',
            'transaccion' => $transaccion,
        ], 201);
    }

    public function show(Request $request, Transacciones $transaccion)
    {
        $this->authorizeOwner($request, $transaccion);

        $transaccion->load('categoria');

        return response()->json([
            'estatus'     => 1,
            'transaccion' => $transaccion,
        ]);
    }

    public function update(Request $request, Transacciones $transaccion)
    {
        $this->authorizeOwner($request, $transaccion);

        $data = $request->validate([
            'categoria_id' => ['sometimes', 'exists:categorias,id'],
            'tipo'         => ['sometimes', 'in:ingreso,egreso'],
            'monto'        => ['sometimes', 'numeric', 'min:0'],
            'fecha'        => ['sometimes', 'date'],
            'descripcion'  => ['nullable', 'string'],
        ]);

        $transaccion->update($data);

        $transaccion->load('categoria');

        return response()->json([
            'estatus'     => 1,
            'mensaje'     => 'Transacción actualizada',
            'transaccion' => $transaccion,
        ]);
    }

    public function destroy(Request $request, Transacciones $transaccion)
    {
        $this->authorizeOwner($request, $transaccion);

        $transaccion->delete();

        return response()->json([
            'estatus' => 1,
            'mensaje' => 'Transacción eliminada',
        ]);
    }

    protected function authorizeOwner(Request $request, Transacciones $transaccion): void
    {
        if ($request->user()->id !== $transaccion->user_id) {
            abort(response()->json([
                'estatus' => 0,
                'mensaje' => 'No autorizado',
            ], 403));
        }
    }
}
