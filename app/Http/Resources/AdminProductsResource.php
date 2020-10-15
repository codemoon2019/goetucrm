<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AdminProductsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
       // $products::hydrate($this->products);
    //    foreach($this->products as $key => $value) {
    //     $t->$key = $value;
    //     }
       
        return $this->products;
        // return [
        //     'id' => $this->products->groupBy('id')
        // ];

        // return [
        //     'id' => $this->id,
        //     'productName' => $this->name
        // ];
    }
}
