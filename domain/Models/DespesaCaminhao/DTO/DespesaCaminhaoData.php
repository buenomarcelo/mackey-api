<?php

namespace MAC\Models\DespesaCaminhao\DTO;

use Illuminate\Http\Request;

final readonly class DespesaCaminhaoData
{
    public function __construct(
        public string $caminhaoUuid,
        public string $servico,
        public float $valorPago,
        public string $dataDespesa,
    ) {
    }

    public static function fromRequest(Request $request): self
    {
        return new self(
            caminhaoUuid: $request->string('caminhao_uuid')->toString(),
            servico: $request->string('servico')->toString(),
            valorPago: (float) $request->input('valor_pago'),
            dataDespesa: $request->string('data_despesa')->toString(),
        );
    }

    public function toArray(): array
    {
        return [
            'servico' => $this->servico,
            'valor_pago' => $this->valorPago,
            'data' => $this->dataDespesa,
        ];
    }
}
