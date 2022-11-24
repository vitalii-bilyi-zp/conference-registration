<?php

namespace App\Http\Requests\Export;

use Illuminate\Foundation\Http\FormRequest;

class CommentsCSV extends FormRequest
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
            'lecture_id' => 'nullable|integer|exists:lectures,id',
        ];
    }

    public function all($keys = null)
    {
        $data = parent::all($keys);

        return array_merge($data, $this->route()->parameters());
    }
}
