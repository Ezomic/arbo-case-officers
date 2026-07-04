<?php

namespace App\Models;

use App\Enums\CaseType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['contract_type_id', 'case_type'])]
class ContractTypeCaseType extends Model
{
    protected function casts(): array
    {
        return [
            'case_type' => CaseType::class,
        ];
    }
}
