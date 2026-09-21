<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProductExport;

class ProductController extends Controller
{
    public function products(Request $request)
    {

        $sort_by = $request->input('sort_by','created_at');
        $sort_order = $request->input('sort_order','desc');
        
        $query = Product::query();

        if($request->filled('search')){
            $search = $request->input('search');
            $query->where(function($q) use ($search){
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('sku', 'LIKE', "%{$search}%");
            });
        }

        if($request->filled('category')){
            $query->where('category_id',$request->category);
        }

        if($request->filled('brand')){
            $query->where('brand_id',$request->brand);
        }

        if($request->has('status') && $request->status != null){
            $query->where('status',$request->status);
        }

        $categories = Category::select('id','name')->orderBy('name')->get();
        $brands = Brand::select('id','name')->orderBy('name')->get();

        $products = $query->orderBy($sort_by,$sort_order)->paginate(10)->withQueryString();
        return view('admin.products',compact('products','categories','brands'));
    }

    public function productAdd()
    {
        $categories = Category::select('id','name')->orderBy('name')->get();
        $brands = Brand::select('id','name')->orderBy('name')->get();
        return view('admin.product-add',compact('categories','brands'));
    }

    public function productStore(Request $request)
    {
        $request->validate([
            'name'=>'required|string|max:255',
            'slug'=>'required|string|max:255|unique:products,slug',
            'short_description'=>'nullable|string|max:500',
            'information'=>'nullable|string|max:4000',
            'description'=>'required|string',
            'regular_price'=>'required|numeric|min:0',
            'sale_price'=>'nullable|numeric|min:0|lte:regular_price',
            'SKU'=>'required|string|max:100|unique:products,sku',
            'stock_status'=>'required|in:instock,outofstock',
            'quantity'=>'required|integer|min:0',
            'featured'=>'nullable|boolean',
            'status'=>'nullable|boolean',
            'category_id'=>'nullable|exists:categories,id',
            'brand_id'=>'nullable|exists:brands,id',
            'image'=>'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'images.*'=>'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $product = new Product();
        $product->name = $request->name;
        $product->slug = $request->slug;
        $product->short_description = $request->short_description;
        $product->information = $request->information;
        $product->description = $request->description;
        $product->regular_price = $request->regular_price;
        $product->sale_price = $request->sale_price;
        $product->SKU = $request->SKU;
        $product->stock_status = $request->stock_status;
        $product->quantity = $request->quantity;
        $product->featured = $request->has('featured') ? 1 : 0;
        $product->status = $request->status ?? 0;
        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;

        $current_timestamp = Carbon::now()->timestamp;

        if($request->hasFile('image')){
            $image = $request->file('image');
            $imageName = $current_timestamp.'_'.$image->getClientOriginalName();
            $this->resizeAndSaveImage($image,$imageName,'uploads/products',570,604);
            $this->resizeAndSaveImage($image,$imageName,'uploads/products/thumbnails',270,303);

            $product->image = $imageName;
        }

        $gallery_arr = array();
        $gallery_images = "";
        $counter = 1;

        if($request->hasFile('images')){
            $allowed_extensions = array("jpg","jpeg","png","gif","webp");
            $files = $request->file('images');
            foreach($files as $file){
                $gextention = $file->getClientOriginalExtension();
                $gcheck = in_array(strtolower($gextention),$allowed_extensions);
                if($gcheck){
                    $gimageName = $current_timestamp.'_'.$counter.'_'.$file->getClientOriginalName();
                    $this->resizeAndSaveImage($file,$gimageName,'uploads/products',570,604);
                    $this->resizeAndSaveImage($file,$gimageName,'uploads/products/thumbnails',270,303);
                    array_push($gallery_arr,$gimageName);
                    $counter++;
                }
            }
            $gallery_images = implode(',', $gallery_arr);
        }

        $product->images = $gallery_images;

        $product->save();

        return redirect()->route('admin.products')->with('success','Product added successfully');
    }

    public function resizeAndSaveImage($image, $imageName, $folder, $width=270, $height=303)
    {
        $imagepath = public_path($folder);
        if(!file_exists($imagepath)){
            mkdir($imagepath,0755,true);
        }
        
        $manager = new ImageManager(new Driver());
        $manager->decode($image)->resize($width,$height)->save($imagepath . '/' . $imageName);
    }

    public function productEdit($id){
        $product = Product::findOrFail($id);
        $categories = Category::select('id','name')->orderBy('name')->get();
        $brands = Brand::select('id','name')->orderBy('name')->get();
        return view('admin.product-edit',compact('product','categories','brands'));
    }

    public function productUpdate(Request $request,$id)
    {
        $product = Product::findOrFail($id);
        $request->validate([
            'name'=>'required|string|max:255',
            'slug'=>'required|string|max:255|unique:products,slug,'.$product->id,
            'short_description'=>'nullable|string|max:500',
            'information'=>'nullable|string|max:4000',
            'description'=>'required|string',
            'regular_price'=>'required|numeric|min:0',
            'sale_price'=>'nullable|numeric|min:0|lte:regular_price',
            'SKU'=>'required|string|max:100|unique:products,sku,'.$product->id,
            'stock_status'=>'required|in:instock,outofstock',
            'quantity'=>'required|integer|min:0',
            'featured'=>'nullable|boolean',
            'status'=>'nullable|boolean',
            'category_id'=>'nullable|exists:categories,id',
            'brand_id'=>'nullable|exists:brands,id',
            'image'=>'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'images.*'=>'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $product->name = $request->name;
        $product->slug = $request->slug;
        $product->short_description = $request->short_description;
        $product->information = $request->information;
        $product->description = $request->description;
        $product->regular_price = $request->regular_price;
        $product->sale_price = $request->sale_price;
        $product->SKU = $request->SKU;
        $product->stock_status = $request->stock_status;
        $product->quantity = $request->quantity;
        $product->featured = $request->boolean('featured');
        $product->status = $request->boolean('status');
        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;

        $current_timestamp = Carbon::now()->timestamp;

        if($request->hasFile('image')){
            if($product->image && file_exists(public_path('uploads/products/'.$product->image))){
                @unlink(public_path('uploads/products/'.$product->image));
            }
            if($product->image && file_exists(public_path('uploads/products/thumbnails/'.$product->image))){
                @unlink(public_path('uploads/products/thumbnails/'.$product->image));
            }
            $image = $request->file('image');
            $imageName = $current_timestamp.'_'.$image->getClientOriginalName();
            $this->resizeAndSaveImage($image,$imageName,'uploads/products',570,604);
            $this->resizeAndSaveImage($image,$imageName,'uploads/products/thumbnails',270,303);

            $product->image = $imageName;
        } elseif ($request->has('deleted_main_image')) {
            if($product->image && file_exists(public_path('uploads/products/'.$product->image))){
                @unlink(public_path('uploads/products/'.$product->image));
            }
            if($product->image && file_exists(public_path('uploads/products/thumbnails/'.$product->image))){
                @unlink(public_path('uploads/products/thumbnails/'.$product->image));
            }
            $product->image = null;
        }

        $gallery_arr = $product->images ? explode(',', $product->images) : [];
        if($request->has('deleted_gallery_images') && is_array($request->deleted_gallery_images)){
            foreach($request->deleted_gallery_images as $deletedImage)
            {
                if($deletedImage && file_exists(public_path('uploads/products/'.$deletedImage))){
                    @unlink(public_path('uploads/products/'.$deletedImage));
                }
                if($deletedImage && file_exists(public_path('uploads/products/thumbnails/'.$deletedImage))){
                    @unlink(public_path('uploads/products/thumbnails/'.$deletedImage));
                }
                
                if(($key=array_search($deletedImage,$gallery_arr))!==false){
                    unset($gallery_arr[$key]);
                }
            }
        }

        if($request->hasFile('images')){
            $allowed_extensions = array("jpg","jpeg","png","gif","webp");
            $files = $request->file('images');
            $counter = 1;
            foreach($files as $file){
                $gextention = $file->getClientOriginalExtension();
                $gcheck = in_array(strtolower($gextention),$allowed_extensions);
                if($gcheck){
                    $gimageName = $current_timestamp.'_'.$counter.'_'.$file->getClientOriginalName();
                    $this->resizeAndSaveImage($file,$gimageName,'uploads/products',570,604);
                    $this->resizeAndSaveImage($file,$gimageName,'uploads/products/thumbnails',270,303);
                    array_push($gallery_arr,$gimageName);
                    $counter++;
                }
            }
        }
        $product->images = !empty($gallery_arr) ? implode(',', array_values($gallery_arr)) : null;

        $product->save();

        return redirect()->route('admin.products')->with('success','Product updated successfully');
    }

    public function productDelete($id)
    {
        $product = Product::findOrFail($id);

        if($product->image && file_exists(public_path('uploads/products/'.$product->image)))
        {
            @unlink(public_path('uploads/products/'.$product->image));
        }
        if($product->image && file_exists(public_path('uploads/products/thumbnails/'.$product->image)))
        {
            @unlink(public_path('uploads/products/thumbnails/'.$product->image));
        }

        if($product->images)
        {
            $gallery_arr = explode(',', $product->images);
            foreach($gallery_arr as $gallery)
            {
                if($gallery && file_exists(public_path('uploads/products/'.$gallery)))
                {
                    @unlink(public_path('uploads/products/'.$gallery));
                }
                if($gallery && file_exists(public_path('uploads/products/thumbnails/'.$gallery)))
                {
                    @unlink(public_path('uploads/products/thumbnails/'.$gallery));
                }
            }
        }

        $product->delete();
        return redirect()->route('admin.products')->with('success','Product deleted successfully');  
    }

    public function productsBulkDelete(Request $request)
    {
        $request->validate([
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,id',
        ]);

        $ids = $request->product_ids;
        $products = Product::whereIn('id',$ids)->get();

        foreach ($products as $product) {
            if($product->image && file_exists(public_path('uploads/products/'.$product->image))){
                @unlink(public_path('uploads/products/'.$product->image));
            }
            if($product->image && file_exists(public_path('uploads/products/thumbnails/'.$product->image))){
                @unlink(public_path('uploads/products/thumbnails/'.$product->image));
            }

            if($product->images)
            {
                $gallery_arr = explode(',', $product->images);
                foreach($gallery_arr as $gallery)
                {
                    if($gallery && file_exists(public_path('uploads/products/'.$gallery)))
                    {
                        @unlink(public_path('uploads/products/'.$gallery));
                    }
                    if($gallery && file_exists(public_path('uploads/products/thumbnails/'.$gallery)))
                    {
                        @unlink(public_path('uploads/products/thumbnails/'.$gallery));
                    }
                }
            }
            $product->delete();
        }

        return back()->with('success',count($ids).' products deleted successfully');
    }

    public function productExport()
    {
        return Excel::download(new ProductExport, 'products.xlsx');
    }
}

