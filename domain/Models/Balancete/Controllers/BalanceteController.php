<?php

namespace MAC\Models\Balancete\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use MAC\Models\Balancete\Actions\CalcularBalanceteAction;
use MAC\Models\Balancete\Requests\BalanceteFilterRequest;

class BalanceteController extends Controller
{
    public function index(BalanceteFilterRequest $request, CalcularBalanceteAction $action): JsonResponse
    {
        $balancete = $action->handle(
            caminhaoUuid: $request->string('caminhao_uuid')->toString() ?: null,
            dataInicio: $request->string('data_inicio')->toString() ?: null,
            dataFim: $request->string('data_fim')->toString() ?: null,
        );

        return response()->json(['data' => $balancete]);
    }
}
