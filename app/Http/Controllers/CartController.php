<?php

namespace App\Http\Controllers;

use App\Facades\Cart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(){
        return view ('cart.index');
    }

    public function add_to_cart(Request $request){
        $request->validate([
            'id' => 'required|integer',
            'name' => 'required|string',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
        ]);

        Cart::instance('cart')->add($request->id, $request->name, $request->quantity, $request->price)->associate("App\Models\Product");
        
        // Remove from wishlist automatically if present
        $wishlistItem = Cart::instance('wishlist')->content()->firstWhere('id', $request->id);
        if ($wishlistItem) {
            Cart::instance('wishlist')->remove($wishlistItem->rowId);
        }

        return back()->with('success', 'Item added to cart successfully');
    }

    public function update_cart(Request $request)
    {
        $request->validate([
            'quantity' => 'required|integer|min:-1',
        ]);

        Cart::instance('cart')->update($request->rowId, $request->quantity);

        return back()->with('success', 'Cart Updated Successfully');
    }

   public function remove_from_cart(String $rowId)
   {
        Cart::instance('cart')->remove($rowId);
        return back()->with('success', 'Item removed from cart successfully');
   }

   public function clear_cart()
   {
       Cart::instance('cart')->destroy();
       return back()->with('success', 'Cart cleared successfully');
   }
}
