<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    use Auditable;

    protected $fillable = [
        'name', 'document', 'phone', 'whatsapp', 'email',
        'city', 'state', 'address', 'notes',
    ];

    public function analyses(): HasMany
    {
        return $this->hasMany(Analysis::class);
    }
}
