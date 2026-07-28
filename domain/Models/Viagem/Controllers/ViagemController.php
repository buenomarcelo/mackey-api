<?php

namespace MAC\Models\Viagem\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use MAC\Models\Viagem\Actions\CreateViagemAction;
use MAC\Models\Viagem\Actions\DeleteViagemAction;
use MAC\Models\Viagem\Actions\ListViagensAction;
use MAC\Models\Viagem\Actions\UpdateViagemAction;
use MAC\Models\Viagem\DTO\ViagemData;
use MAC\Models\Viagem\Requests\StoreViagemRequest;
use MAC\Models\Viagem\Requests\UpdateViagemRequest;
use MAC\Models\Viagem\Resources\ViagemResource;
use MAC\Models\Viagem\Viagem;

class ViagemController extends Controller
{
    public function index(Request $request, ListViagensAction $action): AnonymousResourceCollection
    {
        $viagens = $action->handle(
            motoristaUuid: $request->string('motorista_uuid')->toString() ?: null,
            caminhaoUuid: $request->string('caminhao_uuid')->toString() ?: null,
            statusViagem: $request->string('status_viagem')->toString() ?: null,
            statusPagamento: $request->string('status_pagamento')->toString() ?: null,
            dataInicio: $request->string('data_inicio')->toString() ?: null,
            dataFim: $request->string('data_fim')->toString() ?: null,
            sortBy: $request->string('sort_by')->toString() ?: null,
            descending: $request->boolean('descending'),
            perPage: $request->integer('per_page', 15),
        );

        return ViagemResource::collection($viagens);
    }

    public function store(StoreViagemRequest $request, CreateViagemAction $action): ViagemResource
    {
        $viagem = $action->handle(ViagemData::fromRequest($request));

        return new ViagemResource($viagem);
    }

    public function show(Viagem $viagem): ViagemResource
    {
        return new ViagemResource($viagem->load(['motorista', 'caminhao', 'criadoPor']));
    }

    public function update(UpdateViagemRequest $request, Viagem $viagem, UpdateViagemAction $action): ViagemResource
    {
        $viagem = $action->handle($viagem, ViagemData::fromRequest($request));

        return new ViagemResource($viagem);
    }

    public function destroy(Viagem $viagem, DeleteViagemAction $action): Response
    {
        $action->handle($viagem);

        return response()->noContent();
    }
}
