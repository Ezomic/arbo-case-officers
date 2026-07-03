<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use RobbinThijssen\IdentitySsoKit\Concerns\HasTenantScope;
use RobbinThijssen\IdentitySsoKit\Concerns\HasUuidPrimaryKey;

#[Fillable(['tenant_id', 'employer_id', 'parent_id', 'name', 'is_legal_entity', 'kvk_number'])]
class OrganizationalUnit extends Model
{
    use HasTenantScope, HasUuidPrimaryKey;

    protected function casts(): array
    {
        return [
            'is_legal_entity' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<Employer, $this>
     */
    public function employer(): BelongsTo
    {
        return $this->belongsTo(Employer::class);
    }

    /**
     * @return BelongsTo<OrganizationalUnit, $this>
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(OrganizationalUnit::class, 'parent_id');
    }

    /**
     * @return HasMany<OrganizationalUnit, $this>
     */
    public function children(): HasMany
    {
        return $this->hasMany(OrganizationalUnit::class, 'parent_id');
    }

    /**
     * @return HasMany<Employee, $this>
     */
    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    /**
     * IDs of this unit and everything beneath it — used to stop a unit
     * being re-parented under itself or one of its own descendants.
     *
     * @return array<int, string>
     */
    public function selfAndDescendantIds(): array
    {
        $ids = [$this->id];

        foreach ($this->children as $child) {
            $ids = [...$ids, ...$child->selfAndDescendantIds()];
        }

        return $ids;
    }
}
