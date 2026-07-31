<?php

namespace App\Models\Concerns;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

/**
 * Registra automaticamente quem criou/alterou/excluiu cada registro,
 * com data e horario, atendendo ao requisito de log de auditoria.
 */
trait Auditable
{
    public static function bootAuditable(): void
    {
        static::created(function ($model) {
            $model->writeAudit('created', null, $model->getAttributes());
        });

        static::updated(function ($model) {
            $changes = $model->getChanges();
            unset($changes['updated_at']);
            if (empty($changes)) {
                return;
            }
            $old = array_intersect_key($model->getOriginal(), $changes);
            $model->writeAudit('updated', $old, $changes);
        });

        static::deleted(function ($model) {
            $model->writeAudit('deleted', $model->getOriginal(), null);
        });
    }

    public function writeAudit(string $event, ?array $old, ?array $new): void
    {
        AuditLog::create([
            'user_id'        => Auth::id(),
            'event'          => $event,
            'auditable_type' => static::class,
            'auditable_id'   => $this->getKey(),
            'old_values'     => $old,
            'new_values'     => $new,
            'ip_address'     => Request::ip(),
        ]);
    }
}
