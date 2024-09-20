<?php

namespace App\Actions\Fortify;

use Illuminate\Validation\Rules\Password;

trait LoginValidationRules
{
    /**
     * Get the validation rules used to validate passwords.
     *
     * @return array<int, \Illuminate\Contracts\Validation\Rule|array<mixed>|string>
     */
    protected function passwordRules(): array
    {
        return ['required', 'string', Password::default(), 'confirmed'];
    }

    /**
     * Get the validation rules that apply to the email field.
     *
     * @return array
     */
    protected function emailRules()
    {
        return ['required', 'string', 'email', 'max:255'];
    }
}
