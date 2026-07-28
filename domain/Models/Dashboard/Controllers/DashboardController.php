<?php

namespace MAC\Models\Dashboard\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use MAC\Models\Caminhao\Resources\CaminhaoResource;
use MAC\Models\ContaPagarMotorista\Resources\ContaPagarMotoristaResource;
use MAC\Models\Dashboard\Actions\CalcularIndicadoresMesAction;
use MAC\Models\Dashboard\Actions\CaminhoesConsumoEmQuedaAction;
use MAC\Models\Dashboard\Actions\CaminhoesSemAbastecimentoAction;
use MAC\Models\Dashboard\Actions\ComissoesAVencerAction;
use MAC\Models\Dashboard\Actions\MotoristasSaldoNegativoAction;
use MAC\Models\Dashboard\Actions\ViagensPagamentoPendenteAction;
use MAC\Models\Motorista\Resources\MotoristaResource;
use MAC\Models\Viagem\Resources\ViagemResource;

class DashboardController extends Controller
{
    public function index(
        CalcularIndicadoresMesAction $indicadoresAction,
        ViagensPagamentoPendenteAction $viagensPendentesAction,
        ComissoesAVencerAction $comissoesAVencerAction,
        MotoristasSaldoNegativoAction $saldoNegativoAction,
        CaminhoesSemAbastecimentoAction $semAbastecimentoAction,
        CaminhoesConsumoEmQuedaAction $consumoEmQuedaAction,
    ): JsonResponse {
        return response()->json(['data' => [
            'indicadores' => $indicadoresAction->handle(),
            'alertas' => [
                'viagens_pagamento_pendente' => ViagemResource::collection($viagensPendentesAction->handle()),
                'comissoes_a_vencer' => collect($comissoesAVencerAction->handle())->map(fn ($item) => [
                    'conta' => new ContaPagarMotoristaResource($item['conta']),
                    'vencida' => $item['vencida'],
                ]),
                'motoristas_saldo_negativo' => collect($saldoNegativoAction->handle())->map(fn ($item) => [
                    'motorista' => new MotoristaResource($item['motorista']),
                    'saldo' => $item['saldo'],
                ]),
                'caminhoes_sem_abastecimento' => collect($semAbastecimentoAction->handle())->map(fn ($item) => [
                    'caminhao' => new CaminhaoResource($item['caminhao']),
                    'ultimo_abastecimento' => $item['ultimo_abastecimento'],
                    'dias_sem_abastecer' => $item['dias_sem_abastecer'],
                ]),
                'caminhoes_consumo_em_queda' => collect($consumoEmQuedaAction->handle())->map(fn ($item) => [
                    'caminhao' => new CaminhaoResource($item['caminhao']),
                    'consumo_recente' => $item['consumo_recente'],
                    'consumo_anterior' => $item['consumo_anterior'],
                    'variacao_percentual' => $item['variacao_percentual'],
                ]),
            ],
        ]]);
    }
}
