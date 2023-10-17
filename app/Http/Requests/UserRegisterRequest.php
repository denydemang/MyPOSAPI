<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator as ContractsValidationValidator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Validator as ValidationValidator;

class UserRegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() : bool
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
            "branchcode" => 'required',
            "username" => 'required|max:255',
            "name" => 'required|max:255',
            "password" => 'required',
            "id_role" => 'required',
        ];
    }

    protected function failedValidation(ContractsValidationValidator $validator){
        throw new HttpResponseException(response(
            ["errors" => $validator->getMessageBag()],
            
            400));
    }
}
