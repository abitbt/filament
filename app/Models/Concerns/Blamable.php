<?php

namespace App\Models\Concerns;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

/**
 * @mixin Model
 */
trait Blamable
{
    public static function bootBlamable(): void
    {
        static::creating(static function ($model): void {
            if (Auth::check() && $authId = Auth::id()) {
                $model->created_by = $model->created_by ?? $authId;
                $model->updated_by = $model->updated_by ?? $authId;
            }
        });

        static::updating(static function ($model): void {
            if (Auth::check() && $authId = Auth::id()) {
                $model->updated_by = $authId;
            }
        });
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
