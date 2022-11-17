<?php

namespace App\Http\Requests\Comment;

use Illuminate\Foundation\Http\FormRequest;

class Index extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'page' => 'nullable|integer|min:1',
            'lecture_id' => 'nullable|integer|exists:lectures,id',
        ];
    }

    public function all($keys = null)
    {
        $data = parent::all($keys);

        return array_merge($data, $this->route()->parameters());
    }
}
