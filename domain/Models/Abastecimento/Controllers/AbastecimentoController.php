<?php

namespace MAC\Models\Abastecimento\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use MAC\Models\Abastecimento\Abastecimento;
use MAC\Models\Abastecimento\Actions\DeleteAbastecimentoAction;
use MAC\Models\Abastecimento\Actions\ListAbastecimentosAction;
use MAC\Models\Abastecimento\Actions\RegistrarAbastecimentoAction;
use MAC\Models\Abastecimento\Actions\UpdateAbastecimentoAction;
use MAC\Models\Abastecimento\DTO\AbastecimentoData;
use MAC\Models\Abastecimento\Requests\StoreAbastecimentoRequest;
use MAC\Models\Abastecimento\Requests\UpdateAbastecimentoRequest;
use MAC\Models\Abastecimento\Resources\AbastecimentoResource;

class AbastecimentoController extends Controller
{
    public function index(Request $request, ListAbastecimentosAction $action): AnonymousResourceCollection
    {
        $abastecimentos = $action->handle(
            motoristaUuid: $request->string('motorista_uuid')->toString() ?: null,
            caminhaoUuid: $request->string('caminhao_uuid')->toString() ?: null,
            data: $request->string('data')->toString() ?: null,
            sortBy: $request->string('sort_by')->toString() ?: null,
            descending: $request->boolean('descending'),
            perPage: $request->integer('per_page', 15),
        );

        return AbastecimentoResource::collection($abastecimentos);
    }

    public function store(StoreAbastecimentoRequest $request, RegistrarAbastecimentoAction $action): AbastecimentoResource
    {
        $abastecimento = $action->handle(AbastecimentoData::fromRequest($request));

        return new AbastecimentoResource($abastecimento);
    }

    public function show(Abastecimento $abastecimento): AbastecimentoResource
    {
        return new AbastecimentoResource($abastecimento->load(['motorista', 'caminhao', 'criadoPor']));
    }

    public function update(UpdateAbastecimentoRequest $request, Abastecimento $abastecimento, UpdateAbastecimentoAction $action): AbastecimentoResource
    {
        $abastecimento = $action->handle($abastecimento, AbastecimentoData::fromRequest($request));

        return new AbastecimentoResource($abastecimento);
    }

    public function destroy(Abastecimento $abastecimento, DeleteAbastecimentoAction $action): Response
    {
        $action->handle($abastecimento);

        return response()->noContent();
    }
}
