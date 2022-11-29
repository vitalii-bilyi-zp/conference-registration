<?php

namespace App\Http\Requests\Conference;

use Illuminate\Foundation\Http\FormRequest;

use App\Models\User;

class Store extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('conferencesStore', User::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'title' => 'required|string|min:2|max:255',
            'date' => 'required|date_format:Y-m-d|after_or_equal:today',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'country_id' => 'required|integer|exists:countries,id',
            'category_id' => 'nullable|integer|exists:categories,id',
        ];
    }
}
