<?php

namespace App\Checklist\Builders;

use App\Checklist\Models\Signup;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;

/**
 * @extends Builder<Signup>
 */
final class SignupBuilder extends Builder
{
    public function confirmed(): self
    {
        return $this->whereNotNull('confirmed_at');
    }

    public function unconfirmed(): self
    {
        return $this->whereNull('confirmed_at');
    }

    public function signedUpBefore(CarbonInterface $moment): self
    {
        return $this->where('signed_up_at', '<', $moment);
    }
}
