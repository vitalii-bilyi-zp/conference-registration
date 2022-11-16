<?php

namespace App\Http\Requests\Conference;

use Illuminate\Foundation\Http\FormRequest;

use App\Models\User;

class Update extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('update', User::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'title' => 'nullable|string|min:2|max:255',
            'date' => 'nullable|date_format:Y-m-d',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'country_id' => 'nullable|integer|exists:countries,id',
        ];
    }
}
