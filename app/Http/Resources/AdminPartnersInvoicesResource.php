<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AdminPartnersInvoicesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'name' => $this->description,
            'sale' => $this->invoiceDetails->sum('amount')
            //{name: 'Page A', uv: 4000, pv: 2400, amt: 2400},
        ];
    }
}
