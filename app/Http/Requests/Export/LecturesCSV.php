<?php

namespace App\Http\Requests\Export;

use Illuminate\Foundation\Http\FormRequest;

class LecturesCSV extends FormRequest
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
            'conference_id' => 'nullable|integer|exists:conferences,id',
        ];
    }

    public function all($keys = null)
    {
        $data = parent::all($keys);

        return array_merge($data, $this->route()->parameters());
    }
}
