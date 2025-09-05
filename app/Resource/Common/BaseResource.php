<?php

namespace App\Resource\Common;

use Hyperf\Resource\Json\JsonResource;
use App\Traits\ResourceTrait;

class BaseResource extends JsonResource
{
    use ResourceTrait;

    protected $code;
    protected $message;

    public function __construct($resource = [], string $message = 'success', int $code = 0)
    {
        parent::__construct($resource);
        $this->code = $code;
        $this->message = $message;
    }

    /**
     * Transform the resource into an array.
     * @return array
     */
    public function toArray(): array
    {
        return [
            'data' => $this->resource
        ];
    }
}
