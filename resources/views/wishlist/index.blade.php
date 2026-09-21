<x-app-layout>
    <div class="relative bg-sky-700 text-white h-64 flex items-center justify-center bg-cover bg-center"
        style="background-image: url('{{ asset('assets/images/page-banner.jpg') }}');">
        <div class="absolute inset-0 bg-black bg-opacity-40"></div>
        <div class="relative z-10 text-center">
            <h2 class="text-4xl font-bold mb-2">Wishlist</h2>
            <ul class="flex justify-center space-x-2 text-sm">
                <li><a href="{{ route('home.index') }}" class="hover:text-primary">Home</a></li>
                <li>/</li>
                <li class="text-primary">Wishlist</li>
            </ul>
        </div>
    </div>

    <section class="py-16">
        <div class="container mx-auto px-4">
    @if(session('success'))
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

            @if(Cart::instance('wishlist')->content()->count() == 0)
                <div id="empty-wishlist" class="text-center py-10">
                    <h2 class="text-2xl font-bold mb-4">There are no items in your wishlist</h2>
                    <img src="{{ asset('assets/images/wishlist.png') }}" alt="Empty Wishlist" class="mx-auto mb-6 max-w-xs"
                        onerror="this.src='{{ asset('assets/images/cart.png') }}'">
                    <p class="text-gray-500 mb-6">Explore our store and add items you like to your wishlist!</p>
                    <a href="{{ route('shop.index') }}"
                        class="inline-block bg-primary text-white px-6 py-2.5 rounded-lg hover:bg-blue-600 transition font-medium">Wishlist Items</a>
                </div>
            @else
                <div id="wishlist-content" class="overflow-x-auto mb-8">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-bold text-gray-800">My Wishlist ({{ Cart::instance('wishlist')->count() }})
                        </h3>
                        <form action="{{ route('wishlist.clear') }}" method="POST"
                            onsubmit="return confirm('Are you sure you want to clear your wishlist?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="text-sm text-red-600 hover:text-red-800 font-medium flex items-center gap-1">
                                <i class="fa-regular fa-trash-can"></i> Clear Wishlist
                            </button>
                        </form>
                    </div>

                    <table class="w-full wishlist-table bg-white rounded-lg shadow-sm">
                        <thead class="bg-gray-100 border-b">
                            <tr>
                                <th class="py-4 px-4 text-left font-bold text-gray-700">Image</th>
                                <th class="py-4 px-4 text-left font-bold text-gray-700">Product Information</th>
                                <th class="py-4 px-4 text-left font-bold text-gray-700">Price</th>
                                <th class="py-4 px-4 text-center font-bold text-gray-700">Add to Cart</th>
                                <th class="py-4 px-4 text-center font-bold text-gray-700">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach(Cart::instance('wishlist')->content() as $item)
                                @php
                                    $product = Cart::instance('wishlist')->model($item->rowId);
                                @endphp
                                <tr>
                                    <td class="py-4 px-4 product-thumb" data-label="Image">
                                        @if ($product)
                                            <a href="{{ route('shop.product.details', $product->slug) }}">
                                                <img src="{{ asset('uploads/products/thumbnails/' . $product->image) }}"
                                                    class="w-20 h-20 object-cover rounded" alt="{{ $item->name }}">
                                            </a>
                                        @else
                                            <img src="{{ asset('assets/images/product/product-01.jpg') }}"
                                                class="w-20 h-20 object-cover rounded" alt="{{ $item->name }}">
                                        @endif
                                    </td>
                                    <td class="py-4 px-4" data-label="Product">
                                        <h6 class="font-bold text-gray-800">
                                            @if ($product)
                                                <a href="{{ route('shop.product.details', $product->slug) }}"
                                                    class="hover:text-primary">{{ $item->name }}</a>
                                            @else
                                                <span>{{ $item->name }}</span>
                                            @endif
                                        </h6>
                                    </td>
                                    <td class="py-4 px-4 font-bold text-primary" data-label="Price">
                                        ${{ number_format($item->price, 2) }}
                                    </td>
                                    <td class="py-4 px-4 text-center" data-label="Add to Cart">
                                        <form action="{{ route('wishlist.move_to_cart', $item->rowId) }}" method="POST">
                                            @csrf
                                            <button type="submit"
                                                class="bg-sky-800 text-white px-4 py-2 rounded hover:bg-primary transition text-sm flex items-center justify-center gap-1 mx-auto">
                                                <i class="fa-solid fa-bag-shopping"></i> Add to Cart
                                            </button>
                                        </form>
                                    </td>
                                    <td class="py-4 px-4 text-center" data-label="Action">
                                        <form action="{{ route('wishlist.remove', $item->rowId) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-gray-400 hover:text-red-500 transition p-2"
                                                title="Remove item">
                                                <i class="fa-solid fa-trash-can text-lg"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </section>
</x-app-layout>