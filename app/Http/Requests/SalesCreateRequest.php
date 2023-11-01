<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class SalesCreateRequest extends FormRequest
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
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            "branchcode" => "required",
            "trans_date" => "required",
            "id_cust" => "required",
            "id_user" => "required",
            "total" => "required|numeric",
            "discount" => "nullable|numeric",
            "ppn" => "nullable|numeric",
            "notes" => "nullable",
            "grand_total" => "required|numeric",   
            "paid" => "required|numeric",
            "change_amount" => "required|numeric",
            "is_credit" => "required",
            "items.*.id_product" => "required" ,
            "items.*.id_unit" => "required" ,
            "items.*.qty" => "required|numeric" ,
            "items.*.price" => "required|numeric" ,
            "items.*.discount" => "required|numeric" ,
            "items.*.sub_total" => "required|numeric" ,
        ];
    }
    protected function failedValidation(Validator $validator){
        throw new HttpResponseException(response(["errors" => $validator->getMessageBag()],400));
    }
}
