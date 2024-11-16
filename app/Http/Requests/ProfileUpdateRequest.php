<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        // Get the valid options for school_of_study and year_sem
        $validOptions = (new \App\Http\Controllers\ProfileController)->validOptions();
        
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            // Validation for school_of_study field
            'school_of_study' => [
                'nullable',
                'string',
                'in:' . implode(',', array_keys($validOptions)),
            ],
            // Validation for year_sem field, based on school_of_study
            'year_sem' => [
                'nullable',
                'string',
                'in:' . implode(',', $validOptions[$this->input('school_of_study')] ?? []),
            ],
            // Validation for profile_pic field
            'profile_pic' => 'nullable|image|max:5120', // Max size of 5MB
            // Validation for available_times field (must be an array of times)
            'available_times' => 'nullable|array',
        ];
    }
}
