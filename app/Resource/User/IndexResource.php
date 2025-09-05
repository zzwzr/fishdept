<?php

declare(strict_types=1);

namespace App\Resource\User;

use Hyperf\Resource\Json\JsonResource;

class IndexResource extends JsonResource
{
    use \App\Traits\ResourceTrait;

    /**
     * Transform the resource into an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id'        => $this->id,
            'name'      => $this->name,
            'mobile'    => $this->mobile,
            'gender'    => $this->gender
        ];
    }
}
