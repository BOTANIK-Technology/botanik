<?php

namespace App\Http\Requests\v1;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string $slug
 * @property string $method
 * @property array $params
 *
 * Class PartnerApiRequest
 * @package App\Http\Requests\v1
 */
class PartnerApiRequest extends FormRequest
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
            case 'PUT':
            case 'PATCH':
            case 'POST':
                return [
                    'params' => 'nullable|array'
                ];
        }

        return [
            //
        ];
    }
}
