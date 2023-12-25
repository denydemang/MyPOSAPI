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

    public $msg;
    public function __construct($resource, $msg)
    {
        parent::__construct($resource);
        $this->msg =$msg;
    }
    public function toArray($request)
    {
        return [
            "data" =>  [
                "branchcode" =>$this->resource->branchcode,
                "id" =>$this->resource->id,
                "number_id" =>$this->resource->number_id,
                "name" =>$this->resource->name,
                "address" =>$this->resource->address,
                "contact" =>$this->resource->contact,
                "active" =>$this->resource->active,
            
            ],
            "success" => $this->msg
        ];
    }
}
