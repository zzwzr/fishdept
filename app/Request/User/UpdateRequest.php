<?php

declare(strict_types=1);

namespace App\Request\User;

use Hyperf\Validation\Request\FormRequest;
use Hyperf\Validation\Rule;

class UpdateRequest extends FormRequest
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
            'id'        => 'required',
            'name'      => 'required|string|max:255',
            'mobile'    => 'required|digits:11',
            'password'  => 'nullable|between:6,18|confirmed', // 传 password_confirmation
            'gender'    => ['required', Rule::in(['男', '女', '未知'])]
        ];
    }
}
