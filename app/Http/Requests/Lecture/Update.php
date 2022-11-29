<?php

namespace App\Http\Requests\Lecture;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

use App\Models\User;

use App\Services\LectureService;

class Update extends FormRequest
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
        return $this->lecture && $this->user()->can('lecturesUpdate', [User::class, $this->lecture]);
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
            'description' => 'nullable|string|max:1000',
            'lecture_start' => 'required_with:lecture_end|date_format:Y-m-d H:i:s|after_or_equal:now',
            'lecture_end' => 'required_with:lecture_start|date_format:Y-m-d H:i:s|after:lecture_start',
            'presentation' => 'nullable|file|mimes:ppt,pptx|max:10000',
            'category_id' => 'nullable|integer|exists:categories,id',
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
                $error = null;

                if (isset($this->lecture_start) && isset($this->lecture_end)) {
                    $error = $this->lectureService->validateLectureTime(
                        $this->lecture->conference_id,
                        $this->lecture_start,
                        $this->lecture_end
                    );
                }

                if (!isset($error) && isset($this->category_id)) {
                    $error = $this->lectureService->validateCategoryId(
                        $this->lecture->conference_id,
                        $this->category_id
                    );
                }

                if (!isset($error)) {
                    return;
                }

                $validator->errors()->add($error['field'], $error['message']);
            }
        );
    }
}
