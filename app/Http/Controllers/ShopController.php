<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query()->where('status', true);

        $sortBy = $request->input('sort_by', 'newest');

        match($sortBy){
            'price_asc' => $query->orderBy('sale_price', 'asc'),
            'price_desc' => $query->orderBy('sale_price', 'desc'),
            'featured' => $query->where('featured', true),
            default => $query->latest()
        };

        $perpage = $request->input('per_page', 12);

        if($request->filled('brand')){
            $brandids = (array) $request->input('brand', []);
            $query->whereIn('brand_id', $brandids);
        }

        if($request->filled('category')){
            $categoryids = (array) $request->input('category', []);
            $childCategoryIds = Category::whereIn('parent_id', $categoryids)->pluck('id')->toArray();
            $allCategoryIds = array_unique(array_merge($categoryids, $childCategoryIds));
            $query->whereIn('category_id', $allCategoryIds);
        }

        $minPriceLimit = 0;
        $maxProductPrice = Product::where('status', true)->max('regular_price') ?? 10000;
        $maxPriceLimit = (int) max(10000, ceil($maxProductPrice / 1000) * 1000);

        if($request->filled('min_price') && $request->filled('max_price')){
            $minPrice = (float) $request->input('min_price');
            $maxPrice = (float) $request->input('max_price');
            
            if ($minPrice > $minPriceLimit || $maxPrice < $maxPriceLimit) {
                $query->where(function ($q) use ($minPrice, $maxPrice) {
                    $q->where(function ($sub) use ($minPrice, $maxPrice) {
                        $sub->whereNotNull('sale_price')
                            ->where('sale_price', '>', 0)
                            ->whereBetween('sale_price', [$minPrice, $maxPrice]);
                    })->orWhere(function ($sub) use ($minPrice, $maxPrice) {
                        $sub->where(function ($s) {
                            $s->whereNull('sale_price')
                              ->orWhere('sale_price', '<=', 0);
                        })->whereBetween('regular_price', [$minPrice, $maxPrice]);
                    });
                });
            }
        }
        
        if($request->filled('search')){
            $searchTerm = strtolower($request->input('search'));
            $query->where(function($q) use($searchTerm){
                $q->where('name', 'LIKE', '%'. $searchTerm . '%');
                $q->orWhere('description', 'LIKE', '%'. $searchTerm . '%');
                $q->orWhere('short_description', 'LIKE', '%'. $searchTerm . '%');
            });
        }

        $products = $query->paginate($perpage)->withQueryString();

        $brands = Brand::withCount(['products' => function($q) {
            $q->where('status', true);
        }])->orderBy('name', 'asc')->get();

        $categories = Category::with('children')->withCount(['products' => function($q) {
            $q->where('status', true);
        }])->orderBy('name', 'asc')->get();

        $categories->each(function($category) {
            if ($category->children->isNotEmpty()) {
                $childIds = $category->children->pluck('id')->toArray();
                $allIds = array_merge([$category->id], $childIds);
                $category->products_count = Product::whereIn('category_id', $allIds)->where('status', true)->count();
            }
        });

        return view('shop.index', compact('products', 'brands', 'categories', 'minPriceLimit', 'maxPriceLimit'));
    }

    public function productDetails(String $slug)
    {
        $product = Product::where('slug',$slug)->firstOrFail();
        $relatedProducts = Product::where('category_id',$product->category_id)
                            ->where('id','!=',$product->id)
                            ->where('status',true)
                            ->orderBy('created_at','desc')
                            ->take(8)
                            ->get();
        return view('shop.details',compact('product','relatedProducts'));
    }
}
