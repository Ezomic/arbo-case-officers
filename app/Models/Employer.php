<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use RobbinThijssen\IdentitySsoKit\Concerns\HasTenantScope;
use RobbinThijssen\IdentitySsoKit\Concerns\HasUuidPrimaryKey;

/**
 * @property string $id
 */
#[Fillable(['tenant_id', 'name', 'kvk_number', 'address_line_1', 'address_line_2', 'postal_code', 'city', 'status'])]
class Employer extends Model
{
    use HasTenantScope, HasUuidPrimaryKey;

    protected static function booted(): void
    {
        static::created(function (Employer $employer) {
            $employer->organizationalUnits()->create([
                'tenant_id' => $employer->tenant_id,
                'name' => $employer->name,
                'is_legal_entity' => true,
                'kvk_number' => $employer->kvk_number,
            ]);
        });
    }

    /**
     * @return HasMany<Contract, $this>
     */
    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    /**
     * @return HasMany<Employee, $this>
     */
    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    /**
     * @return HasMany<OrganizationalUnit, $this>
     */
    public function organizationalUnits(): HasMany
    {
        return $this->hasMany(OrganizationalUnit::class);
    }

    /**
     * The root unit auto-created alongside the Employer — used as the
     * fallback when an employee/import row doesn't specify a unit.
     */
    public function defaultOrganizationalUnit(): ?OrganizationalUnit
    {
        return $this->organizationalUnits()->whereNull('parent_id')->oldest()->first();
    }

    /**
     * @return HasMany<ContactPerson, $this>
     */
    public function contactPersons(): HasMany
    {
        return $this->hasMany(ContactPerson::class);
    }
}
