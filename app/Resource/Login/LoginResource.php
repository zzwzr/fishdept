<?php

namespace App\Resource\Login;

use Hyperf\Resource\Json\JsonResource;

class LoginResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'mobile'        => $this->mobile,
            'avatar'        => $this->avatar,
            'gender'        => $this->gender,
            'token'         => $this->token,
            'token_type'    => 'Bearer '
        ];
    }
}
