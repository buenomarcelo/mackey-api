<?php

namespace MAC\Models\ContaPagarMotorista\Actions;

use Barryvdh\DomPDF\Facade\Pdf;
use MAC\Models\ContaPagarMotorista\ContaPagarMotorista;

final class GerarReciboAction
{
    public function handle(ContaPagarMotorista $conta): \Barryvdh\DomPDF\PDF
    {
        $conta->loadMissing(['motorista', 'viagem']);

        return Pdf::loadView('pdf.recibo-comissao', ['conta' => $conta]);
    }
}
