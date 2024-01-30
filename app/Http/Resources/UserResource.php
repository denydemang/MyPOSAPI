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
    protected $msg;
    protected $token;
    public function __construct($resource, $msg , $token = null)
    {
        parent::__construct($resource);
        $this->msg =$msg;
        $this->token =$token;
    }
    public function toArray($request) :array
    {


        return [
            "data" => [
                "id" => $this->resource->id,
                "name" => $this->resource->name,
                "username" => $this->resource->username,
                "role" => $this->resource->id_role,
                "role_name" => $this->resource->role,
                "branchcode" => $this->resource->branchcode,
                "token" =>$this->whenNotNull($this->token)
            ],
            "success" => $this->msg
        ];
    }
}
