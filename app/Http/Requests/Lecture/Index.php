<?php

namespace App\Http\Requests\Lecture;

use Illuminate\Foundation\Http\FormRequest;

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
            'page' => 'nullable|integer|min:1',
            'conference_id' => 'nullable|integer|exists:conferences,id',
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'integer|exists:categories,id',
            'from_date' => 'nullable|date_format:Y-m-d H:i:s',
            'to_date' => 'nullable|date_format:Y-m-d H:i:s',
            'duration' => 'nullable|integer|min:' . Lecture::MIN_DURATION . '|max:' . Lecture::MAX_DURATION,
        ];
    }

    public function all($keys = null)
    {
        $data = parent::all($keys);

        return array_merge($data, $this->route()->parameters());
    }
}
