<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Enums\StatusResponseEnum;
use App\Rules\CustomPasswordRule;
use App\Traits\RestResponseTrait;

class StoreUserRequest extends FormRequest
{
    use RestResponseTrait;

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
    public function rules(): array
    {
        return [
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'login' => 'required|string|max:255|unique:users,login',
            // 'photo' => 'required|string|max:255',
            'password' => 'required|string|min:4',
            'photo' => 'nullable|image|max:2048',
            'role_id' => 'required|integer',
            // 'password' => ['required', 'string', 'confirmed', new CustomPasswordRule()],
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'nom.required' => 'Le nom est obligatoire.',
            'prenom.required' => 'Le prénom est obligatoire.',
            'role_id.required' => 'Le rôle est obligatoire.',
            'login.required' => 'Le login est obligatoire.',
            'login.unique' => "Ce login est déjà utilisé.",
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
        ];
    }
    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator)
    {
        $response = $this->sendResponse($validator->errors(), StatusResponseEnum::ECHEC, 'Validation échouée', 422);
        throw new HttpResponseException($response);
    }
}
