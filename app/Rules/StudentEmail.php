<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class StudentEmail implements Rule
{
    public function passes($attribute, $value)
    {
        return str_ends_with($value, '@students.apiit.lk');
    }

    public function message()
    {
        return 'Please Use Your APIIT Student Email Address';
    }
}
