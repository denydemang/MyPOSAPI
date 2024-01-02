<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CompanyProfileCreateRequest extends FormRequest
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
            'branchcode' => 'required',
            'profile_name' => 'required|max:30',
            'app_name' => 'required|max:10',
            'address' => 'required|max:100',
            'phone' => 'required|numeric|digits_between:5,13',
            'email' => 'required|email',
            'npwp' => 'max: 100|nullable',
            'moto' => 'max: 300|nullable',
        ];
    }
    protected function failedValidation(Validator $validator){
        throw new HttpResponseException(response(["errors" => $validator->getMessageBag()],400));
    }
}
