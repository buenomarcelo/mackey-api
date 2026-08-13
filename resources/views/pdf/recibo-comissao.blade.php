<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Recibo de Comissão</title>
    <style>
        body { font-family: Helvetica, Arial, sans-serif; font-size: 13px; color: #1a1a1a; }
        h1 { font-size: 18px; color: #0F2A4A; border-bottom: 2px solid #0F2A4A; padding-bottom: 8px; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        td { padding: 6px 0; }
        td.label { color: #555; width: 220px; }
        .valor { font-size: 20px; font-weight: bold; color: #0F2A4A; margin-top: 24px; }
        .assinatura { margin-top: 60px; border-top: 1px solid #999; width: 300px; text-align: center; padding-top: 4px; }
    </style>
</head>
<body>
    <h1>Recibo de Comissão — VM Transportes</h1>

    <table>
        <tr><td class="label">Motorista</td><td>{{ $conta->motorista->nome }}</td></tr>
        <tr><td class="label">CPF</td><td>{{ $conta->motorista->cpf }}</td></tr>
        <tr><td class="label">Viagem</td><td>{{ $conta->viagem->origem }} → {{ $conta->viagem->destino }}</td></tr>
        <tr><td class="label">Contrato</td><td>{{ $conta->viagem->contrato }}</td></tr>
        <tr><td class="label">Data da viagem</td><td>{{ $conta->viagem->data?->format('d/m/Y') }}</td></tr>
        <tr><td class="label">Status</td><td>{{ $conta->status->value === 'pago' ? 'Pago' : 'Pendente' }}</td></tr>
        <tr><td class="label">Data do pagamento</td><td>{{ $conta->data_pagamento?->format('d/m/Y') ?? '-' }}</td></tr>
    </table>

    <p class="valor">Valor da comissão: R$ {{ number_format((float) $conta->valor_comissao, 2, ',', '.') }}</p>

    <div class="assinatura">Assinatura do motorista</div>
</body>
</html>
