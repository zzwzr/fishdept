<?php

declare(strict_types=1);

namespace App\Traits;

trait ResourceTrait
{
    public function with(): array
    {
        return [
            'code'      => $this->code,
            'message'   => $this->message
        ];
    }
}
