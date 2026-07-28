<?php

namespace MAC\Models\Caminhao\DTO;

use Illuminate\Http\Request;

final readonly class CaminhaoData
{
    public function __construct(
        public string $placa,
        public string $modelo,
        public string $marca,
        public int $ano,
        public float $capacidadeCarga,
        public ?string $renavam,
        public ?string $cor,
    ) {
    }

    public static function fromRequest(Request $request): self
    {
        return new self(
            placa: strtoupper($request->string('placa')->toString()),
            modelo: $request->string('modelo')->toString(),
            marca: $request->string('marca')->toString(),
            ano: (int) $request->input('ano'),
            capacidadeCarga: (float) $request->input('capacidade_carga'),
            renavam: $request->string('renavam')->toString() ?: null,
            cor: $request->string('cor')->toString() ?: null,
        );
    }

    public function toArray(): array
    {
        return [
            'placa' => $this->placa,
            'modelo' => $this->modelo,
            'marca' => $this->marca,
            'ano' => $this->ano,
            'capacidade_carga' => $this->capacidadeCarga,
            'renavam' => $this->renavam,
            'cor' => $this->cor,
        ];
    }
}
