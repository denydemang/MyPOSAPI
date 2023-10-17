<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

class UserResource extends JsonResource
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
    public function toArray($request) :array
    {


        return [
            "data" => [
                "id" => $this->resource->id,
                "name" => $this->resource->name,
                "username" => $this->resource->username,
                "role" => $this->resource->id_role,
                "branchcode" => $this->resource->branchcode,
                "token" => $this->whenNotNull($this->resource->token),
            ],
            "success" => $this->msg
        ];
    }
}
