<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class GRNUpdateRequest extends FormRequest
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
            "received_date" => "required",
            "id_purchase" => "required",
            "received_by" => "nullable",
            "grand_total" => "required|numeric",
            "description" => "nullable",  
            "items.*.id_product" => "required" ,
            "items.*.id_unit" => "required" ,
            "items.*.qty" => "required|numeric" ,
            "items.*.price" => "required|numeric" ,
            "items.*.sub_total" => "required|numeric" ,
        ];
    }
    protected function failedValidation(Validator $validator){
        throw new HttpResponseException(response(["errors" => $validator->getMessageBag()],400));
    }
}
