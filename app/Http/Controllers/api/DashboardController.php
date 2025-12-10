<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AnalisisService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function __construct(
        protected AnalisisService $service
    ) {}

    public function resumen(Request $request)
    {
        $user = $request->user();

        $monthParam = $request->query('month');
        $date = $monthParam
            ? Carbon::parse($monthParam . '-01')
            : now();

        $summary = $this->service->summaryMonth($user, $date);
        $saldoProyectado = $this->service->cashFuture($user, $date);

        $summary['month'] = $date->format('Y-m');
        $summary['saldo_proximo_mes'] = $saldoProyectado;


        return response()->json([
            'estatus'  => 1,
            'mensaje'  => 'Resumen mensual',
            'resumen'  => $summary,
        ]);
    }
}
