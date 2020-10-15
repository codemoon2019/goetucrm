<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AdminProductsInvoiceResource extends JsonResource
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
            'id' => $this->product_id != null ? $this->product_id : null,
            'name' => $this->product != null ? $this->product->name : null,
            'sale' => $this->total,
        ];
    }

    public function sortArray($details)
    {
        $products = array();
        foreach($details as $detail){
                $products->push($detail);
        }

        return $products;
    }
}
