<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class PurchaseCreateRequest extends FormRequest
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
            "branchcode" => "required",
            "trans_date" => "required",
            "id_user" => "required",
            "id_supplier" => "required",
            "total" => "required|numeric",
            "other_fee" => "nullable|numeric",
            "payment_term" => "nullable",
            "ppn" => "nullable|numeric",
            "grand_total" => "required|numeric",
            "is_credit" => "required",
            "items.*.id_product" => "required" ,
            "items.*.id_unit" => "required" ,
            "items.*.qty" => "required|numeric" ,
            "items.*.price" => "required|numeric" ,
            "items.*.discount" => "nullable|numeric" ,
            "items.*.sub_total" => "required|numeric" ,
        ];
    }
    protected function failedValidation(Validator $validator){
        throw new HttpResponseException(response(["errors" => $validator->getMessageBag()],400));
    }
}
