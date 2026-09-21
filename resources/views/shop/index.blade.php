<x-app-layout>
    <style>
        .custom-range-slider {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            pointer-events: none;
        }

        .custom-range-slider::-webkit-slider-thumb {
            pointer-events: auto;
            -webkit-appearance: none;
            appearance: none;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #0284c7;
            border: 2px solid #ffffff;
            cursor: pointer;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
        }

        .custom-range-slider::-moz-range-thumb {
            pointer-events: auto;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #0284c7;
            border: 2px solid #ffffff;
            cursor: pointer;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
        }
    </style>

    @if (session('success'))
        <div id="alert-success"
            class="z-50 fixed top-5 right-5 w-auto max-w-md flex items-center justify-between p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl gap-3 text-sm font-medium shadow-lg transition-all duration-500">
            <div class="flex items-center gap-2.5">
                <i class="fa-solid fa-circle-check text-emerald-600 text-lg"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" onclick="document.getElementById('alert-success').remove()"
                class="text-emerald-600 hover:text-emerald-900 transition p-1">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <script>
            setTimeout(() => {
                const el = document.getElementById('alert-success');
                if (el) el.remove();
            }, 3000);
        </script>
    @endif

    <div class="relative bg-sky-700 text-white h-64 flex items-center justify-center bg-cover bg-center"
        style="background-image: url('{{ asset('assets/images/page-banner.jpg') }}');">
        <div class="absolute inset-0 bg-black bg-opacity-40"></div>
        <div class="relative z-10 text-center">
            <h2 class="text-4xl font-bold mb-2">Shop</h2>
            <ul class="flex justify-center space-x-2 text-sm">
                <li><a href="{{ route('home.index') }}" class="hover:text-primary">Home</a></li>
                <li>/</li>
                <li class="text-primary">Shop</li>
            </ul>
        </div>
    </div>

    <div class="container mx-auto px-4 py-16">
        <div class="flex flex-col lg:flex-row gap-8">

            <aside class="w-full lg:w-1/4 order-2 lg:order-1 space-y-8">
                <form id="form-filter" method="GET" action="{{ route('shop.index') }}">
                    <input type="hidden" name="sort_by" id="filter-sort-by" value="{{ request('sort_by', 'newest') }}">
                    <input type="hidden" name="per_page" id="filter-per-page" value="{{ request('per_page', '12') }}">

                    @if (request()->filled('search') || request()->filled('brand') || request()->filled('category') || (request()->filled('min_price') && request('min_price') > $minPriceLimit) || (request()->filled('max_price') && request('max_price') < $maxPriceLimit) || (request()->filled('sort_by') && request('sort_by') !== 'newest') || (request()->filled('per_page') && request('per_page') != 12))
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="font-bold text-gray-800">Active Filters</h2>
                            <a href="{{ route('shop.index') }}"
                                class="text-sm text-red-500 hover:underline font-semibold flex items-center space-x-2">
                                <i class="fa-solid fa-xmark"></i>
                                <span class="lg:inline hidden">Clear All Filters</span>
                            </a>
                        </div>
                    @endif

                    <div class="bg-gray-50 p-6 rounded-lg border mb-8">
                        <div class="relative">
                            <input type="text" placeholder="Search product..." name="search"
                                value="{{ request('search') }}"
                                class="w-full border p-3 rounded focus:outline-none focus:border-primary pr-10">
                            <button type="submit" class="absolute right-3 top-3 text-gray-400 hover:text-primary"><i
                                    class="fa fa-search"></i></button>
                        </div>
                    </div>

                    <div class="bg-gray-50 p-6 rounded-lg border mb-8">
                        <h4 class="font-bold text-lg mb-4">Brands</h4>
                        <ul class="space-y-3">
                            @foreach ($brands as $brand)
                                <li class="flex items-center {{ $loop->iteration > 6 ? 'brand-extra hidden' : '' }}">
                                    <label class="flex items-center cursor-pointer hover:text-primary">
                                        <input type="checkbox" name="brand[]" value="{{ $brand->id }}" {{ in_array($brand->id, (array) request('brand', [])) ? 'checked' : '' }}
                                            class="custom-checkbox hidden filter-checkbox peer">
                                        <div
                                            class="w-4 h-4 border border-gray-300 rounded mr-3 flex items-center justify-center bg-white transition peer-checked:bg-primary peer-checked:border-primary peer-checked:text-white text-transparent">
                                            <i class="fa-check fa text-[10px]"></i>
                                        </div>
                                        {{ $brand->name }} ({{ $brand->products_count }})
                                    </label>
                                </li>
                            @endforeach
                        </ul>
                        @if ($brands->count() > 6)
                            <button type="button" id="toggle-brands-btn" class="mt-4 text-sm font-semibold text-primary hover:underline flex items-center gap-1">
                                <span id="toggle-brands-text">See All</span>
                                <i id="toggle-brands-icon" class="fa fa-chevron-down text-xs"></i>
                            </button>
                        @endif
                    </div>

                    <div class="bg-gray-50 p-6 rounded-lg border mb-8">
                        <h4 class="font-bold text-lg mb-4">Categories</h4>
                        <ul class="space-y-3">
                            @foreach ($categories as $category)
                                <li class="flex items-center {{ $loop->iteration > 6 ? 'category-extra hidden' : '' }}">
                                    <label class="flex items-center cursor-pointer hover:text-primary">
                                        <input type="checkbox" name="category[]" value="{{ $category->id }}" {{ in_array($category->id, (array) request('category', [])) ? 'checked' : '' }} class="custom-checkbox hidden filter-checkbox peer">
                                        <div class="w-4 h-4 border border-gray-300 rounded mr-3 flex items-center justify-center bg-white transition peer-checked:bg-primary peer-checked:border-primary peer-checked:text-white text-transparent">
                                            <i class="fa-check fa text-[10px]"></i>
                                        </div>
                                        {{ $category->name }} ({{ $category->products_count }})
                                    </label>
                                </li>
                            @endforeach
                        </ul>
                        @if ($categories->count() > 6)
                            <button type="button" id="toggle-categories-btn" class="mt-4 text-sm font-semibold text-primary hover:underline flex items-center gap-1">
                                <span id="toggle-categories-text">See All</span>
                                <i id="toggle-categories-icon" class="fa fa-chevron-down text-xs"></i>
                            </button>
                        @endif
                    </div>

                    <div class="bg-gray-50 p-6 rounded-lg border mb-8">
                        <h4 class="font-bold text-lg mb-4">Filter By Price</h4>
                        <div class="relative pt-6 pb-2">
                            {{-- Background Track --}}
                            <div class="absolute w-full h-1 bg-gray-300 rounded top-6 z-0"></div>

                            {{-- Main Track --}}
                            <div class="absolute h-1 bg-primary rounded top-6 z-10" id="range-track" style="left: 0%; right: 0%;"></div>

                            {{-- Range Sliders --}}
                            <input type="range" name="min_price" min="{{ $minPriceLimit }}" max="{{ $maxPriceLimit }}" step="10" value="{{ request('min_price', $minPriceLimit) }}"
                                class="absolute w-full h-1 bg-transparent appearance-none top-6 left-0 pointer-events-none z-20 custom-range-slider price-slider"
                                id="range-min">
                            <input type="range" name="max_price" min="{{ $minPriceLimit }}" max="{{ $maxPriceLimit }}" step="10" value="{{ request('max_price', $maxPriceLimit) }}"
                                class="absolute w-full h-1 bg-transparent appearance-none top-6 left-0 pointer-events-none z-20 custom-range-slider price-slider"
                                id="range-max">

                            <div class="flex justify-between mt-4 text-sm font-medium text-gray-600">
                                <span>$<span id="price-min-display">{{ request('min_price', $minPriceLimit) }}</span></span>
                                <span>$<span id="price-max-display">{{ request('max_price', $maxPriceLimit) }}</span></span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 p-6 rounded-lg border mb-8">
                        <h4 class="font-bold text-lg mb-4">Filter By Color</h4>
                        <ul class="space-y-3">
                            <li class="flex items-center">
                                <input type="checkbox" id="c1" class="hidden peer">
                                <label for="c1"
                                    class="flex items-center cursor-pointer hover:text-primary peer-checked:text-primary group">
                                    <span
                                        class="w-4 h-4 rounded-full bg-blue-500 mr-3 border border-gray-200 group-hover:shadow-md"></span>
                                    Blue
                                </label>
                            </li>
                            <li class="flex items-center">
                                <input type="checkbox" id="c2" class="hidden peer">
                                <label for="c2"
                                    class="flex items-center cursor-pointer hover:text-primary peer-checked:text-primary group">
                                    <span
                                        class="w-4 h-4 rounded-full bg-green-500 mr-3 border border-gray-200 group-hover:shadow-md"></span>
                                    Green
                                </label>
                            </li>
                            <li class="flex items-center">
                                <input type="checkbox" id="c3" class="hidden peer">
                                <label for="c3"
                                    class="flex items-center cursor-pointer hover:text-primary peer-checked:text-primary group">
                                    <span
                                        class="w-4 h-4 rounded-full bg-gray-500 mr-3 border border-gray-200 group-hover:shadow-md"></span>
                                    Gray
                                </label>
                            </li>
                            <li class="flex items-center">
                                <input type="checkbox" id="c4" class="hidden peer">
                                <label for="c4"
                                    class="flex items-center cursor-pointer hover:text-primary peer-checked:text-primary group">
                                    <span
                                        class="w-4 h-4 rounded-full bg-black mr-3 border border-gray-200 group-hover:shadow-md"></span>
                                    Black
                                </label>
                            </li>
                        </ul>
                    </div>

                    <div class="bg-gray-50 p-6 rounded-lg border">
                        <h4 class="font-bold text-lg mb-4">Tags</h4>
                        <div class="flex flex-wrap gap-2">
                            <a href="#"
                                class="px-3 py-1 bg-white border rounded text-sm hover:bg-primary hover:text-white transition">Clothing</a>
                            <a href="#"
                                class="px-3 py-1 bg-white border rounded text-sm hover:bg-primary hover:text-white transition">Furniture</a>
                            <a href="#"
                                class="px-3 py-1 bg-white border rounded text-sm hover:bg-primary hover:text-white transition">Lights</a>
                            <a href="#"
                                class="px-3 py-1 bg-white border rounded text-sm hover:bg-primary hover:text-white transition">Modern</a>
                        </div>
                    </div>
                </form>
            </aside>

            <div class="w-full lg:w-3/4 order-1 lg:order-2">
                <div
                    class="flex flex-col sm:flex-row justify-between items-center bg-white border p-4 rounded mb-8 shadow-sm">
                    <p class="text-sm mb-4 sm:mb-0">
                        Showing <span class="font-bold text-primary">{{ $products->firstItem() ?? 0 }}</span> to
                        <span class="font-bold">{{ $products->lastItem() ?? 0 }}</span>
                        of <span class="font-bold">{{ $products->total() }}</span> Results
                    </p>

                    <div class="flex items-center space-x-6">
                        <div class="flex space-x-2">
                            <button type="button" id="btn-grid"
                                class="w-8 h-8 flex items-center justify-center rounded bg-primary text-white transition">
                                <i class="fa fa-th"></i>
                            </button>
                            <button type="button" id="btn-list"
                                class="w-8 h-8 flex items-center justify-center rounded bg-gray-200 hover:bg-primary hover:text-white transition">
                                <i class="fa fa-list"></i>
                            </button>
                        </div>

                        <div class="flex items-center">
                            <label for="select-per-page" class="mr-2 text-sm font-medium">View:</label>
                            <select id="select-per-page"
                                class="border rounded p-1 text-sm focus:outline-none focus:border-primary">
                                <option value="12" {{ request('per_page') == '12' ? 'selected' : '' }}>12 products</option>
                                <option value="24" {{ request('per_page') == '24' ? 'selected' : '' }}>24 products</option>
                                <option value="48" {{ request('per_page') == '48' ? 'selected' : '' }}>48 products</option>
                            </select>
                        </div>

                        <div class="flex items-center">
                            <label for="select-sort-by" class="mr-2 text-sm font-medium">Sort By:</label>
                            <select id="select-sort-by"
                                class="border rounded p-1 text-sm focus:outline-none focus:border-primary">
                                <option value="newest" {{ request('sort_by') == 'newest' ? 'selected' : '' }}>Newest</option>
                                <option value="price_asc" {{ request('sort_by') == 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                                <option value="price_desc" {{ request('sort_by') == 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                                <option value="featured" {{ request('sort_by') == 'featured' ? 'selected' : '' }}>Featured</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Product Grid View --}}
                <div id="product-grid-view" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                    @forelse ($products as $product)
                        @php
                            $effectivePrice = $product->sale_price ?: $product->regular_price;
                            $wishlistItem = Cart::instance('wishlist')->content()->firstWhere('id', $product->id);
                            $isInWishlist = !is_null($wishlistItem);
                        @endphp
                        <div class="group">
                            <div class="relative overflow-hidden bg-gray-100 rounded-lg mb-4">
                                <a href="{{ route('shop.product.details', $product->slug) }}">
                                    <img src="{{ asset('uploads/products/thumbnails/' . $product->image) }}"
                                        alt="{{ $product->name }}"
                                        class="w-full h-[300px] object-cover transition duration-500 group-hover:scale-105" />
                                </a>

                                @if ($product->sale_price && $product->sale_price < $product->regular_price)
                                    <span class="absolute top-3 left-3 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded">
                                        -{{ round((($product->regular_price - $product->sale_price) / $product->regular_price) * 100) }}%
                                    </span>
                                @endif

                                <div
                                    class="absolute bottom-4 left-0 right-0 flex justify-center space-x-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                    
                                    {{-- Wishlist Button Form --}}
                                    @if ($isInWishlist)
                                        <form action="{{ route('wishlist.remove', $wishlistItem->rowId) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Remove from Wishlist"
                                                class="w-10 h-10 bg-white rounded-full shadow hover:bg-primary hover:text-white flex items-center justify-center transition">
                                                <i class="fa-solid fa-heart text-red-500"></i>
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('wishlist.add') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                                            <input type="hidden" name="name" value="{{ $product->name }}">
                                            <input type="hidden" name="price" value="{{ $effectivePrice }}">
                                            <button type="submit" title="Add to Wishlist"
                                                class="w-10 h-10 bg-white rounded-full shadow hover:bg-primary hover:text-white flex items-center justify-center transition">
                                                <i class="fa-regular fa-heart"></i>
                                            </button>
                                        </form>
                                    @endif

                                    {{-- Add to Cart Form --}}
                                    <form action="{{ route('cart.add') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $product->id }}">
                                        <input type="hidden" name="name" value="{{ $product->name }}">
                                        <input type="hidden" name="quantity" value="1">
                                        <input type="hidden" name="price" value="{{ $effectivePrice }}">
                                        <button type="submit" title="Add to Cart"
                                            class="w-10 h-10 bg-white rounded-full shadow hover:bg-primary hover:text-white flex items-center justify-center transition">
                                            <i class="fa-solid fa-bag-shopping"></i>
                                        </button>
                                    </form>

                                    <a href="{{ route('shop.product.details', $product->slug) }}" title="View Details"
                                        class="w-10 h-10 bg-white rounded-full shadow hover:bg-primary hover:text-white flex items-center justify-center transition">
                                        <i class="fa-solid fa-magnifying-glass"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="text-center">
                                <h4 class="text-lg font-medium hover:text-primary">
                                    <a href="{{ route('shop.product.details', $product->slug) }}">{{ $product->name }}</a>
                                </h4>
                                <div class="mb-4">
                                    <div class="flex justify-center items-center space-x-2 mt-1">
                                        @if ($product->sale_price && $product->sale_price < $product->regular_price)
                                            <span class="text-gray-400 line-through text-sm">
                                                ${{ number_format($product->regular_price, 2) }}
                                            </span>
                                        @endif
                                        <span class="text-xl text-primary font-bold">
                                            ${{ number_format($effectivePrice, 2) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-12">
                            <p class="text-gray-500 text-lg">No products found matching your criteria.</p>
                        </div>
                    @endforelse
                </div>

                {{-- Product List View --}}
                <div id="product-list-view" class="flex flex-col space-y-8 hidden">
                    @forelse ($products as $product)
                        @php
                            $effectivePrice = $product->sale_price ?: $product->regular_price;
                            $wishlistItem = Cart::instance('wishlist')->content()->firstWhere('id', $product->id);
                            $isInWishlist = !is_null($wishlistItem);
                        @endphp
                        <div class="flex flex-col md:flex-row gap-6 bg-white border rounded-lg p-4 hover:shadow-lg transition">
                            <div class="w-full md:w-1/3 relative bg-gray-100 rounded overflow-hidden">
                                <a href="{{ route('shop.product.details', $product->slug) }}">
                                    <img src="{{ asset('uploads/products/thumbnails/' . $product->image) }}" alt="{{ $product->name }}"
                                        class="w-full h-full object-cover">
                                </a>
                                @if ($product->sale_price && $product->sale_price < $product->regular_price)
                                    <span class="absolute top-3 left-3 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded">
                                        -{{ round((($product->regular_price - $product->sale_price) / $product->regular_price) * 100) }}%
                                    </span>
                                @endif
                            </div>
                            <div class="w-full md:w-2/3 flex flex-col justify-center">
                                <h4 class="text-xl font-bold hover:text-primary mb-2">
                                    <a href="{{ route('shop.product.details', $product->slug) }}">{{ $product->name }}</a>
                                </h4>
                                <div class="flex items-center space-x-2 mb-4">
                                    @if ($product->sale_price && $product->sale_price < $product->regular_price)
                                        <span class="text-gray-400 line-through text-sm">
                                            ${{ number_format($product->regular_price, 2) }}
                                        </span>
                                    @endif
                                    <span class="text-primary font-bold text-lg">
                                        ${{ number_format($effectivePrice, 2) }}
                                    </span>
                                </div>
                                <p class="text-gray-600 mb-6 text-sm leading-relaxed line-clamp-3">
                                    {{ $product->short_description ?? Str::limit(strip_tags($product->description), 120) }}
                                </p>
                                <div class="flex items-center space-x-2">
                                    {{-- Wishlist Button --}}
                                    @if ($isInWishlist)
                                        <form action="{{ route('wishlist.remove', $wishlistItem->rowId) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Remove from Wishlist"
                                                class="px-4 py-2 border rounded hover:bg-primary hover:text-white transition">
                                                <i class="fa-solid fa-heart text-red-500"></i>
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('wishlist.add') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                                            <input type="hidden" name="name" value="{{ $product->name }}">
                                            <input type="hidden" name="price" value="{{ $effectivePrice }}">
                                            <button type="submit" title="Add to Wishlist"
                                                class="px-4 py-2 border rounded hover:bg-primary hover:text-white transition">
                                                <i class="fa-regular fa-heart"></i>
                                            </button>
                                        </form>
                                    @endif

                                    {{-- Add to Cart --}}
                                    <form action="{{ route('cart.add') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $product->id }}">
                                        <input type="hidden" name="name" value="{{ $product->name }}">
                                        <input type="hidden" name="quantity" value="1">
                                        <input type="hidden" name="price" value="{{ $effectivePrice }}">
                                        <button type="submit"
                                            class="px-4 py-2 bg-sky-700 text-white rounded hover:bg-primary transition flex items-center gap-2">
                                            <i class="fa-solid fa-bag-shopping"></i> Add to Cart
                                        </button>
                                    </form>

                                    <a href="{{ route('shop.product.details', $product->slug) }}"
                                        class="px-4 py-2 border rounded hover:bg-primary hover:text-white transition">
                                        <i class="fa-solid fa-magnifying-glass"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <p class="text-gray-500 text-lg">No products found matching your criteria.</p>
                        </div>
                    @endforelse
                </div>

                <div class="mt-12 flex justify-center">
                    {{ $products->links() }}
                </div>

            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const form = document.getElementById('form-filter');

            // View toggle (Grid / List)
            const btnGrid = document.getElementById('btn-grid');
            const btnList = document.getElementById('btn-list');
            const gridView = document.getElementById('product-grid-view');
            const listView = document.getElementById('product-list-view');

            if (btnGrid && btnList && gridView && listView) {
                btnGrid.addEventListener('click', function () {
                    gridView.classList.remove('hidden');
                    listView.classList.add('hidden');
                    btnGrid.classList.add('bg-primary', 'text-white');
                    btnGrid.classList.remove('bg-gray-200');
                    btnList.classList.remove('bg-primary', 'text-white');
                    btnList.classList.add('bg-gray-200');
                });

                btnList.addEventListener('click', function () {
                    listView.classList.remove('hidden');
                    gridView.classList.add('hidden');
                    btnList.classList.add('bg-primary', 'text-white');
                    btnList.classList.remove('bg-gray-200');
                    btnGrid.classList.remove('bg-primary', 'text-white');
                    btnGrid.classList.add('bg-gray-200');
                });
            }

            // Sync sort_by and per_page dropdowns with hidden inputs in filter form
            const selectSortBy = document.getElementById('select-sort-by');
            const selectPerPage = document.getElementById('select-per-page');
            const filterSortBy = document.getElementById('filter-sort-by');
            const filterPerPage = document.getElementById('filter-per-page');

            if (selectSortBy && filterSortBy) {
                selectSortBy.addEventListener('change', function () {
                    filterSortBy.value = this.value;
                    form.submit();
                });
            }

            if (selectPerPage && filterPerPage) {
                selectPerPage.addEventListener('change', function () {
                    filterPerPage.value = this.value;
                    form.submit();
                });
            }

            // Auto-submit filter form on checkbox change
            document.querySelectorAll('.filter-checkbox').forEach(element => {
                element.addEventListener('change', function () {
                    form.submit();
                });
            });

            // Price Range Slider
            const rangeMin = document.getElementById('range-min');
            const rangeMax = document.getElementById('range-max');
            const priceMinDisplay = document.getElementById('price-min-display');
            const priceMaxDisplay = document.getElementById('price-max-display');
            const rangeTrack = document.getElementById('range-track');

            function updateRangeTrack() {
                if (!rangeMin || !rangeMax || !rangeTrack) return;

                const minVal = parseInt(rangeMin.value) || 0;
                const maxVal = parseInt(rangeMax.value) || 0;
                const minLimit = parseInt(rangeMin.min) || 0;
                const maxLimit = parseInt(rangeMin.max) || 10000;
                const totalRange = maxLimit - minLimit;

                const minPercent = totalRange > 0 ? ((minVal - minLimit) / totalRange) * 100 : 0;
                const maxPercent = totalRange > 0 ? ((maxVal - minLimit) / totalRange) * 100 : 100;

                rangeTrack.style.left = minPercent + '%';
                rangeTrack.style.right = (100 - maxPercent) + '%';

                if (priceMinDisplay) priceMinDisplay.innerText = minVal;
                if (priceMaxDisplay) priceMaxDisplay.innerText = maxVal;
            }

            if (rangeMin && rangeMax) {
                updateRangeTrack();

                let timeout = null;

                function handlePriceInput(e) {
                    let minVal = parseInt(rangeMin.value) || 0;
                    let maxVal = parseInt(rangeMax.value) || 0;

                    if (this.id === 'range-min') {
                        if (minVal > maxVal) {
                            rangeMin.value = maxVal;
                        }
                        rangeMin.style.zIndex = '25';
                        rangeMax.style.zIndex = '20';
                    } else if (this.id === 'range-max') {
                        if (maxVal < minVal) {
                            rangeMax.value = minVal;
                        }
                        rangeMax.style.zIndex = '25';
                        rangeMin.style.zIndex = '20';
                    }

                    updateRangeTrack();

                    clearTimeout(timeout);
                    timeout = setTimeout(() => {
                        form.submit();
                    }, 500);
                }

                rangeMin.addEventListener('input', handlePriceInput);
                rangeMax.addEventListener('input', handlePriceInput);
            }

            // Toggle Brands See All / See Less
            const toggleBrandsBtn = document.getElementById('toggle-brands-btn');
            if (toggleBrandsBtn) {
                const brandExtras = document.querySelectorAll('.brand-extra');
                const brandText = document.getElementById('toggle-brands-text');
                const brandIcon = document.getElementById('toggle-brands-icon');

                // If any hidden brand is currently checked, expand the list by default
                const hasCheckedBrand = Array.from(brandExtras).some(li => li.querySelector('input:checked'));
                if (hasCheckedBrand) {
                    brandExtras.forEach(el => el.classList.remove('hidden'));
                    brandText.textContent = 'See Less';
                    brandIcon.classList.replace('fa-chevron-down', 'fa-chevron-up');
                }

                toggleBrandsBtn.addEventListener('click', function () {
                    const isHidden = brandExtras[0].classList.contains('hidden');
                    brandExtras.forEach(el => el.classList.toggle('hidden'));
                    brandText.textContent = isHidden ? 'See Less' : 'See All';
                    brandIcon.classList.toggle('fa-chevron-down', !isHidden);
                    brandIcon.classList.toggle('fa-chevron-up', isHidden);
                });
            }

            // Toggle Categories See All / See Less
            const toggleCategoriesBtn = document.getElementById('toggle-categories-btn');
            if (toggleCategoriesBtn) {
                const categoryExtras = document.querySelectorAll('.category-extra');
                const categoryText = document.getElementById('toggle-categories-text');
                const categoryIcon = document.getElementById('toggle-categories-icon');

                // If any hidden category is currently checked, expand the list by default
                const hasCheckedCategory = Array.from(categoryExtras).some(li => li.querySelector('input:checked'));
                if (hasCheckedCategory) {
                    categoryExtras.forEach(el => el.classList.remove('hidden'));
                    categoryText.textContent = 'See Less';
                    categoryIcon.classList.replace('fa-chevron-down', 'fa-chevron-up');
                }

                toggleCategoriesBtn.addEventListener('click', function () {
                    const isHidden = categoryExtras[0].classList.contains('hidden');
                    categoryExtras.forEach(el => el.classList.toggle('hidden'));
                    categoryText.textContent = isHidden ? 'See Less' : 'See All';
                    categoryIcon.classList.toggle('fa-chevron-down', !isHidden);
                    categoryIcon.classList.toggle('fa-chevron-up', isHidden);
                });
            }
        });
    </script>
</x-app-layout>