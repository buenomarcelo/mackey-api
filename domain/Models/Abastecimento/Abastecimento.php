<?php

namespace MAC\Models\Abastecimento;

use Database\Factories\AbastecimentoFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MAC\Base\Traits\HasUuidRouteKey;
use MAC\Models\Caminhao\Caminhao;
use MAC\Models\Motorista\Motorista;
use MAC\Models\User\User;

#[Fillable([
    'motorista_id', 'caminhao_id', 'data', 'km', 'litragem',
    'valor_litro', 'valor_enviado', 'posto', 'valor_sobrando', 'criado_por_id',
])]
#[Hidden(['id', 'motorista_id', 'caminhao_id', 'criado_por_id'])]
class Abastecimento extends Model
{
    /** @use HasFactory<AbastecimentoFactory> */
    use HasFactory, HasUuids, HasUuidRouteKey {
        HasUuidRouteKey::uniqueIds insteadof HasUuids;
    }

    protected function casts(): array
    {
        return [
            'data' => 'date',
            'km' => 'integer',
            'litragem' => 'decimal:2',
            'valor_litro' => 'decimal:3',
            'valor_enviado' => 'decimal:2',
            'valor_sobrando' => 'decimal:2',
        ];
    }

    public function motorista()
    {
        return $this->belongsTo(Motorista::class);
    }

    public function caminhao()
    {
        return $this->belongsTo(Caminhao::class);
    }

    public function criadoPor()
    {
        return $this->belongsTo(User::class, 'criado_por_id');
    }

    /**
     * Consumo (km/l) desde o abastecimento anterior com km registrado para o
     * mesmo caminhão. Null se este registro não tem km ou não há um anterior.
     */
    public function consumoKmL(): ?float
    {
        if (is_null($this->km)) {
            return null;
        }

        $anterior = static::query()
            ->where('caminhao_id', $this->caminhao_id)
            ->whereNotNull('km')
            ->where('km', '<', $this->km)
            ->orderByDesc('km')
            ->first();

        if (! $anterior || (float) $this->litragem <= 0) {
            return null;
        }

        $kmRodado = $this->km - $anterior->km;

        return round($kmRodado / (float) $this->litragem, 2);
    }
}
