<x-admin-layout>
    <main class="flex-1 overflow-y-auto p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Add New Category</h1>
            <a href="{{ route('admin.categories') }}"
                class="border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 px-5 py-2.5 rounded-lg text-sm font-medium transition flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> Back to Categories
            </a>
        </div>
        <div class="max-w-3xl mx-auto">
            <form method="POST" action="{{ route('admin.category.store') }}" enctype="multipart/form-data" class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 space-y-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Category Name *</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="e.g. Samsung"
                            class="w-full border @error('name') border-red-500 @else border-gray-300 @enderror px-4 py-2 rounded-lg outline-none focus:ring-1 focus:ring-primary">
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Category Slug</label>
                        <input type="text" id="slug" name="slug" value="{{ old('slug') }}" placeholder="samsung"
                            class="w-full border @error('slug') border-red-500 @else border-gray-300 @enderror px-4 py-2 rounded-lg bg-gray-50 outline-none focus:bg-white focus:ring-1 focus:ring-primary">
                        @error('slug')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="parent_id" class="block text-sm font-medium text-gray-700 mb-1">Parent Category</label>
                    <select name="parent_id" id="parent_id"
                        class="w-full border @error('parent_id') border-red-500 @else border-gray-300 @enderror px-4 py-2 rounded-lg bg-gray-50 outline-none focus:bg-white focus:ring-1 focus:ring-primary">
                        <option value="">Select Parent Category</option>
                        @foreach ($parentCategories as $parentCategory)
                            <option value="{{ $parentCategory->id }}">{{ $parentCategory->name }}</option>
                        @endforeach
                    </select>
                    @error('parent_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Category Image *</label>
                    <div class="relative flex items-center justify-center w-full h-40">

                        <label for="category-image"
                            class="relative flex flex-col items-center justify-center w-full h-full border-2 @error('image') border-red-400 @else border-gray-300 @enderror border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition overflow-hidden">

                            <div id="upload-content" class="flex flex-col items-center justify-center pt-5 pb-6 z-10">
                                <i class="fa-solid fa-image text-3xl text-gray-400 mb-2"></i>
                                <p class="text-sm text-gray-500">Upload category image (PNG/JPG/WEBP)</p>
                            </div>

                            <img id="image-preview"
                                class="hidden absolute inset-0 w-full h-full object-contain p-2 z-20 bg-white" src=""
                                alt="Image Preview">

                            <input id="category-image" name="image" type="file" class="hidden"
                                accept="image/png, image/jpeg, image/jpg, image/webp" />
                        </label>

                        <button type="button" id="remove-logo-btn"
                            class="hidden absolute top-2 right-2 z-30 bg-white text-red-500 hover:text-white hover:bg-red-500 rounded-full w-8 h-8 flex items-center justify-center shadow-md border border-gray-200 transition-colors focus:outline-none"
                            title="Remove image">
                            <i class="fa-solid fa-xmark"></i>
                        </button>

                    </div>
                    @error('image')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" id="status" name="status" value="1" {{ old('status', 1) ? 'checked' : '' }}
                        class="w-4 h-4 text-primary rounded border-gray-300 focus:ring-primary">
                    <label for="status" class="text-sm text-gray-700">Set as Active Category</label>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t">
                    <a href="{{ route('admin.categories') }}"
                        class="px-6 py-2 border rounded-lg hover:bg-gray-50 transition text-sm">Cancel</a>
                    <button type="submit"
                        class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-blue-600 transition text-sm font-medium shadow-sm">Save
                        Category</button>
                </div>
            </form>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const imageInput = document.getElementById('category-image');
            const imagePreview = document.getElementById('image-preview');
            const uploadContent = document.getElementById('upload-content');
            const removeBtn = document.getElementById('remove-logo-btn');
            const nameInput = document.getElementById('name');
            const slugInput = document.getElementById('slug');

            if (imageInput) {
                imageInput.addEventListener('change', function (e) {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function (event) {
                            imagePreview.src = event.target.result;
                            imagePreview.classList.remove('hidden');
                            uploadContent.classList.add('hidden');
                            removeBtn.classList.remove('hidden');
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }

            if (removeBtn) {
                removeBtn.addEventListener('click', function (e) {
                    e.preventDefault();
                    imageInput.value = '';
                    imagePreview.src = '';
                    imagePreview.classList.add('hidden');
                    uploadContent.classList.remove('hidden');
                    removeBtn.classList.add('hidden');
                });
            }

            if (nameInput && slugInput) {
                nameInput.addEventListener('keyup', function () {
                    slugInput.value = this.value
                        .toLowerCase()
                        .trim()
                        .replace(/[^\w\s-]/g, '')
                        .replace(/[\s_-]+/g, '-')
                        .replace(/^-+|-+$/g, '');
                });
            }
        });
    </script>
</x-admin-layout>