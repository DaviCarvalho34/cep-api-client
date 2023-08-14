<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAddressRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            "cep" => ['required', 'min:8', 'max:9'],
            "road" => ['nullable', 'min:3', 'max:50'],
            "neighborhood" => ['nullable', 'min:3', 'max:50'],
            "city" => ['required', 'min:3', 'max:50'],
            "uf" => ['required', 'min:2', 'max:2']
        ];
    }
}
