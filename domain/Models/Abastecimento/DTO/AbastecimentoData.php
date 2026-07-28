<?php

namespace MAC\Models\Abastecimento\DTO;

use Illuminate\Http\Request;

final readonly class AbastecimentoData
{
    public function __construct(
        public string $motoristaUuid,
        public string $caminhaoUuid,
        public string $dataAbastecimento,
        public ?int $km,
        public float $litragem,
        public float $valorLitro,
        public float $valorEnviado,
        public string $posto,
    ) {
    }

    public static function fromRequest(Request $request): self
    {
        return new self(
            motoristaUuid: $request->string('motorista_uuid')->toString(),
            caminhaoUuid: $request->string('caminhao_uuid')->toString(),
            dataAbastecimento: $request->string('data_abastecimento')->toString(),
            km: $request->filled('km') ? (int) $request->input('km') : null,
            litragem: (float) $request->input('litragem'),
            valorLitro: (float) $request->input('valor_litro'),
            valorEnviado: (float) $request->input('valor_enviado'),
            posto: $request->string('posto')->toString(),
        );
    }

    public function toArray(): array
    {
        return [
            'data' => $this->dataAbastecimento,
            'km' => $this->km,
            'litragem' => $this->litragem,
            'valor_litro' => $this->valorLitro,
            'valor_enviado' => $this->valorEnviado,
            'posto' => $this->posto,
        ];
    }
}
