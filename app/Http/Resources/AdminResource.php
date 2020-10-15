<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AdminResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        $partner = $this->checkNull($this);
       

        return [ 
            'id' => $partner['id'],
            'fullName' => $partner['fullName'] ,
            'totalSale' => $partner['totalSale']
        ];
    }

    public function checkNull($data)
    {
        if($data->connectedUser){
            $partner = array(
                'id' => $data->connectedUser->id,
                'fullName' => $data->connectedUser->first_name .' '.$data->connectedUser->last_name, 
                'totalSale' =>$data->invoiceHeaders->sum('total_due')
            );
        }
        else
        {
            $partner = array(
                'id' => '',
                'fullName' => '',
                'totalSale' => ''
            );
        }

        return $partner;
    }
}
