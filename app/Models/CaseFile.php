<?php

namespace App\Models;

use App\Enums\CaseType;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use RobbinThijssen\IdentitySsoKit\Concerns\HasTenantScope;
use RobbinThijssen\IdentitySsoKit\Concerns\HasUuidPrimaryKey;

/**
 * @property string $id
 * @property string $tenant_id
 * @property string $employer_id
 * @property string $employee_id
 * @property string|null $case_officer_user_id
 * @property CaseType $case_type
 * @property string $status
 * @property CarbonImmutable $opened_at
 * @property CarbonImmutable|null $closed_at
 * @property string|null $advice
 * @property string|null $restrictions
 * @property CarbonImmutable|null $expected_return_date
 */
#[Fillable(['tenant_id', 'employer_id', 'employee_id', 'case_officer_user_id', 'case_type', 'status', 'opened_at', 'closed_at', 'advice', 'restrictions', 'expected_return_date'])]
class CaseFile extends Model
{
    use HasTenantScope, HasUuidPrimaryKey;

    protected $table = 'cases';

    protected function casts(): array
    {
        return [
            'case_type' => CaseType::class,
            'opened_at' => 'datetime',
            'closed_at' => 'datetime',
            'expected_return_date' => 'date',
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
     * @return BelongsTo<Employee, $this>
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /** @return HasMany<CaseNote, $this> */
    public function notes(): HasMany
    {
        return $this->hasMany(CaseNote::class, 'case_id');
    }

    /** @return HasMany<CaseTask, $this> */
    public function tasks(): HasMany
    {
        return $this->hasMany(CaseTask::class, 'case_id');
    }

    /** @return BelongsTo<User, $this> */
    public function assignedOfficer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'case_officer_user_id');
    }
}
