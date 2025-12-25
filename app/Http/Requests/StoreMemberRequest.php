<?php

namespace App\Http\Requests;

use App\Rules\PhoneNumberRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreMemberRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:members,email'],
            'phone' => ['required', 'string', new PhoneNumberRule()],
            'address' => ['required', 'string', 'max:1000'],
        ];
    }

    /**
     * Get the validated data from the request.
     * Decode phone from JSON string to array.
     */
    public function validated($key = null, $default = null): mixed
    {
        $validated = parent::validated($key, $default);

        // If getting all validated data (no specific key)
        if ($key === null && isset($validated['phone']) && is_string($validated['phone'])) {
            $validated['phone'] = json_decode($validated['phone'], true);
        }

        return $validated;
    }
}
