<?php

namespace MAC\Models\Caminhao\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use MAC\Models\Caminhao\Actions\CreateCaminhaoAction;
use MAC\Models\Caminhao\Actions\InactivateCaminhaoAction;
use MAC\Models\Caminhao\Actions\ListCaminhoesAction;
use MAC\Models\Caminhao\Actions\UpdateCaminhaoAction;
use MAC\Models\Caminhao\Caminhao;
use MAC\Models\Caminhao\DTO\CaminhaoData;
use MAC\Models\Caminhao\Requests\StoreCaminhaoRequest;
use MAC\Models\Caminhao\Requests\UpdateCaminhaoRequest;
use MAC\Models\Caminhao\Resources\CaminhaoResource;

class CaminhaoController extends Controller
{
    public function index(Request $request, ListCaminhoesAction $action): AnonymousResourceCollection
    {
        $caminhoes = $action->handle(
            search: $request->string('search')->toString() ?: null,
            somenteAtivos: $request->boolean('somente_ativos'),
            sortBy: $request->string('sort_by')->toString() ?: null,
            descending: $request->boolean('descending'),
            perPage: $request->integer('per_page', 15),
        );

        return CaminhaoResource::collection($caminhoes);
    }

    public function store(StoreCaminhaoRequest $request, CreateCaminhaoAction $action): CaminhaoResource
    {
        $caminhao = $action->handle(CaminhaoData::fromRequest($request));

        return new CaminhaoResource($caminhao->load('criadoPor'));
    }

    public function show(Caminhao $caminhao): CaminhaoResource
    {
        return new CaminhaoResource($caminhao->load('criadoPor'));
    }

    public function update(UpdateCaminhaoRequest $request, Caminhao $caminhao, UpdateCaminhaoAction $action): CaminhaoResource
    {
        $caminhao = $action->handle($caminhao, CaminhaoData::fromRequest($request));

        return new CaminhaoResource($caminhao);
    }

    public function destroy(Caminhao $caminhao, InactivateCaminhaoAction $action): Response
    {
        $action->handle($caminhao);

        return response()->noContent();
    }
}
