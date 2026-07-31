<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bank extends Model
{
    use Auditable;

    protected $fillable = [
        'name', 'payoff_discount_percent', 'contact', 'phone', 'notes', 'is_active',
    ];

    protected $casts = [
        'payoff_discount_percent' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function analyses(): HasMany
    {
        return $this->hasMany(Analysis::class);
    }
}
