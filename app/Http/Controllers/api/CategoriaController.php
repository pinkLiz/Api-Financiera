<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Categorias;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    public function index()
    {
        $categorias = Categorias::all();

        return response()->json([
            'estatus' => 1,
            'categorias' => $categorias
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'tipo'   => ['required', 'in:ingreso,egreso']
        ]);

        $categoria = Categorias::create([
            'nombre' => $request->nombre,
            'tipo'   => $request->tipo
        ]);

        return response()->json([
            'estatus' => 1,
            'mensaje' => 'Categoria creada correctamente',
            'categoria' => $categoria
        ], 201);
    }


    public function show($id)
    {
        $categoria = Categorias::find($id);

        if (!$categoria) {
            return response()->json([
                'estatus' => 0,
                'mensaje' => 'Categoria no encontrada'
            ], 404);
        }

        return response()->json([
            'estatus' => 1,
            'categoria' => $categoria
        ]);
    }


    public function update(Request $request, $id)
    {
        $categoria = Categorias::find($id);

        if (!$categoria) {
            return response()->json([
                'estatus' => 0,
                'mensaje' => 'Categoria no encontrada'
            ], 404);
        }

        $request->validate([
            'nombre' => ['sometimes', 'string', 'max:100'],
            'tipo'   => ['sometimes', 'in:ingreso,egreso']
        ]);

        $categoria->update($request->only(['nombre', 'tipo']));


        return response()->json([
            'estatus' => 1,
            'mensaje' => 'Categoria actualizada',
            'categoria' => $categoria
        ]);
    }

    public function destroy($id)
    {
        $categoria = Categorias::find($id);

        if (!$categoria) {
            return response()->json([
                'estatus' => 0,
                'mensaje' => 'Categoria no encontrada'
            ], 404);
        }

        $categoria->delete();

        return response()->json([
            'estatus' => 1,
            'mensaje' => 'Categoria eliminada'
        ]);

    }
}
