<?php

namespace App\Models;

use App\Enums\CaseType;
use App\Enums\ReintegrationMilestone;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $type
 * @property CaseType|null $case_type
 * @property ReintegrationMilestone|null $milestone
 */
#[Fillable(['task_type_id', 'type', 'case_type', 'milestone'])]
class TaskTypeCondition extends Model
{
    protected function casts(): array
    {
        return [
            'case_type' => CaseType::class,
            'milestone' => ReintegrationMilestone::class,
        ];
    }
}
