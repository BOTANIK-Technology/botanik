<?php

namespace App\Http\Requests\v1;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
        return [
            'name'       => 'required|string|max:255|min:8',
            'email'      => 'required|string|max:255|min:10|email:rfc,dns',
            'phone'      => 'required|string|max:20|min:10',
            'role'       => 'required',
            'addresses'  => 'required|json',
            'services'   => 'required|json',
            'timetables' => 'required|json',
            'password'   => 'nullable|string|max:30',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages(): array
    {
        $required = __('обязательное поле.');
        $max = __('максимальное кол-во символов (255) превышено.');
        $min = __('минимально допустимое кол-во символов');
        return [
            'name.required' => 'ФИО '.$required,
            'email.required' => 'Email '.$required,
            'phone.required' => 'Телефон '.$required,
            'phone.max' => __('Максимально допустимое значение поля "Телефон" - 20 символов.'),
            'email.email:rfc,dns' => __('Неверный формат email адреса.'),
            'name.max' => 'ФИО - '.$max,
            'name.min' => 'ФИО - '.$min.' 8.',
            'email.min' => 'Email - '.$min.' 10.',
        ];
    }
}
