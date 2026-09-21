<x-admin-layout>
    <main class="flex-1 overflow-y-auto p-6 bg-gray-100">

        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Edit Product</h1>
            <a href="{{ route('admin.products') }}"
                class="border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 px-5 py-2.5 rounded-lg text-sm font-medium transition flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> Back to Products
            </a>
        </div>

        <form action="{{ route('admin.product.update',$product->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                        <h3 class="font-bold text-gray-800 mb-4 border-b pb-2">Basic Information</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Product Name *</label>
                                <input type="text" id="product-name" name="name" placeholder="e.g. Modern Sofa" value="{{ old('name',$product->name)}}"
                                    class="w-full border px-4 py-2 rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary text-sm"
                                    required>
                                @error('name')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
                                <input type="text" id="product-slug" name="slug" placeholder="e.g. modern-sofa" value="{{ old('slug',$product->slug) }}"
                                    class="w-full border px-4 py-2 rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary text-sm bg-gray-50">
                                @error('slug')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Short Description</label>
                                <textarea id="short_description" name="short_description" rows="3"
                                    placeholder="Brief summary..."
                                    class="w-full border px-4 py-2 rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary text-sm">{{ old('short_description',$product->short_description) }}</textarea>
                                @error('short_description')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                <textarea id="description" name="description" rows="18"
                                    placeholder="Detailed description..."
                                    class="w-full border px-4 py-2 rounded-lg focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary text-sm">{{ old('description',$product->description) }}</textarea>
                                @error('description')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                        <h3 class="font-bold text-gray-800 mb-4 border-b pb-2">Pricing & Inventory</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Regular Price ($)</label>
                                <input type="number" id="regular_price" name="regular_price" placeholder="0.00" value="{{ old('regular_price',$product->regular_price) }}"
                                    class="w-full border px-4 py-2 rounded-lg focus:outline-none focus:border-primary text-sm">
                                @error('regular_price')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror    
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Sale Price ($)</label>
                                <input type="number" id="sale_price" name="sale_price" placeholder="0.00" value="{{ old('sale_price',$product->sale_price) }}"
                                    class="w-full border px-4 py-2 rounded-lg focus:outline-none focus:border-primary text-sm">
                                @error('sale_price')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">SKU</label>
                                <input type="text" id="SKU" name="SKU" placeholder="Product SKU" value="{{ old('SKU',$product->SKU) }}"
                                    class="w-full border px-4 py-2 rounded-lg focus:outline-none focus:border-primary text-sm">
                                @error('SKU')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror    
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Stock Status</label>
                                <select id="stock_status" name="stock_status"
                                    class="w-full border px-4 py-2 rounded-lg focus:outline-none focus:border-primary text-sm bg-white">
                                    <option value="instock" {{ old('stock_status',$product->stock_status) == 'instock' ? 'selected' : '' }}>In Stock</option>
                                    <option value="outofstock" {{ old('stock_status',$product->stock_status) == 'outofstock' ? 'selected' : '' }}>Out of Stock</option>
                                </select>
                                @error('stock_status')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror    
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
                                <input type="number" id="quantity" name="quantity" placeholder="Total items" value="{{ old('quantity',$product->quantity) }}"
                                    class="w-full border px-4 py-2 rounded-lg focus:outline-none focus:border-primary text-sm">
                                @error('quantity')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror    
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                        <h3 class="font-bold text-gray-800 mb-4 border-b pb-2">Publish</h3>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Status:</span>
                                <select id="status" name="status"
                                    class="border rounded text-sm px-2 py-1 bg-white focus:outline-none">
                                    <option value="0" {{ old('status',$product->status)  ? '' : 'selected' }}>Draft</option>
                                    <option value="1" {{ old('status',$product->status)  ? 'selected' : '' }}>Published</option>
                                </select>
                                @error('status')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="flex items-center gap-2 pt-2">
                                <input type="checkbox" id="featured" name="featured" value="1" {{ old('featured',$product->featured) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-primary focus:ring-primary">
                                <label for="featured" class="text-sm text-gray-700 cursor-pointer">This is a featured
                                    product</label>
                            </div>
                            <button type="submit"
                                class="w-full bg-primary hover:bg-blue-600 text-white py-2 rounded-lg text-sm font-medium transition mt-4 shadow">
                                    Save Product
                            </button>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                        <h3 class="font-bold text-gray-800 mb-4 border-b pb-2">Organization</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                                <select id="category_id" name="category_id"
                                    class="w-full border px-4 py-2 rounded-lg focus:outline-none focus:border-primary text-sm bg-white">
                                    <option value="">Select Category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Brand</label>
                                <select id="brand_id" name="brand_id"
                                    class="w-full border px-4 py-2 rounded-lg focus:outline-none focus:border-primary text-sm bg-white">
                                    <option value="">Select Brand</option>
                                    @foreach ($brands as $brand)
                                        <option value="{{ $brand->id }}" {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                                    @endforeach
                                </select>
                                @error('brand_id')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                        <h3 class="font-bold text-gray-800 mb-4 border-b pb-2">Product Image (Main)</h3>
                        
                        <label for="product-image" id="single-upload-label" class="block border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:bg-gray-50 transition cursor-pointer mb-4">
                            <i class="fa-solid fa-cloud-arrow-up text-3xl text-gray-400 mb-2"></i>
                            <p class="text-sm text-gray-500">Upload New Main Image</p>
                            <input type="file" id="product-image" name="image" class="hidden" accept="image/png, image/jpeg, image/jpg, image/webp">
                        </label>

                        <div id="single-new-preview-container" class="hidden mb-6 relative h-40 bg-gray-50 rounded border border-blue-200 flex items-center justify-center overflow-hidden group shadow-sm">
                            <img id="single-image-preview" src="" class="max-w-full max-h-full object-contain">
                            <button type="button" id="remove-single-new-btn" class="absolute top-2 right-2 bg-red-500 text-white rounded-md w-7 h-7 flex items-center justify-center text-sm shadow-md hover:bg-red-600 transition focus:outline-none">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>

                        @if($product->image)
                        <h4 class="text-sm font-medium text-gray-700 mb-3 border-b pb-1">Existing Image</h4>
                        <div class="grid grid-cols-3 gap-2">
                            <div class="existing-image-wrapper relative group h-24 bg-gray-100 rounded border overflow-hidden">
                                <img src="{{ asset('uploads/products/thumbnails/' . $product->image) }}" class="w-full h-full object-cover" alt="{{ $product->name }}">
                                <div class="remove-existing-btn absolute inset-0 bg-black bg-opacity-50 hidden group-hover:flex items-center justify-center cursor-pointer text-white transition-opacity" data-input-name="deleted_main_image" data-image="{{ $product->image }}">
                                    <i class="fa-solid fa-trash pointer-events-none"></i>
                                </div>
                            </div>                                                                      
                        </div>
                        @endif
                    </div>

                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                        <h3 class="font-bold text-gray-800 mb-4 border-b pb-2">Product Gallery Images</h3>
                        
                        <label for="product-images" class="block border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:bg-gray-50 transition cursor-pointer mb-4">
                            <i class="fa-solid fa-cloud-arrow-up text-3xl text-gray-400 mb-2"></i>
                            <p class="text-sm text-gray-500">Upload New Gallery Images</p>
                            <input type="file" id="product-images" name="images[]" class="hidden" multiple accept="image/png, image/jpeg, image/jpg, image/webp">
                        </label>

                        <div id="gallery-new-preview-container" class="grid grid-cols-3 gap-2 mb-6"></div>

                        @if($product->images)
                        <h4 class="text-sm font-medium text-gray-700 mb-3 border-b pb-1">Existing Gallery Images</h4>
                        <div class="grid grid-cols-3 gap-2">
                            @foreach (explode(',', $product->images) as $img)
                            <div class="existing-image-wrapper relative group h-20 bg-gray-100 rounded border overflow-hidden">
                                <img src="{{ asset('uploads/products/thumbnails/' . $img) }}" class="w-full h-full object-cover" alt="{{ $product->name }}">
                                <div class="remove-existing-btn absolute inset-0 bg-black bg-opacity-50 hidden group-hover:flex items-center justify-center cursor-pointer text-white transition-opacity" data-input-name="deleted_gallery_images[]" data-image="{{ $img }}">
                                    <i class="fa-solid fa-trash pointer-events-none"></i>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
            </div>
            <div id="deleted-existing-images-container" class="hidden"></div>
        </form>

    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            
            // ==========================================
            // 1. AUTO-SLUG GENERATOR
            // ==========================================
            const productNameInput = document.getElementById('product-name');
            const productSlugInput = document.getElementById('product-slug');

            if (productNameInput && productSlugInput) {
                productNameInput.addEventListener('input', function() {
                    const slug = this.value.toLowerCase()
                        .trim()
                        .replace(/[^a-z0-9 -]/g, '')
                        .replace(/\s+/g, '-')
                        .replace(/-+/g, '-');
                    productSlugInput.value = slug;
                });
            }

            // ==========================================
            // 2. SINGLE NEW IMAGE UPLOAD (MAIN)
            // ==========================================
            const singleImageInput = document.getElementById('product-image');
            const singleUploadLabel = document.getElementById('single-upload-label');
            const singlePreviewContainer = document.getElementById('single-new-preview-container');
            const singleImagePreview = document.getElementById('single-image-preview');
            const removeSingleNewBtn = document.getElementById('remove-single-new-btn');

            if (singleImageInput) {
                singleImageInput.addEventListener('change', function(event) {
                    const file = event.target.files[0];
                    if (file && file.type.startsWith('image/')) {
                        singleImagePreview.src = URL.createObjectURL(file);
                        singleUploadLabel.classList.add('hidden');
                        singlePreviewContainer.classList.remove('hidden');
                        singlePreviewContainer.classList.add('flex');
                    } else {
                        resetSingleNewImage();
                    }
                });
            }

            if (removeSingleNewBtn) {
                removeSingleNewBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    resetSingleNewImage();
                });
            }

            function resetSingleNewImage() {
                singleImageInput.value = '';
                singleImagePreview.src = '';
                singlePreviewContainer.classList.add('hidden');
                singlePreviewContainer.classList.remove('flex');
                singleUploadLabel.classList.remove('hidden');
            }

            // ==========================================
            // 3. MULTIPLE NEW IMAGE UPLOAD (GALLERY)
            // ==========================================
            const galleryInput = document.getElementById('product-images');
            const galleryPreviewContainer = document.getElementById('gallery-new-preview-container');
            let selectedGalleryFiles = []; // Array to store multiple files

            if (galleryInput && galleryPreviewContainer) {
                galleryInput.addEventListener('change', function(event) {
                    const files = Array.from(event.target.files);
                    
                    files.forEach(file => {
                        if (file.type.startsWith('image/')) {
                            // Prevent duplicates
                            const isDuplicate = selectedGalleryFiles.some(f => f.name === file.name && f.size === file.size);
                            if (!isDuplicate) {
                                selectedGalleryFiles.push(file);
                            }
                        }
                    });
                    updateGalleryPreviews();
                });
            }

            function updateGalleryPreviews() {
                // Sync the HTML input
                const dataTransfer = new DataTransfer();
                selectedGalleryFiles.forEach(file => dataTransfer.items.add(file));
                galleryInput.files = dataTransfer.files;

                // Clear current previews
                galleryPreviewContainer.innerHTML = '';

                // Re-render
                selectedGalleryFiles.forEach((file, index) => {
                    const objectUrl = URL.createObjectURL(file);
                    
                    const div = document.createElement('div');
                    div.className = 'relative h-20 bg-gray-50 rounded border border-blue-200 flex items-center justify-center overflow-hidden group shadow-sm';
                    
                    const img = document.createElement('img');
                    img.src = objectUrl;
                    img.className = 'w-full h-full object-cover';
                    
                    const removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.className = 'absolute top-1 right-1 bg-red-500 text-white rounded-md w-6 h-6 flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 transition-opacity focus:outline-none shadow-md';
                    removeBtn.innerHTML = '<i class="fa-solid fa-xmark"></i>';
                    
                    removeBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        selectedGalleryFiles.splice(index, 1);
                        updateGalleryPreviews();
                        URL.revokeObjectURL(objectUrl);
                    });

                    div.appendChild(img);
                    div.appendChild(removeBtn);
                    galleryPreviewContainer.appendChild(div);
                });
            }

            // ==========================================
            // 4. REMOVE EXISTING IMAGES (TRACK FOR BACKEND)
            // ==========================================
            const removeExistingBtns = document.querySelectorAll('.remove-existing-btn');
            const deletedImagesContainer = document.getElementById('deleted-existing-images-container');

            removeExistingBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    // We pull the input name dynamically so PHP knows if it's the main image or a gallery array
                    const inputName = this.getAttribute('data-input-name'); 
                    const imageName = this.getAttribute('data-image');
                    const wrapper = this.closest('.existing-image-wrapper');
                    
                    // Hide the image from the UI
                    wrapper.style.display = 'none';
                    
                    // Create a hidden input for PHP
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = inputName;
                    hiddenInput.value = imageName;
                    
                    if (deletedImagesContainer) {
                        deletedImagesContainer.appendChild(hiddenInput);
                    }
                });
            });

        });
    </script>
</x-admin-layout>