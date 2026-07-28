<?php

namespace MAC\Models\DespesaCaminhao;

use Database\Factories\DespesaCaminhaoFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MAC\Base\Traits\HasUuidRouteKey;
use MAC\Models\Caminhao\Caminhao;
use MAC\Models\User\User;

#[Fillable(['caminhao_id', 'servico', 'valor_pago', 'data', 'criado_por_id'])]
#[Hidden(['id', 'caminhao_id', 'criado_por_id'])]
class DespesaCaminhao extends Model
{
    /** @use HasFactory<DespesaCaminhaoFactory> */
    use HasFactory, HasUuids, HasUuidRouteKey {
        HasUuidRouteKey::uniqueIds insteadof HasUuids;
    }

    protected $table = 'despesas_caminhao';

    protected function casts(): array
    {
        return [
            'valor_pago' => 'decimal:2',
            'data' => 'date',
        ];
    }

    public function caminhao()
    {
        return $this->belongsTo(Caminhao::class);
    }

    public function criadoPor()
    {
        return $this->belongsTo(User::class, 'criado_por_id');
    }
}
