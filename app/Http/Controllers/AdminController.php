<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use Illuminate\Http\Request;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function index(){
        return view('admin.index');
    }

    public function brands(Request $request){
        $query = Brand::query();

        if($request->filled('search')){
            $query->where('name', 'like', "%{$request->search}%");
        }

        if($request->filled('status')){
            $query->where('status', $request->status);
        }

        $brands = $query->orderBy('id', 'DESC')->paginate(10)->withQueryString();
        return view('admin.brands', compact('brands'));
    }

    public function brandAdd(){
        return view('admin.brand-add');
    }

    public function brandStore(Request $request){
        $request->validate([
            'name'=>'required|string|max:255',
            'slug'=>'nullable|string|max:255|unique:brands,slug',
            'image'=>'nullable|image|mimes:png,jpg,jpeg,gif,svg,webp|max:2048',
            'status'=>'nullable|boolean',
        ]);

        $brand = new Brand();
        $brand->name = $request->name;
        $brand->slug = $request->slug ? Str::slug($request->slug) : Str::slug($request->name);
        $brand->status = $request->has('status') ? 1 : 0;

        if($request->hasFile('image')){
            $destinationPath = public_path('uploads/brands');
            if(!file_exists($destinationPath)){
                mkdir($destinationPath, 0755, true);
            }
            $image = $request->file('image');
            $imageName = time().'_'.uniqid().'.'.$image->extension();
            $this->generateThumbnailImage($image, $imageName, 'uploads/brands');
            $image->move($destinationPath, $imageName);
            $brand->image = $imageName;
        }

        $brand->save();
        return redirect()->route('admin.brands')->with('success','Brand Added Successfully');
    }

    public function generateThumbnailImage($image,$imageName,$folder,$width=124,$height=124){
        $thumbnailPath = public_path($folder.'/thumbnails');
        if(!file_exists($thumbnailPath)){
            mkdir($thumbnailPath, 0755, true);
        }
        $manager = new ImageManager(new Driver());
        $manager->decode($image)->resize($width,$height)->save($thumbnailPath . '/' . $imageName);
    }

    public function brandEdit($id){
        $brand = Brand::findOrFail($id);
        return view('admin.brand-edit', compact('brand'));
    }

    public function brandUpdate(Request $request, $id){
        $request->validate([
            'name'=>'required|string|max:255',
            'slug'=>'nullable|string|max:255|unique:brands,slug,'.$id,
            'image'=>'nullable|image|mimes:png,jpg,jpeg,gif,svg,webp|max:2048',
            'status'=>'nullable|boolean',
        ]);

        $brand = Brand::findOrFail($id);
        $brand->name = $request->name;
        $brand->slug = $request->slug ? Str::slug($request->slug) : Str::slug($request->name);
        $brand->status = $request->has('status') ? 1 : 0;

        if($request->hasFile('image')){
            if($brand->image){
                @unlink(public_path('uploads/brands').'/'.$brand->image);
                @unlink(public_path('uploads/brands/thumbnails').'/'.$brand->image);
            }

            $destinationPath = public_path('uploads/brands');
            if(!file_exists($destinationPath)){
                mkdir($destinationPath, 0755, true);
            }
            $image = $request->file('image');
            $imageName = time().'_'.uniqid().'.'.$image->extension();
            $this->generateThumbnailImage($image, $imageName, 'uploads/brands');
            $image->move($destinationPath, $imageName);
            $brand->image = $imageName;
        }

        $brand->save();
        return redirect()->route('admin.brands')->with('success','Brand Updated Successfully');
    }

    public function brandDelete($id){
        $brand = Brand::findOrFail($id);

        if($brand->image){
            @unlink(public_path('uploads/brands').'/'.$brand->image);
            @unlink(public_path('uploads/brands/thumbnails').'/'.$brand->image);
        }
        
        $brand->delete();
        return redirect()->route('admin.brands')->with('success','Brand Deleted Successfully');
    }

    public function categories(Request $request){
        $query = Category::query();

        if($request->filled('search')){
            $query->where('name', 'like', "%{$request->search}%");
        }

        if($request->filled('status')){
            $query->where('status', $request->status);
        }

        $categories = $query->orderBy('id', 'DESC')->paginate(10)->withQueryString();
        return view('admin.categories', compact('categories'));
    }

    public function categoryAdd(){
        $parentCategories = Category::where('parent_id', null)->orderBy('name','ASC')->get();
        return view('admin.category-add', compact('parentCategories'));
    }

    public function categoryStore(Request $request){
        $request->validate([
            'name'=>'required|string|max:255',
            'slug'=>'nullable|string|max:255|unique:categories,slug',
            'parent_id'=>'nullable|exists:categories,id',
            'image'=>'nullable|image|mimes:png,jpg,jpeg,gif,svg,webp|max:2048',
            'status'=>'nullable|boolean',
        ]);

        $category = new Category();
        $category->name = $request->name;
        $category->slug = $request->slug ? Str::slug($request->slug) : Str::slug($request->name);
        $category->parent_id = $request->parent_id;
        $category->status = $request->has('status') ? 1 : 0;

        if($request->hasFile('image')){
            $destinationPath = public_path('uploads/categories');
            if(!file_exists($destinationPath)){
                mkdir($destinationPath, 0755, true);
            }
            $image = $request->file('image');
            $imageName = time().'_'.uniqid().'.'.$image->extension();
            $this->generateThumbnailImage($image, $imageName, 'uploads/categories');
            $image->move($destinationPath, $imageName);
            $category->image = $imageName;
        }

        $category->save();
        return redirect()->route('admin.categories')->with('success','Category Added Successfully');
    }

    public function categoryEdit($id){
        $category = Category::findOrFail($id);

        $parentCategories = Category::where('parent_id', null)->where('id', '!=', $category->id)->orderBy('name','ASC')->get();
        return view('admin.category-edit', compact('category', 'parentCategories'));
    }

    public function categoryUpdate(Request $request, $id){
        $request->validate([
            'name'=>'required|string|max:255',
            'slug'=>'nullable|string|max:255|unique:categories,slug,'.$id,
            'parent_id'=>'nullable|exists:categories,id',
            'image'=>'nullable|image|mimes:png,jpg,jpeg,gif,svg,webp|max:2048',
            'status'=>'nullable|boolean',
        ]);

        $category = Category::findOrFail($id);
        $category->name = $request->name;
        $category->slug = $request->slug ? Str::slug($request->slug) : Str::slug($request->name);
        $category->parent_id = $request->parent_id;
        $category->status = $request->has('status') ? 1 : 0;

        if($request->hasFile('image')){
            if($category->image){
                @unlink(public_path('uploads/categories').'/'.$category->image);
                @unlink(public_path('uploads/categories/thumbnails').'/'.$category->image);
            }

            $destinationPath = public_path('uploads/categories');
            if(!file_exists($destinationPath)){
                mkdir($destinationPath, 0755, true);
            }
            $image = $request->file('image');
            $imageName = time().'_'.uniqid().'.'.$image->extension();
            $this->generateThumbnailImage($image, $imageName, 'uploads/categories');
            $image->move($destinationPath, $imageName);
            $category->image = $imageName;
        }

        $category->save();
        return redirect()->route('admin.categories')->with('success','Category Updated Successfully');
    }

    public function categoryDelete($id){
        $category = Category::findOrFail($id);

        if($category->image){
            @unlink(public_path('uploads/categories').'/'.$category->image);
            @unlink(public_path('uploads/categories/thumbnails').'/'.$category->image);
        }
        
        $category->delete();
        return redirect()->route('admin.categories')->with('success','Category Deleted Successfully');
    }

}
