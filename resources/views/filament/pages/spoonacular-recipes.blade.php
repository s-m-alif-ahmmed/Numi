<x-filament::page>
    @php($products = $this->getProductsData())
    @if (!empty($searchResults) && isset($searchResults['items']) && count($searchResults['items']) > 0)
        <div class="mb-4 flex justify-between items-center">
            <div>
                <span class="text-gray-600">{{ count($searchResults['items']) }} results found</span>
            </div>
            <div>
                <x-filament::button
                    wire:click="importSelected"
                    wire:loading.attr="disabled"
                    color="success"
                    :disabled="empty($selectedItems)"
                >
                    <span wire:loading.remove wire:target="importSelected">Import Selected ({{ count($selectedItems) }})</span>
                    <span wire:loading wire:target="importSelected">Importing...</span>
                </x-filament::button>
            </div>
        </div>

        <!-- Custom Data Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-4 py-3 flex justify-between items-center border-b border-gray-200">
                <div class="flex items-center">
                    <span class="text-sm text-gray-700 mr-3">Per Page:</span>
                    <select
                        wire:model.live="perPage"
                        class="rounded-md border-gray-300 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50 text-sm"
                    >
                        @foreach($perPageOptions as $option)
                            <option value="{{ $option }}">{{ $option }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <span class="text-sm text-gray-700">
                        {{ $products->firstItem() ?? 0 }} - {{ $products->lastItem() ?? 0 }} of {{ $products->total() }}
                    </span>
                </div>
            </div>

            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <input
                            type="checkbox"
                            wire:key="all-visible-select"
                            class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50"
                            wire:click="selectAllVisible"
                            @if($this->areAllVisibleSelected()) checked @endif
                        >
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Image
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Title
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Vendor
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Price
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" x-data="{ openItem: null }">
                @foreach($products as $product)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input
                                type="checkbox"
                                wire:key="{{ $product['id'] }}"
                                class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50"
                                wire:click="toggleSelection('{{ $product['id'] }}')"
                                @if(in_array($product['id'], $selectedItems)) checked @endif
                            >
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if(isset($product['keyPhoto']['originalUrl']))
                                <img
                                    src="{{ $product['keyPhoto']['originalUrl'] }}"
                                    alt="{{ $product['title'] }}"
                                    class="h-10 w-10 rounded-full object-cover"
                                >
                            @else
                                <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                    <span class="text-gray-500 text-xs">No img</span>
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $product['title'] }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $product['vendor']['title'] ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                @if(isset($product['price']))
                                    {{ number_format($product['price'], 2) }}
                                @else
                                    N/A
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button
                                type="button"
                                class="text-primary-600 hover:text-primary-900"
                                @click="openItem = (openItem === '{{ $product['id'] }}') ? null : '{{ $product['id'] }}'"
                            >
                                <span x-show="openItem !== '{{ $product['id'] }}'">View Details</span>
                                <span x-show="openItem === '{{ $product['id'] }}'">Hide Details</span>
                            </button>
                        </td>
                    </tr>
                    <!-- Product Details Row -->
                    <tr x-show="openItem === '{{ $product['id'] }}'" x-cloak x-transition class="bg-gray-50">
                        <td colspan="6" class="px-6 py-4">
                            <div class="max-w-full overflow-hidden">
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                    <div class="flex justify-center">
                                        @if(isset($product['keyPhoto']['originalUrl']))
                                            <img
                                                src="{{ $product['keyPhoto']['originalUrl'] }}"
                                                alt="{{ $product['title'] }}"
                                                class="max-w-full h-auto rounded-lg object-cover max-h-64"
                                            >
                                        @else
                                            <div class="w-full h-48 bg-gray-200 rounded-lg flex items-center justify-center">
                                                <span class="text-gray-500">No image available</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="space-y-4">
                                        <h3 class="text-lg font-medium text-gray-900">{{ $product['title'] }}</h3>

                                        @if(isset($product['summary']))
                                            <div>
                                                <h4 class="text-sm font-medium text-gray-700 mb-1">Description</h4>
                                                <p class="text-sm text-gray-600">{{ Str::limit($product['summary'], 200) }}</p>
                                            </div>
                                        @endif

                                        <div class="grid grid-cols-2 gap-4">
                                            @if(isset($product['price']))
                                                <div>
                                                    <h4 class="text-sm font-medium text-gray-700">Price</h4>
                                                    <p class="text-sm text-gray-900">${{ number_format($product['price'], 2) }}</p>
                                                </div>
                                            @endif

                                            @if(isset($product['vendor']['title']))
                                                <div>
                                                    <h4 class="text-sm font-medium text-gray-700">Vendor</h4>
                                                    <p class="text-sm text-gray-900">{{ $product['vendor']['title'] }}</p>
                                                </div>
                                            @endif

                                            @if(isset($product['googlePlace']['city']) && isset($product['googlePlace']['countryCode']))
                                                <div>
                                                    <h4 class="text-sm font-medium text-gray-700">Location</h4>
                                                    <p class="text-sm text-gray-900">{{ $product['googlePlace']['city'] }}, {{ $product['googlePlace']['countryCode'] }}</p>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="pt-2">
                                            <x-filament::button
                                                wire:click="importSingle('{{ $product['id'] }}')"
                                                wire:loading.attr="disabled"
                                                wire:target="importSingle('{{ $product['id'] }}')"
                                                size="sm"
                                                color="primary"
                                            >
                                                <span wire:loading.remove wire:target="importSingle('{{ $product['id'] }}')">Import This Product</span>
                                                <span wire:loading wire:target="importSingle('{{ $product['id'] }}')">Importing...</span>
                                            </x-filament::button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Showing
                            <span class="font-medium">{{ $products->firstItem() ?? 0 }}</span>
                            to
                            <span class="font-medium">{{ $products->lastItem() ?? 0 }}</span>
                            of
                            <span class="font-medium">{{ $products->total() }}</span>
                            results
                        </p>
                    </div>
                    <div>
                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                            @if ($products->onFirstPage())
                                <span class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-300">
                                    <span class="sr-only">Previous</span>
                                    <!-- Heroicon name: solid/chevron-left -->
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </span>
                            @else
                                <button wire:click="previousPage" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                    <span class="sr-only">Previous</span>
                                    <!-- Heroicon name: solid/chevron-left -->
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            @endif

                            <!-- Current: "z-10 bg-indigo-50 border-indigo-500 text-indigo-600", Default: "bg-white border-gray-300 text-gray-500 hover:bg-gray-50" -->
                            @for ($i = 1; $i <= $products->lastPage(); $i++)
                                @if ($i == $products->currentPage())
                                    <span class="relative inline-flex items-center px-4 py-2 border border-primary-500 bg-primary-50 text-sm font-medium text-primary-600">
                                        {{ $i }}
                                    </span>
                                @else
                                    <button wire:click="goToPage({{ $i }})" class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                        {{ $i }}
                                    </button>
                                @endif
                            @endfor

                            @if ($products->hasMorePages())
                                <button wire:click="nextPage" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                    <span class="sr-only">Next</span>
                                    <!-- Heroicon name: solid/chevron-right -->
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10l-3.293-3.293a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            @else
                                <span class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-300">
                                    <span class="sr-only">Next</span>
                                    <!-- Heroicon name: solid/chevron-right -->
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10l-3.293-3.293a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </span>
                            @endif
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4 flex justify-end">
            <x-filament::button
                wire:click="importSelected"
                wire:loading.attr="disabled"
                color="success"
                :disabled="empty($selectedItems)"
            >
                <span wire:loading.remove wire:target="importSelected">Import Selected ({{ count($selectedItems) }})</span>
                <span wire:loading wire:target="importSelected">Importing...</span>
            </x-filament::button>
        </div>
    @elseif (!empty($searchResults) && isset($searchResults['items']) && count($searchResults['items']) === 0)
        <div class="flex items-center justify-center min-h-screen bg-gray-100">
            <div class="bg-white rounded-lg shadow-md p-12 text-center max-w-lg w-full">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <h2 class="text-xl font-semibold text-gray-800 mb-2">No Results Found</h2>
                <p class="text-gray-600">We couldn't find any products matching your search. Please try different keywords.</p>
            </div>
        </div>
    @else
        <div class="flex items-center justify-center min-h-screen bg-gray-100">
            <div class="bg-white rounded-lg shadow-md p-12 text-center max-w-lg w-full">
                <svg class="w-16 h-16 mx-auto text-blue-500 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122" />
                </svg>
                <h2 class="text-xl font-semibold text-gray-800 mb-2">Find Products</h2>
                <p class="text-gray-600">Search for products from the Spoonacular API.</p>
            </div>
        </div>
    @endif
</x-filament::page>
