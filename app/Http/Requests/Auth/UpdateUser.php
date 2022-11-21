<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

use App\Models\User;

use App\Services\UserService;

class UpdateUser extends FormRequest
{
    protected $userService;

    public function __construct(UserService $userService, ...$args) {
        parent::__construct(...$args);
        $this->userService = $userService;
    }
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
            'firstname' => 'nullable|string|max:255',
            'lastname' => 'nullable|string|max:255',
            'birthdate' => 'nullable|date_format:Y-m-d',
            'country_id' => 'nullable|integer|exists:countries,id',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|string|email|max:255',
            'old_password' => 'required_with:new_password|string|min:8|max:255',
            'new_password' => 'required_with:old_password|string|min:8|max:255',
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

                if (isset($this->email)) {
                    $error = $this->userService->validateNewEmail(
                        $this->user(),
                        $this->email
                    );
                }

                if (!isset($error) && isset($this->old_password) && isset($this->new_password)) {
                    $error = $this->userService->validateNewPassword(
                        $this->user(),
                        $this->old_password,
                        $this->new_password
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
