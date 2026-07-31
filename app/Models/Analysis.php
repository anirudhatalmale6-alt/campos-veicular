<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Analysis extends Model
{
    use Auditable;

    protected $fillable = [
        'code', 'client_id', 'bank_id', 'responsible_user_id',
        'vehicle_brand', 'vehicle_model', 'vehicle_year', 'vehicle_plate',
        'vehicle_color', 'vehicle_km', 'vehicle_renavam',
        'fipe_value', 'installments_paid', 'installments_late', 'installments_remaining',
        'installment_value', 'gross_debt', 'payoff_value', 'fines', 'ipva',
        'client_requested_amount', 'commission', 'transport', 'estimated_maintenance',
        'other_costs', 'target_margin_percent',
        'total_acquisition_cost', 'fipe_percent', 'estimated_margin_value',
        'estimated_margin_percent', 'recommended_max_proposal', 'is_viable',
        'status', 'observations', 'converted_vehicle_id', 'converted_at',
    ];

    protected $casts = [
        'fipe_value' => 'decimal:2',
        'installment_value' => 'decimal:2',
        'gross_debt' => 'decimal:2',
        'payoff_value' => 'decimal:2',
        'fines' => 'decimal:2',
        'ipva' => 'decimal:2',
        'client_requested_amount' => 'decimal:2',
        'commission' => 'decimal:2',
        'transport' => 'decimal:2',
        'estimated_maintenance' => 'decimal:2',
        'other_costs' => 'decimal:2',
        'target_margin_percent' => 'decimal:2',
        'total_acquisition_cost' => 'decimal:2',
        'fipe_percent' => 'decimal:2',
        'estimated_margin_value' => 'decimal:2',
        'estimated_margin_percent' => 'decimal:2',
        'recommended_max_proposal' => 'decimal:2',
        'is_viable' => 'boolean',
        'converted_at' => 'datetime',
    ];

    public const STATUSES = [
        'em_analise'    => 'Em análise',
        'em_negociacao' => 'Em negociação',
        'aprovada'      => 'Aprovada',
        'reprovada'     => 'Reprovada',
        'convertida'    => 'Convertida em frota',
    ];

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class);
    }

    public function responsible(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsible_user_id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(AnalysisAttachment::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(AnalysisAttachment::class)->where('type', 'photo');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(AnalysisAttachment::class)->where('type', 'document');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(AnalysisNote::class)->latest();
    }

    public function fleetVehicle(): BelongsTo
    {
        return $this->belongsTo(FleetVehicle::class, 'converted_vehicle_id');
    }

    public function vehicleTitle(): string
    {
        return trim(collect([$this->vehicle_brand, $this->vehicle_model, $this->vehicle_year])
            ->filter()->implode(' ')) ?: 'Veículo sem descrição';
    }
}
