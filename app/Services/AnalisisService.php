<?php

namespace App\Services;

use App\Models\Transacciones;
use App\Models\Configuracion;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class AnalisisService
{

    public function summaryMonth(User $user, Carbon $month): array
    {
        $start = $month->copy()->startOfMonth()->toDateString();
        $end   = $month->copy()->endOfMonth()->toDateString();

        $transacciones = Transacciones::with('categoria')
            ->where('user_id', $user->id)
            ->whereBetween('fecha', [$start, $end])
            ->get();

        $totalIngresos = $transacciones
            ->where('tipo', 'ingreso')
            ->sum('monto');

        $totalEgresos = $transacciones
            ->where('tipo', 'egreso')
            ->sum('monto');

        $saldo = $totalIngresos - $totalEgresos;


        $gastosCategoria = $transacciones
            ->where('tipo', 'egreso')
            ->groupBy('categoria_id')
            ->map(function (Collection $items) use ($totalEgresos) {
                $first = $items->first();
                $total = $items->sum('monto');
                $porcentaje = $totalEgresos > 0
                    ? round(($total / $totalEgresos) * 100, 2)
                    : 0;

                return [
                    'categoria_id' => $first->categoria_id,
                    'nombre'       => optional($first->categoria)->nombre,
                    'tipo'         => optional($first->categoria)->tipo,
                    'total'        => $total,
                    'porcentaje'   => $porcentaje,
                ];
            })
            ->values()
            ->all();

        return [
            'month'          => $month->format('Y-m'),
            'total_ingresos' => (float) $totalIngresos,
            'total_egresos'  => (float) $totalEgresos,
            'saldo'          => (float) $saldo,
            'gastos_categoria' => $gastosCategoria,
        ];
    }

    public function tips(User $user, Carbon $month): array
    {
        $config = Configuracion::firstOrCreate(
            ['user_id' => $user->id],
            [
                'max_porcentaje_gasto_total' => 30.00,
                'max_incremento_mensual'     => 20.00,
            ]
        );

        $summaryActual = $this->summaryMonth($user, $month);
        $summaryPrevio = $this->summaryMonth($user, $month->copy()->subMonth());

        $consejos = [];

        $totalIngresos = $summaryActual['total_ingresos'];
        $totalEgresos  = $summaryActual['total_egresos'];

        $gastoComida = collect($summaryActual['gastos_categoria'])
            ->first(function ($cat) {
                return Str::contains(Str::lower($cat['nombre'] ?? ''), 'comida');
            });

        if ($gastoComida && $totalEgresos > 0) {
            if ($gastoComida['porcentaje'] > $config->max_porcentaje_gasto_total) {
                $consejos[] = [
                    'codigo'  => 'GASTO_COMIDA_ALTO',
                    'mensaje' => "Has gastado mucho en comida este mes ({$gastoComida['porcentaje']}% de tus egresos).",
                ];
            }
        }

        if ($totalEgresos > $totalIngresos && ($totalIngresos + $totalEgresos) > 0) {
            $consejos[] = [
                'codigo'  => 'EGRESOS_MAYORES_INGRESOS',
                'mensaje' => 'Tus gastos superan tus ingresos este mes. Considera reducir compras o revisar suscripciones.',
            ];
        }

        $gastosPrevios = $summaryPrevio['total_egresos'];
        if ($gastosPrevios > 0 && $totalEgresos > 0) {
            $incremento = (($totalEgresos - $gastosPrevios) / $gastosPrevios) * 100;

            if ($incremento > $config->max_incremento_mensual) {
                $incRedondeado = round($incremento, 2);
                $consejos[] = [
                    'codigo'  => 'GASTOS_INCREMENTO_ALTO',
                    'mensaje' => "Tus gastos aumentaron {$incRedondeado}% respecto al mes anterior.",
                ];
            }
        }

        $negativos = 0;
        for ($i = 0; $i < 3; $i++) {
            $m = $month->copy()->subMonths($i);
            $s = $this->summaryMonth($user, $m);
            $saldoMes = $s['saldo'];

            if ($saldoMes < 0) {
                $negativos++;
            } else {
                break;
            }
        }

        if ($negativos >= 3) {
            $consejos[] = [
                'codigo'  => 'SALDO_FUTURO_NEGATIVO',
                'mensaje' => 'En los últimos meses has tenido más gastos que ingresos. Tu saldo futuro podría ser negativo si te mantienes asi.',
            ];
        }

        if (! empty($summaryActual['gastos_categoria'])) {
            $maxCat = collect($summaryActual['gastos_categoria'])
                ->sortByDesc('total')
                ->first();

            if ($maxCat && $maxCat['total'] > 0) {
                $consejos[] = [
                    'codigo'  => 'CATEGORIA_MAYOR_GASTO',
                    'mensaje' => "Tu mayor gasto este mes fue en '{$maxCat['nombre']}' con $ {$maxCat['total']}. Revisa si puedes ajustar esa categoría.",
                ];
            }
        }

        return $consejos;
    }

    public function cashFuture(User $user, Carbon $month): float
    {
        $meses = [];
        for ($i = 0; $i < 3; $i++) {
            $m = $month->copy()->subMonths($i);
            $summary = $this->summaryMonth($user, $m);
            $meses[] = $summary['saldo'] ?? 0;
        }

        if (empty($meses)) {
            $summaryActual = $this->summaryMonth($user, $month);
            return (float) ($summaryActual['saldo'] ?? 0);
        }

        $promedio = array_sum($meses) / count($meses);

        return round($promedio, 2);
    }
}
