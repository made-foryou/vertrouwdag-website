<?php

namespace App\Checklist\Models;

use App\Checklist\Builders\SignupBuilder;
use Carbon\CarbonImmutable;
use Database\Factories\Checklist\SignupFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;

/**
 * A visitor who asked for the checklist. The row only lives until the address is confirmed and in the
 * newsletter list, or until it is pruned unconfirmed after thirty days.
 *
 * @property string $id
 * @property string $first_name
 * @property string $email
 * @property CarbonImmutable|null $wedding_date
 * @property string|null $source
 * @property CarbonImmutable $signed_up_at
 * @property CarbonImmutable|null $confirmed_at
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 *
 * @method static SignupBuilder query()
 */
#[Table('checklist_signups')]
#[Fillable(['first_name', 'email', 'wedding_date', 'source', 'signed_up_at', 'confirmed_at'])]
#[UseFactory(SignupFactory::class)]
final class Signup extends Model
{
    /** @use HasFactory<SignupFactory> */
    use HasFactory;

    use HasUlids;

    /**
     * @param  QueryBuilder  $query
     */
    public function newEloquentBuilder($query): SignupBuilder
    {
        return new SignupBuilder($query);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'wedding_date' => 'immutable_date',
            'signed_up_at' => 'immutable_datetime',
            'confirmed_at' => 'immutable_datetime',
        ];
    }
}
