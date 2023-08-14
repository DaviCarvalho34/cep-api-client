<?php

namespace App\Http\Resources\V1\Address;

use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
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
            "id" => $this->id,
            "cep" =>$this->cep,
            "road" => $this->road,
            "neighborhood" => $this->neighborhood,
            "city" => $this->city,
            "uf" => $this->uf
        ];
    }
}
