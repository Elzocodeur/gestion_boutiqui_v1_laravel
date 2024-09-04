<?php

namespace App\Http\Requests;

use App\Rules\TelephoneRule;
use App\Traits\RestResponseTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Enums\StatusResponseEnum;
use App\Rules\CustomPasswordRule;

class StoreClientRequest extends FormRequest
{
    use RestResponseTrait;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'surname' => ['required', 'string', 'max:255','unique:clients,surname'],
            'address' => ['string', 'max:255'],
            'telephone' => ['required', new TelephoneRule()],
            'user' => ['sometimes', 'array'],
            'user.nom' => ['required_with:user', 'string'],
            'user.prenom' => ['required_with:user', 'string'],
            'user.login' => ['required_with:user', 'string'],
            'user.photo' => ['required_with:user', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            // 'user.password' => ['required_with:user', new CustomPasswordRule(), 'confirmed'],
        ];
    }

    public function messages()
    {
        return [
            'surname.required' => "Le surnom est obligatoire.",
            'user.photo.image' => "Le fichier doit être une image.",
            'user.photo.mimes' => "Le fichier doit être de type : jpg, jpeg, png.",
            'user.password.required_with' => "Le mot de passe est obligatoire.",
            'user.password.confirmed' => "La confirmation du mot de passe ne correspond pas.",
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException($this->sendResponse($validator->errors(), StatusResponseEnum::ECHEC, 404));
    }
}
