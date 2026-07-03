<?php

namespace App\Models;

use RobbinThijssen\IdentitySsoKit\Concerns\HasTenantScope;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['tenant_id', 'employer_id', 'initiated_by_user_id', 'initiated_by_app', 'file_type', 'status', 'total_rows', 'success_count', 'error_count', 'error_log'])]
class EmployeeImport extends Model
{
    use HasTenantScope;

    protected function casts(): array
    {
        return [
            'error_log' => 'array',
        ];
    }

    /**
     * @return BelongsTo<Employer, $this>
     */
    public function employer(): BelongsTo
    {
        return $this->belongsTo(Employer::class);
    }
}
