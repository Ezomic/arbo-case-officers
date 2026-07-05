<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use RobbinThijssen\IdentitySsoKit\Concerns\HasUuidPrimaryKey;

/**
 * @property string $id
 * @property string $employer_id
 * @property string $name
 * @property string|null $email
 * @property string|null $phone
 * @property string|null $job_title
 */
#[Fillable(['employer_id', 'name', 'email', 'phone', 'job_title'])]
class ContactPerson extends Model
{
    use HasUuidPrimaryKey;

    protected $table = 'contact_persons';

    /**
     * @return BelongsTo<Employer, $this>
     */
    public function employer(): BelongsTo
    {
        return $this->belongsTo(Employer::class);
    }
}
