<?php

namespace MAC\Models\Viagem\DTO;

use Illuminate\Http\Request;
use MAC\Models\Viagem\Enums\StatusPagamento;
use MAC\Models\Viagem\Enums\StatusViagem;

final readonly class ViagemData
{
    public function __construct(
        public string $motoristaUuid,
        public string $caminhaoUuid,
        public string $dataViagem,
        public string $origem,
        public string $destino,
        public ?string $contrato,
        public float $peso,
        public float $frete,
        public float $entrada,
        public float $valor2,
        public float $valor3,
        public float $pedagio,
        public ?string $motivo,
        public StatusViagem $statusViagem,
        public StatusPagamento $statusPagamento,
    ) {
    }

    public static function fromRequest(Request $request): self
    {
        return new self(
            motoristaUuid: $request->string('motorista_uuid')->toString(),
            caminhaoUuid: $request->string('caminhao_uuid')->toString(),
            dataViagem: $request->string('data_viagem')->toString(),
            origem: $request->string('origem')->toString(),
            destino: $request->string('destino')->toString(),
            contrato: $request->string('contrato')->toString() ?: null,
            peso: (float) $request->input('peso'),
            frete: (float) $request->input('frete'),
            entrada: (float) $request->input('entrada', 0),
            valor2: (float) $request->input('valor_2', 0),
            valor3: (float) $request->input('valor_3', 0),
            pedagio: (float) $request->input('pedagio', 0),
            motivo: $request->string('motivo')->toString() ?: null,
            statusViagem: StatusViagem::from($request->input('status_viagem', StatusViagem::AGENDADA->value)),
            statusPagamento: StatusPagamento::from($request->input('status_pagamento', StatusPagamento::PENDENTE->value)),
        );
    }

    public function toArray(): array
    {
        return [
            'data' => $this->dataViagem,
            'origem' => $this->origem,
            'destino' => $this->destino,
            'contrato' => $this->contrato,
            'peso' => $this->peso,
            'frete' => $this->frete,
            'entrada' => $this->entrada,
            'valor_2' => $this->valor2,
            'valor_3' => $this->valor3,
            'pedagio' => $this->pedagio,
            'motivo' => $this->motivo,
            'status_viagem' => $this->statusViagem,
            'status_pagamento' => $this->statusPagamento,
        ];
    }
}
