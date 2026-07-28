<?php

namespace MAC\Models\Caminhao;

use Database\Factories\CaminhaoFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MAC\Base\Traits\HasUuidRouteKey;
use MAC\Models\Abastecimento\Abastecimento;
use MAC\Models\DespesaCaminhao\DespesaCaminhao;
use MAC\Models\User\User;
use MAC\Models\Viagem\Viagem;

#[Fillable(['placa', 'modelo', 'marca', 'ano', 'capacidade_carga', 'renavam', 'cor', 'ativo', 'criado_por_id'])]
#[Hidden(['id', 'criado_por_id'])]
class Caminhao extends Model
{
    /** @use HasFactory<CaminhaoFactory> */
    use HasFactory, HasUuids, HasUuidRouteKey {
        HasUuidRouteKey::uniqueIds insteadof HasUuids;
    }

    protected $table = 'caminhoes';

    protected function casts(): array
    {
        return [
            'ano' => 'integer',
            'capacidade_carga' => 'decimal:2',
            'ativo' => 'boolean',
        ];
    }

    public function viagens()
    {
        return $this->hasMany(Viagem::class);
    }

    public function abastecimentos()
    {
        return $this->hasMany(Abastecimento::class);
    }

    public function despesas()
    {
        return $this->hasMany(DespesaCaminhao::class);
    }

    public function criadoPor()
    {
        return $this->belongsTo(User::class, 'criado_por_id');
    }
}
