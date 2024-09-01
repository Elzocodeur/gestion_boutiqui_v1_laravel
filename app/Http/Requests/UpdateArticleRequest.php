<?php

namespace App\Http\Requests;

use App\Enums\StatusResponseEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Enums\StateEnum;

class UpdateArticleRequest extends FormRequest
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
    public function rules(): array
    {
        return [
            'libelle' => 'nullable|string|max:255',
            'prix' => 'nullable|numeric',
            'quantite' => 'required|numeric',
        ];
    }

    /**
     * Get the validation messages that apply to the rules.
     */
    public function messages(): array
    {
        return [
            'libelle.required' => 'Le libelle est obligatoire.',
            'libelle.string' => 'Le libelle doit être une chaîne de caractères.',
            'prix.required' => 'Le prix est obligatoire.',
            'prix.numeric' => 'Le prix doit être un nombre.',
            'quantite.required' => 'La quantité en stock est obligatoire.',
            'quantite.numeric' => 'La quantité en stock doit être un nombre.',
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'status' => StatusResponseEnum::ECHEC,
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}
