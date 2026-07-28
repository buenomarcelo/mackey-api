<?php

namespace MAC\Models\Motorista\DTO;

use Illuminate\Http\Request;

final readonly class MotoristaData
{
    public function __construct(
        public string $nome,
        public string $cpf,
        public string $telefone,
        public string $cnhNumero,
        public string $cnhCategoria,
        public string $cnhValidade,
        public ?string $cep,
        public ?string $logradouro,
        public ?string $numero,
        public ?string $cidade,
        public ?string $uf,
        public float $percentualComissao,
        public int $diaPagamento,
    ) {
    }

    public static function fromRequest(Request $request): self
    {
        return new self(
            nome: $request->string('nome')->toString(),
            cpf: $request->string('cpf')->toString(),
            telefone: $request->string('telefone')->toString(),
            cnhNumero: $request->string('cnh_numero')->toString(),
            cnhCategoria: $request->string('cnh_categoria')->toString(),
            cnhValidade: $request->string('cnh_validade')->toString(),
            cep: $request->string('cep')->toString() ?: null,
            logradouro: $request->string('logradouro')->toString() ?: null,
            numero: $request->string('numero')->toString() ?: null,
            cidade: $request->string('cidade')->toString() ?: null,
            uf: $request->string('uf')->toString() ?: null,
            percentualComissao: (float) $request->input('percentual_comissao'),
            diaPagamento: (int) $request->input('dia_pagamento'),
        );
    }

    public function toArray(): array
    {
        return [
            'nome' => $this->nome,
            'cpf' => $this->cpf,
            'telefone' => $this->telefone,
            'cnh_numero' => $this->cnhNumero,
            'cnh_categoria' => $this->cnhCategoria,
            'cnh_validade' => $this->cnhValidade,
            'cep' => $this->cep,
            'logradouro' => $this->logradouro,
            'numero' => $this->numero,
            'cidade' => $this->cidade,
            'uf' => $this->uf,
            'percentual_comissao' => $this->percentualComissao,
            'dia_pagamento' => $this->diaPagamento,
        ];
    }
}
