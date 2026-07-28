<?php

namespace MAC\Models\Motorista\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use MAC\Models\Abastecimento\Actions\CalcularSaldoMotoristaAction;
use MAC\Models\Motorista\Actions\CreateMotoristaAction;
use MAC\Models\Motorista\Actions\InactivateMotoristaAction;
use MAC\Models\Motorista\Actions\ListMotoristasAction;
use MAC\Models\Motorista\Actions\UpdateMotoristaAction;
use MAC\Models\Motorista\DTO\MotoristaData;
use MAC\Models\Motorista\Motorista;
use MAC\Models\Motorista\Requests\StoreMotoristaRequest;
use MAC\Models\Motorista\Requests\UpdateMotoristaRequest;
use MAC\Models\Motorista\Resources\MotoristaResource;

class MotoristaController extends Controller
{
    public function index(Request $request, ListMotoristasAction $action): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $motoristas = $action->handle(
            search: $request->string('search')->toString() ?: null,
            somenteAtivos: $request->boolean('somente_ativos'),
            sortBy: $request->string('sort_by')->toString() ?: null,
            descending: $request->boolean('descending'),
            perPage: $request->integer('per_page', 15),
        );

        return MotoristaResource::collection($motoristas);
    }

    public function store(StoreMotoristaRequest $request, CreateMotoristaAction $action): MotoristaResource
    {
        $motorista = $action->handle(MotoristaData::fromRequest($request));

        return new MotoristaResource($motorista->load('criadoPor'));
    }

    public function show(Motorista $motorista): MotoristaResource
    {
        return new MotoristaResource($motorista->load('criadoPor'));
    }

    public function update(UpdateMotoristaRequest $request, Motorista $motorista, UpdateMotoristaAction $action): MotoristaResource
    {
        $motorista = $action->handle($motorista, MotoristaData::fromRequest($request));

        return new MotoristaResource($motorista);
    }

    public function destroy(Motorista $motorista, InactivateMotoristaAction $action): Response
    {
        $action->handle($motorista);

        return response()->noContent();
    }

    public function saldoAbastecimento(Motorista $motorista, CalcularSaldoMotoristaAction $action): JsonResponse
    {
        return response()->json(['data' => ['saldo' => $action->handle($motorista)]]);
    }
}
