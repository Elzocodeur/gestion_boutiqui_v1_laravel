<?php

namespace App\Http\Requests;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

use Illuminate\Foundation\Http\FormRequest;

class StoreDetteRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'montant' => 'required|numeric|min:0',
            'clientId' => 'required|exists:clients,id',
            'articles' => 'required|array|min:1',
            'articles.*.articleId' => 'required|exists:articles,id',
            'articles.*.qteVente' => 'required|integer|min:1',
            'articles.*.prixVente' => 'required|numeric|min:0',
            'paiement.montant' => 'nullable|numeric|max:' . $this->input('montant'),
        ];
    }

    public function messages(): array
    {
        return [
            'montant.required' => 'Le montant est obligatoire.',
            'montant.numeric' => 'Le montant doit être un nombre.',
            'montant.min' => 'Le montant doit être positif.',
            'clientId.required' => 'Le client est obligatoire.',
            'clientId.exists' => 'Le client n\'existe pas.',
            'articles.required' => 'Les articles sont obligatoires.',
            'articles.*.articleId.required' => 'L\'article est obligatoire.',
            'articles.*.articleId.exists' => 'L\'article n\'existe pas.',
            'articles.*.qteVente.required' => 'La quantité est obligatoire.',
            'articles.*.qteVente.integer' => 'La quantité doit être un nombre entier.',
            'articles.*.qteVente.min' => 'La quantité doit être positive.',
            'articles.*.prixVente.required' => 'Le prix est obligatoire.',
            'articles.*.prixVente.numeric' => 'Le prix doit être un nombre.',
            'articles.*.prixVente.min' => 'Le prix doit être positif.',
            'paiement.montant.numeric' => 'Le montant du paiement doit être un nombre.',
            'paiement.montant.max' => 'Le montant du paiement ne doit pas depasser le montant de la dette.',
        ];
    }
    
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => 'echec',
            'data' => $validator->errors(),
            'message' => 'Validation échouée'
        ], 422));
    }
}
