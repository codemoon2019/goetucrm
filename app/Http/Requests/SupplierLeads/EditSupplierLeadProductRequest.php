<?php

namespace App\Http\Requests\SupplierLeads;

use Illuminate\Foundation\Http\FormRequest;

class EditSupplierLeadProductRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $this->sanitize();

        return [
            'products' => 'nullable|array',
            'product_names.*' => 'required|string',
            'product_descriptions.*' => 'required|string|max:250',
            'product_prices.*' => 'required|numeric',
        ];
    }

    public function sanitize()
    {
        $data = $this->all();

        $products = [];
        $length = count($data['product_names'] ?? []);
        for ($i = 0; $i < $length; $i++) {
            $products[] = [
                'id' => $data['product_ids'][$i] ?? null,
                'name' => $data['product_names'][$i],
                'description' => $data['product_descriptions'][$i],
                'price' => $data['product_prices'][$i]
            ];
        }

        $data['products'] = $products;
        
        unset($data['product_names']);
        unset($data['product_prices']);
        unset($data['product_descriptions']);

        $this->replace($data);
    }
}
