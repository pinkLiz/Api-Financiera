<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AnalisisService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ConsejosController extends Controller
{
    public function __construct(
        protected AnalisisService $service
    ) {}

    public function index(Request $request)
    {
        $user = $request->user();

        $monthParam = $request->query('month');
        $date = $monthParam
            ? Carbon::parse($monthParam . '-01')
            : now();

        $consejos = $this->service->tips($user, $date);

        return response()->json([
            'estatus'  => 1,
            'mensaje'  => 'Consejos generados',
            'consejos' => $consejos,
        ]);
    }
}
