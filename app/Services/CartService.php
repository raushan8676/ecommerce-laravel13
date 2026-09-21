<?php

namespace App\Services;

use Illuminate\Http\Resources\Attributes\Collects;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Collection;

class CartService
{
    protected string $instance = 'cart';
    protected ?string $lastRowId = null;
    protected float $defaultTax = 18;

    public function instance(string $name): self
    {
        $this->instance = $name;
        return $this;
    }

    public function content(): Collection
    {
        return $this->getCart();
    }

    public function taxRate(): float
    {
        return (float) $this->defaultTax;
    }

    public function setTaxRate(float $taxRate): self
    {
        $this->defaultTax = $taxRate;
        return $this;
    }

    protected function getCart(): Collection
    {
        $cart = Session::get("cart.{$this->instance}", collect());
        $collection = $cart instanceof Collection ? $cart : collect($cart);

        return $collection->map(function ($item){
            $obj = is_array($item) ? (object)$item : $item;
            if (isset($obj->row_id) && !isset($obj->rowId)) {
                $obj->rowId = $obj->row_id;
            }
            if (isset($obj->rowId) && !isset($obj->row_id)) {
                $obj->row_id = $obj->rowId;
            }
            return $obj;
        });
    }
    
    public function add(mixed $id, ?string $name=null, ?int $qty = null, ?float $price = null , array $options =[], ?int $tax = null):self
    {
        if(is_array($id)){
            return $this->addArray($id);
        }

        $qty = ($qty !== null && $qty > 0) ? $qty : 1;
        $price = (float)($price !== null ? $price : 0);

        $cart = $this->getCart();
        $rowId = md5((string)$id. serialize($options));

        if ($cart->has($rowId)) {
            $cartItem = $cart->get($rowId);
            $cartItem->qty += $qty;
            $cartItem->subtotal = $cartItem->qty * $cartItem->price;
        } else {
            $cartItem = (object)[
                'rowId'=> $rowId,
                'id'=> $id,
                'name'=> $name,
                'qty'=> $qty,
                'price'=> $price,
                'options'=> $options,
                'tax' => ($tax !== null && $tax > 0) ? $tax : $this->defaultTax,
                'subtotal' => $qty * $price,
                'associatedModel' => null
            ];
        }
        
        $cart->put($rowId, $cartItem);
        $this->lastRowId = $rowId;
        
        Session::put("cart.{$this->instance}", $cart);
        return $this;
    }

    public function addArray(array $data): self
    {
        return $this ->add(
            $data['id'],
            $data['name'] ?? null,
            (int) ($data['qty'] ?? 1),
            (float) ($data['price'] ?? 0),
            ($data['options'] ?? []),
            (int) ($data['tax'] ?? 0)
        );
    }

    public function associate(string $model): self
    {
       if (!$this->lastRowId) {
            return $this;
       }

       $cart = $this->getCart();
       $item = $cart->get($this->lastRowId);
       if ($item) {
           $item->associatedModel = $model;
           $cart->put($this->lastRowId, $item);
           Session::put("cart.{$this->instance}", $cart);
       }
       return $this;
    }

    public function get(string $rowId): ?object
    {
        return $this->getCart()->get($rowId);
    }

    public function model(string $rowId): ?object
    {
        $item = $this->getCart()->get($rowId);

        if($item && $item->associatedModel){
            return $item->associatedModel::find($item->id);
        }

        return null;
    }

    public function update(string $rowId, mixed $data): object|bool
    {
        $cart = $this->getCart();
        if(!$cart->has($rowId))
        {
            return false;
        }

        $item = $cart->get($rowId);

        if(is_numeric($data)){
            $item->qty = (int) $data;
        }
        else {
            foreach ($data as $key => $value) {
               $item->{$key} = $value;
            }
        }

        $item->subtotal = $item->qty * $item->price;

        Session::put("cart.{$this->instance}", $cart);

        return $item;
    }

    public function subTotal(): float
    {
        return (float) $this->getCart()->sum('subtotal');
    }

    public function tax(): float
    {
        return $this->taxTotal();
    }

    public function taxTotal(): float
    {
        return (float) $this->getCart()->sum(function($item){
            $taxRate = (isset($item->tax) && $item->tax > 0) ? (float)$item->tax : (float)$this->defaultTax;
            return (float)$item->qty * (float)$item->price * ($taxRate / 100);
        });
    }
    public function total(): float
    {
        return $this->subTotal() + $this->taxTotal();
    } 

    public function remove(string $rowId): self
    {
        $cart = $this->getCart();
        $cart->forget($rowId);
        Session::put("cart.{$this->instance}", $cart);
        
        return $this;
    }
    
    public function destroy(): void
    {
        Session::forget("cart.{$this->instance}");
    }
    
    public function count(): int
    {
        return (int) $this->getCart()->sum('qty');
    } 
}
