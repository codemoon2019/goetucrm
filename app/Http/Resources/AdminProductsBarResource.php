<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AdminProductsBarResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $bar = $this->barCode($this);  
        return $bar; 
    } 

    public function barCode($bars)
    {
        $barcode = collect([]);
        foreach($bars as $key => $value)
        {
             $barcode->push(
               $key
            );
            // foreach((array)$key as $k => $v)
            // {
            //     $barcode->push(
            //         array(
            //             "name" =>$k
            //         )
            //     );

            // }
        }

        return $barcode;
    }
}
