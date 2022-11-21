<?php

namespace App\Http\Requests\Conference;

use Illuminate\Foundation\Http\FormRequest;

use App\Models\User;

class CancelParticipation extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->conference && $this->user()->can('conferencesCancelParticipation', [User::class, $this->conference]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            //
        ];
    }
}
