<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RoleDetailResource extends JsonResource
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
                "branchcode" => $this->resource['branchcode'],
                "id" => $this->resource['id'],
                "name" => $this->resource['name'],
                "access" => $this->resource['access']
            ],
            "success" => $this->msg
        ];
    }
}
