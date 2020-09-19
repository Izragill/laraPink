<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return \Auth::user()->canDo('EDIT_USERS');
    }

    protected function getValidatorInstance()
    {
        $validator = parent::getValidatorInstance();

        $validator->sometimes('password', 'required|min:6|confirmed', function($input)
        {
            if(!empty($input->password) || ((empty($input->password) && $this->route()->getName() !== 'admin.users.update'))) {
                return TRUE;
            }

            return FALSE;
        });

        return $validator;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|max:255',
            'login' => 'required|max:255',
            'role_id' => 'required|integer',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users','email')->ignore($this->route('user'))
            ]
        ];
    }
}
