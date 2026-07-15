<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Carbon;
use RobbinThijssen\IdentitySsoKit\Concerns\HasTenantScope;
use RobbinThijssen\IdentitySsoKit\Concerns\HasUuidPrimaryKey;

/**
 * @property string $id
 * @property string $tenant_id
 * @property string $employer_id
 * @property string|null $organizational_unit_id
 * @property string $first_name
 * @property string $last_name
 * @property string|null $email
 * @property string|null $employee_number
 * @property string|null $bsn
 * @property string|null $status
 * @property string|null $source
 * @property Carbon|null $date_of_birth
 * @property string|null $gender
 * @property string|null $nationality
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['tenant_id', 'employer_id', 'organizational_unit_id', 'first_name', 'last_name', 'email', 'employee_number', 'date_of_birth', 'bsn', 'status', 'source', 'gender', 'nationality'])]
class Employee extends Model
{
    use HasTenantScope, HasUuidPrimaryKey;

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'bsn' => 'encrypted',
        ];
    }

    /**
     * @return HasMany<CaseFile, $this>
     */
    public function cases(): HasMany
    {
        return $this->hasMany(CaseFile::class);
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
    public function organizationalUnit(): BelongsTo
    {
        return $this->belongsTo(OrganizationalUnit::class);
    }

    /**
     * @return MorphOne<Address, $this>
     */
    public function address(): MorphOne
    {
        return $this->morphOne(Address::class, 'addressable');
    }
}
