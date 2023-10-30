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
                "name" => $this->name,
                "brands" => $this->brands,
                "price" => $this->price,
                "status" => $this->status,
                "maxstock" => $this->maxstock,
                "minstock" => $this->minstock,
                "id_category" => $this->id_category,
                "category" => $this->category,
                "unit" => $this->unit,
                "remaining_stock" =>$this->remaining_stock
            ],
            "success" => "Successfully Specific Product"
        ];
    }
}
