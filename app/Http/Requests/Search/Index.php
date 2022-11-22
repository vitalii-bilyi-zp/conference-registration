<?php

namespace App\Http\Requests\Search;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use App\Models\Conference;
use App\Models\Lecture;

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
            'search_term' => 'required|string|min:2|max:255',
            'type' => [
                'nullable',
                'string',
                Rule::in([Conference::TYPE, Lecture::TYPE]),
            ],
        ];
    }

    public function all($keys = null)
    {
        $data = parent::all($keys);

        return array_merge($data, $this->route()->parameters());
    }
}
