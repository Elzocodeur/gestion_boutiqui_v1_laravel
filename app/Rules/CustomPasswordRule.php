<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class CustomPasswordRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $validator = Validator::make(
            [$attribute => $value],
            [$attribute => ['required', Password::min(8)->letters()->mixedCase()->numbers()->uncompromised()]]
        );

        if ($validator->fails()) {
            // Loop through each error message and pass it to the $fail closure
            foreach ($validator->errors()->all() as $message) {
                $fail($message);
            }
        }
    }
}
