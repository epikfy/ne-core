<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
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
            'id' => $this->id,
            'user_id' => $this->user_id,
            'user_name' => $this->user_name,
            'client_id' => $this->client_id,
            'client_name' => ($this->client) ? $this->client->first_name . ' ' . $this->client->last_name : '',
            'product_id' => $this->product_id,
            'product_name' => ($this->product) ? $this->product->description : '',
            'internal_nr' => $this->internal_nr,
            'invoice_nr' => $this->invoice_nr,
            'date' => $this->date,
            'amount' => $this->amount,
            'discount' => $this->discount,
            'tax' => $this->tax,
            'quantity' => $this->quantity,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
