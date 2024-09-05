<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Products') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="relative overflow-x-auto">
                        @if (auth()->user()->is_admin)
                            <a href="{{ route('products.create') }}" class="mb-4 rounded-md inline-flex items-center px-4 py-2 bg-black text-white" type="button">
                                ADD NEW PRODUCT
                            </a>
                        @endif

                        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3">
                                        PRODUCT NAME
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        PRICE
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        PRICE EUR
                                    </th>
                                    @if (auth()->user()->is_admin)
                                    <th></th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($products as $product)
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        {{ $product->name }}
                                    </th>
                                    <td class="px-6 py-4">
                                        {{ $product->price }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ $product->price_eur }}
                                    </td>
                                    @if (auth()->user()->is_admin)
                                    <td class="px-6 py-4">
                                        <a href="{{ route('products.edit', $product->id) }}" class="rounded-md inline-flex items-center px-4 py-2 bg-black text-white">Edit</a>
                                        <form method="POST" action="{{ route("products.destroy", $product) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <input onclick="return confirm('Are you sure?')" type="submit" value="Delete" class="cursor-pointer rounded-md inline-flex items-center px-4 py-2 bg-red-900 text-white">
                                        </form>
                                    </td>
                                    @endif
                                </tr>
                                @empty
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                    <td class="px-6 py-4">
                                        {{ __('No products found') }}
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
