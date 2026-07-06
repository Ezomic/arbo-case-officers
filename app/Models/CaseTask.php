<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use RobbinThijssen\IdentitySsoKit\Concerns\HasUuidPrimaryKey;

/**
 * @property string $id
 * @property string $case_id
 * @property string|null $task_type_id
 * @property string|null $assigned_user_id
 * @property string $title
 * @property string|null $description
 * @property CarbonImmutable|null $due_date
 * @property CarbonImmutable|null $completed_at
 */
#[Fillable(['case_id', 'task_type_id', 'assigned_user_id', 'title', 'description', 'due_date', 'completed_at'])]
class CaseTask extends Model
{
    use HasUuidPrimaryKey;

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'completed_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<CaseFile, $this> */
    public function case(): BelongsTo
    {
        return $this->belongsTo(CaseFile::class, 'case_id');
    }

    /** @return BelongsTo<TaskType, $this> */
    public function taskType(): BelongsTo
    {
        return $this->belongsTo(TaskType::class);
    }

    /** @return BelongsTo<User, $this> */
    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }
}
