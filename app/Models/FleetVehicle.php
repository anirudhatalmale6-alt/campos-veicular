<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FleetVehicle extends Model
{
    use Auditable;

    protected $fillable = [
        'analysis_id', 'brand', 'model', 'year', 'plate', 'color', 'km',
        'renavam', 'fipe_value', 'acquisition_cost', 'status', 'notes',
    ];

    protected $casts = [
        'fipe_value' => 'decimal:2',
        'acquisition_cost' => 'decimal:2',
    ];

    public const STATUSES = [
        'disponivel' => 'Disponível',
        'alugado'    => 'Alugado',
        'manutencao' => 'Em manutenção',
        'inativo'    => 'Inativo',
    ];

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function analysis(): BelongsTo
    {
        return $this->belongsTo(Analysis::class);
    }

    public function title(): string
    {
        return trim(collect([$this->brand, $this->model, $this->year])
            ->filter()->implode(' ')) ?: 'Veículo';
    }
}
