<?php

namespace MAC\Models\ContaPagarMotorista\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use MAC\Models\ContaPagarMotorista\Actions\GerarReciboAction;
use MAC\Models\ContaPagarMotorista\Actions\ListContasPagarAction;
use MAC\Models\ContaPagarMotorista\Actions\MarcarComoPagoAction;
use MAC\Models\ContaPagarMotorista\Actions\ResumoContasPagarAction;
use MAC\Models\ContaPagarMotorista\ContaPagarMotorista;
use MAC\Models\ContaPagarMotorista\Resources\ContaPagarMotoristaResource;

class ContaPagarMotoristaController extends Controller
{
    public function index(Request $request, ListContasPagarAction $action): AnonymousResourceCollection
    {
        $contas = $action->handle(
            motoristaUuid: $request->string('motorista_uuid')->toString() ?: null,
            status: $request->string('status')->toString() ?: null,
            dataInicio: $request->string('data_inicio')->toString() ?: null,
            dataFim: $request->string('data_fim')->toString() ?: null,
            dataVencimento: $request->string('data_vencimento')->toString() ?: null,
            sortBy: $request->string('sort_by')->toString() ?: null,
            descending: $request->boolean('descending'),
            perPage: $request->integer('per_page', 15),
        );

        return ContaPagarMotoristaResource::collection($contas);
    }

    public function resumo(Request $request, ResumoContasPagarAction $action): JsonResponse
    {
        $resumo = $action->handle(
            ano: $request->integer('ano', now()->year),
            mes: $request->integer('mes', now()->month),
        );

        return response()->json(['data' => $resumo]);
    }

    public function show(ContaPagarMotorista $contaPagarMotorista): ContaPagarMotoristaResource
    {
        return new ContaPagarMotoristaResource($contaPagarMotorista->load(['motorista', 'viagem', 'criadoPor']));
    }

    public function marcarPago(ContaPagarMotorista $contaPagarMotorista, MarcarComoPagoAction $action): ContaPagarMotoristaResource
    {
        $conta = $action->handle($contaPagarMotorista);

        return new ContaPagarMotoristaResource($conta->load(['motorista', 'viagem']));
    }

    public function recibo(ContaPagarMotorista $contaPagarMotorista, GerarReciboAction $action)
    {
        return $action->handle($contaPagarMotorista)->stream('recibo-comissao.pdf');
    }
}
