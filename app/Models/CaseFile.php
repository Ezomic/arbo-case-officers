<?php

namespace App\Models;

use RobbinThijssen\IdentitySsoKit\Concerns\HasTenantScope;
use RobbinThijssen\IdentitySsoKit\Concerns\HasUuidPrimaryKey;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['tenant_id', 'employer_id', 'employee_id', 'case_officer_user_id', 'case_type', 'status', 'opened_at', 'closed_at', 'advice', 'restrictions', 'expected_return_date'])]
class CaseFile extends Model
{
    use HasTenantScope, HasUuidPrimaryKey;

    protected $table = 'cases';

    protected function casts(): array
    {
        return [
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
}
