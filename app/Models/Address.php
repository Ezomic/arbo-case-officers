<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;
use RobbinThijssen\IdentitySsoKit\Concerns\HasUuidPrimaryKey;

/**
 * No tenant scope: this model has no tenant_id column. Every access path in
 * this app goes through an already tenant-checked owner (e.g. Employee), so
 * never query Address::query() directly without an explicit tenant check.
 *
 * @property string $id
 * @property string $addressable_type
 * @property string $addressable_id
 * @property string $address_line_1
 * @property string|null $address_line_2
 * @property string $postal_code
 * @property string $city
 * @property string $country
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['addressable_type', 'addressable_id', 'address_line_1', 'address_line_2', 'postal_code', 'city', 'country'])]
class Address extends Model
{
    use HasUuidPrimaryKey;

    /**
     * @return MorphTo<Model, $this>
     */
    public function addressable(): MorphTo
    {
        return $this->morphTo();
    }
}
