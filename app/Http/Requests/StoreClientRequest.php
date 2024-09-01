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
        $rules = [
            'surname' => ['required', 'string', 'max:255','unique:clients,surname'],
            'address' => ['string', 'max:255'],
            'telephone' => ['required',new TelephoneRule()],

            'user' => ['sometimes','array'],
            'user.nom' => ['required_with:user','string'],
            'user.prenom' => ['required_with:user','string'],
            'user.login' => ['required_with:user','string'],
            // 'user.role' => ['required_with:user', 'in:' . implode(',', array_column(RoleEnum::cases(), 'value'))],
            'user.password' => ['required_with:user', new CustomPasswordRule(),'confirmed'],

        ];

        return $rules;
    }

    function messages()
    {
        return [
            'surname.required' => "Le surnom est obligatoire.",
        ];
    }

    function failedValidation(Validator $validator)
    {
        throw new HttpResponseException($this->sendResponse($validator->errors(),StatusResponseEnum::ECHEC,404));
    }
}
