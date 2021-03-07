<?php

namespace App\Http\Requests\v1\BeautyPro;

use Illuminate\Foundation\Http\FormRequest;

class ClientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        switch ($this->method()) {
            case 'GET':

                break;
            case 'POST':
                return [
                    'image' => 'required|image',
                    'path'  => 'nullable|string'
                ];
        }

        return [
            //
        ];
    }
}
