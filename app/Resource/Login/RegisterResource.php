<?php

namespace App\Resource\Login;

use Hyperf\Resource\Json\JsonResource;

class RegisterResource extends JsonResource
{
    use \App\Traits\ResourceTrait;

    /**
     * Transform the resource into an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return parent::toArray();
    }
}
