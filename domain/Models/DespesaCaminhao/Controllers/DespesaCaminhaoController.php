<?php

namespace MAC\Models\DespesaCaminhao\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use MAC\Models\DespesaCaminhao\Actions\CreateDespesaCaminhaoAction;
use MAC\Models\DespesaCaminhao\Actions\DeleteDespesaCaminhaoAction;
use MAC\Models\DespesaCaminhao\Actions\ListDespesasCaminhaoAction;
use MAC\Models\DespesaCaminhao\Actions\UpdateDespesaCaminhaoAction;
use MAC\Models\DespesaCaminhao\DespesaCaminhao;
use MAC\Models\DespesaCaminhao\DTO\DespesaCaminhaoData;
use MAC\Models\DespesaCaminhao\Requests\StoreDespesaCaminhaoRequest;
use MAC\Models\DespesaCaminhao\Requests\UpdateDespesaCaminhaoRequest;
use MAC\Models\DespesaCaminhao\Resources\DespesaCaminhaoResource;

class DespesaCaminhaoController extends Controller
{
    public function index(Request $request, ListDespesasCaminhaoAction $action): AnonymousResourceCollection
    {
        $despesas = $action->handle(
            caminhaoUuid: $request->string('caminhao_uuid')->toString() ?: null,
            data: $request->string('data')->toString() ?: null,
            sortBy: $request->string('sort_by')->toString() ?: null,
            descending: $request->boolean('descending'),
            perPage: $request->integer('per_page', 15),
        );

        return DespesaCaminhaoResource::collection($despesas);
    }

    public function store(StoreDespesaCaminhaoRequest $request, CreateDespesaCaminhaoAction $action): DespesaCaminhaoResource
    {
        $despesa = $action->handle(DespesaCaminhaoData::fromRequest($request));

        return new DespesaCaminhaoResource($despesa);
    }

    public function show(DespesaCaminhao $despesaCaminhao): DespesaCaminhaoResource
    {
        return new DespesaCaminhaoResource($despesaCaminhao->load(['caminhao', 'criadoPor']));
    }

    public function update(UpdateDespesaCaminhaoRequest $request, DespesaCaminhao $despesaCaminhao, UpdateDespesaCaminhaoAction $action): DespesaCaminhaoResource
    {
        $despesa = $action->handle($despesaCaminhao, DespesaCaminhaoData::fromRequest($request));

        return new DespesaCaminhaoResource($despesa);
    }

    public function destroy(DespesaCaminhao $despesaCaminhao, DeleteDespesaCaminhaoAction $action): Response
    {
        $action->handle($despesaCaminhao);

        return response()->noContent();
    }
}
