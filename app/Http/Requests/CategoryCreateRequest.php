<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CategoryCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
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
            "name" => 'required',
            "status" => 'required|numeric',
        ];
    }
    protected function failedValidation(Validator $validator){
        throw new HttpResponseException(response(["errors" => $validator->getMessageBag()],400));
    }
}