<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use RobbinThijssen\IdentitySsoKit\Concerns\HasTenantScope;

#[Fillable(['id', 'tenant_id', 'name'])]
class ContractType extends Model
{
    use HasTenantScope;

    public $incrementing = false;

    protected $keyType = 'string';

    /**
     * @return HasMany<ContractTypeCaseType, $this>
     */
    public function caseTypes(): HasMany
    {
        return $this->hasMany(ContractTypeCaseType::class);
    }
}
