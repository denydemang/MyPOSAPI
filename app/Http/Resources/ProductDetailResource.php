<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    
    public function toArray($request)
    {
        return [
            "data" =>[
                "id" => $this->id,
                "branchcode" => $this->branchcode,
                "barcode" => $this->barcode,
                "product_name" => $this->product_name,
                "brands" => $this->brands,
                "price" => $this->price,
                "product_status" => $this->product_status,
                "maxstock" => $this->maxstock,
                "minstock" => $this->minstock,
                "id_category" => $this->id_category,
                "category_name" => $this->category_name,
                "id_unit" => $this->id_unit,
                "unit_name" => $this->unit_name,
            ],
            "success" => "Successfully Specific Product"
        ];
    }
}
