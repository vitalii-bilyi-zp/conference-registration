<?php

namespace App\Http\Requests\Lecture;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

use App\Models\User;
use App\Models\Conference;

use App\Services\LectureService;

class Store extends FormRequest
{
    protected $lectureService;

    public function __construct(LectureService $lectureService, ...$args) {
        parent::__construct(...$args);
        $this->lectureService = $lectureService;
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('lecturesStore', [User::class, $this->conference_id]);
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
            'description' => 'nullable|string|max:1000',
            'lecture_start' => 'required|date_format:Y-m-d H:i:s|before:lecture_end',
            'lecture_end' => 'required|date_format:Y-m-d H:i:s',
            'presentation' => 'nullable|file|mimes:ppt,pptx|max:10000',
            'conference_id' => 'required|integer|exists:conferences,id',
        ];
    }

    /**
     * @param \Illuminate\Validation\Validator $validator
     * @return void
     */
    public function withValidator(Validator $validator)
    {
        $validator->after(
            function ($validator) {
                $error = $this->lectureService->validateLectureTime(
                    $this->conference_id,
                    $this->lecture_start,
                    $this->lecture_end
                );

                if (!isset($error)) {
                    return;
                }

                $validator->errors()->add($error['field'], $error['message']);
            }
        );
    }
}
