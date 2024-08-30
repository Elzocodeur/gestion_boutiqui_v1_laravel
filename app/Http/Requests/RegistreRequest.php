<?php

namespace App\Http\Requests;

use App\Enums\RoleEnum;
use App\Enums\StateEnum;
use App\Enums\StatusResponseEnum;
use App\Enums\UserRole;
use App\Rules\CustomPasswordRule;
use App\Rules\CustumPasswordRule;
use App\Rules\PasswordRules;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegistreRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function Rules(): array
    {
        return [
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'login' => 'required|string|max:255|unique:users,login',
            'photo' => 'required|string|max:255',
            //'role' => ['required'],
            'password' =>['confirmed', new CustomPasswordRule()],
        ];
    }

    public function messages(): array
    {
        return [
            'nom.required' => 'Le nom est obligatoire.',
            'prenom.required' => 'Le prénom est obligatoire.',
            'login.required' => 'Le login est obligatoire.',
            'login.unique' => "Cet login est déjà utilisé.",
            'photo.required' => 'La photo est obligatoire.',
        ];
    }

    function validation(Validator $validator)
    {
        throw new HttpResponseException($this->sendResponse($validator->errors(),StatusResponseEnum::ECHEC,404));
    }
}