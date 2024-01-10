<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomerDetailResource extends JsonResource
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
            "data" =>[
                "id" => $this->resource->id,
                "branchcode" => $this->resource->branchcode,
                "cust_no" => $this->resource->cust_no,
                "name" => $this->resource->name,
                "address" => $this->resource->address,
                "contact" => $this->resource->phone,
                "active" => $this->resource->active,
            ],
            "success" => $this->msg
        ];
    }
}
