<?php

declare(strict_types=1);

namespace App\Request\Room;

use Hyperf\Validation\Request\FormRequest;

class InfoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name'          => 'required',
            'browser_id'    => 'required'
        ];
    }
}
