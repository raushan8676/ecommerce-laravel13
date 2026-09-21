<?php

namespace App\Http\Controllers;

use App\Facades\Cart;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index()
    {
        return view('wishlist.index');
    }

    public function add_to_wishlist(Request $request)
    {
        $request->validate([
            'product_id' => 'nullable|integer',
            'id' => 'nullable|integer',
            'name' => 'required|string',
            'quantity' => 'nullable|integer|min:1',
            'price' => 'required|numeric|min:0',
        ]);

        $productId = $request->input('product_id', $request->input('id'));

        if (!$productId) {
            return back()->with('error', 'Invalid product.');
        }

        $quantity = $request->input('quantity', 1);

        Cart::instance('wishlist')->add($productId, $request->name, (int) $quantity, (float) $request->price)->associate('App\Models\Product');

        return back()->with('success', 'Product added to wishlist successfully!');
    }

    public function move_to_cart(string $rowId)
    {
        $item = Cart::instance('wishlist')->get($rowId);
        if ($item) {
            Cart::instance('cart')->add($item->id, $item->name, (int) ($item->qty ?: 1), (float) $item->price)->associate('App\Models\Product');
            Cart::instance('wishlist')->remove($rowId);
            return redirect()->route('cart.index')->with('success', 'Item moved to cart successfully!');
        }
        return redirect()->route('wishlist.index')->with('error', 'Item not found in wishlist.');
    }

    public function remove_from_wishlist(string $rowId)
    {
        Cart::instance('wishlist')->remove($rowId);
        return back()->with('success', 'Item removed from wishlist successfully!');
    }

    public function clear_wishlist()
    {
        Cart::instance('wishlist')->destroy();
        return back()->with('success', 'Wishlist cleared successfully!');
    }
}

