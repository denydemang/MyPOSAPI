<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CompanyProfileDetailResource extends JsonResource
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
            "data" => [
                "branchcode" =>$this->branchcode,
                "profile_name" =>$this->profile_name,
                "address" =>$this->address,
                "phone" =>$this->phone,
                "email" =>$this->email,
                "npwp" =>$this->npwp,
                "moto" =>$this->moto,
            ],
            "success" => "successfully get specific comp.profile"
            ];
    }
}
