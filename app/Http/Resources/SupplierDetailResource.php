<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SupplierDetailResource extends JsonResource
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
            "data" =>  [
                "branchcode" =>$this->branchcode,
                "id" =>$this->id,
                "name" =>$this->name,
                "address" =>$this->address,
                "contact" =>$this->contact,
                "active" =>$this->active,
            
            ],
            "success" => "Succedfully Get Sepicific Supplier"
        ];
    }
}
