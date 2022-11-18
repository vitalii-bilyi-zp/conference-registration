<?php

namespace App\Http\Requests\Category;

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
        return $this->user()->can('categoriesStore', User::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'required|string|min:2|max:255',
            'category_id' => 'nullable|integer|exists:categories,id',
        ];
    }
}
