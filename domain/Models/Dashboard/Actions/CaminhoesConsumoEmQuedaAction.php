<?php

namespace MAC\Models\Dashboard\Actions;

use MAC\Models\Caminhao\Caminhao;

final class CaminhoesConsumoEmQuedaAction
{
    /**
     * @return array<int, array{caminhao: Caminhao, consumo_recente: float, consumo_anterior: float, variacao_percentual: float}>
     */
    public function handle(int $tamanhoJanela = 3, float $limiteQuedaPercentual = 10.0): array
    {
        $resultado = [];

        foreach (Caminhao::query()->where('ativo', true)->get() as $caminhao) {
            $registros = $caminhao->abastecimentos()
                ->whereNotNull('km')
                ->orderBy('km')
                ->get();

            $consumos = $registros->map(fn ($abastecimento) => $abastecimento->consumoKmL())
                ->filter(fn ($consumo) => ! is_null($consumo))
                ->values();

            if ($consumos->count() < $tamanhoJanela * 2) {
                continue;
            }

            $recentes = $consumos->slice(-$tamanhoJanela);
            $anteriores = $consumos->slice(-$tamanhoJanela * 2, $tamanhoJanela);

            $mediaRecente = $recentes->avg();
            $mediaAnterior = $anteriores->avg();

            if ($mediaAnterior <= 0) {
                continue;
            }

            $variacao = (($mediaRecente - $mediaAnterior) / $mediaAnterior) * 100;

            if ($variacao <= -$limiteQuedaPercentual) {
                $resultado[] = [
                    'caminhao' => $caminhao,
                    'consumo_recente' => round($mediaRecente, 2),
                    'consumo_anterior' => round($mediaAnterior, 2),
                    'variacao_percentual' => round($variacao, 2),
                ];
            }
        }

        return $resultado;
    }
}
