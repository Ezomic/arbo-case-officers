<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use RobbinThijssen\IdentitySsoKit\Concerns\HasTenantScope;
use RobbinThijssen\IdentitySsoKit\Concerns\HasUuidPrimaryKey;

/**
 * @property string $id
 * @property string $tenant_id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['id', 'tenant_id', 'name'])]
class NoteType extends Model
{
    use HasTenantScope, HasUuidPrimaryKey;

    /**
     * @return HasMany<NoteTypePermission, $this>
     */
    public function permissions(): HasMany
    {
        return $this->hasMany(NoteTypePermission::class);
    }

    /**
     * @return HasMany<CaseNote, $this>
     */
    public function notes(): HasMany
    {
        return $this->hasMany(CaseNote::class);
    }

    public function permissionFor(string $role): ?NoteTypePermission
    {
        return $this->permissions->firstWhere('role', $role);
    }

    /**
     * Scope to note types where the user's role has can_read = true.
     *
     * @param  Builder<NoteType>  $query
     * @return Builder<NoteType>
     */
    public function scopeReadableBy(Builder $query, User $user): Builder
    {
        return $query->whereHas('permissions', function (Builder $q) use ($user): void {
            $q->where('role', $user->current_role)->where('can_read', true);
        });
    }

    /**
     * Scope to note types where the user's role has can_write = true.
     *
     * @param  Builder<NoteType>  $query
     * @return Builder<NoteType>
     */
    public function scopeWritableBy(Builder $query, User $user): Builder
    {
        return $query->whereHas('permissions', function (Builder $q) use ($user): void {
            $q->where('role', $user->current_role)->where('can_write', true);
        });
    }
}
