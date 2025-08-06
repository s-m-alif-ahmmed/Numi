<!-- resources/views/filament/modals/recipe-details.blade.php -->
<div class="p-4">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            @if(isset($product['keyPhoto']['originalUrl']))
                <img
                    src="{{ $product['keyPhoto']['originalUrl'] }}"
                    alt="{{ $product['title'] }}"
                    class="w-full h-auto rounded-lg"
                >
            @endif

            <div class="mt-4 space-y-2">
                <h3 class="text-lg font-medium">{{ $product['title'] }}</h3>

                @if(isset($product['summary']))
                    <p class="text-sm text-gray-600">{{ $product['summary'] }}</p>
                @endif

                @if(isset($product['excerpt']))
                    <div class="text-sm text-gray-600 mt-2">
                        <div class="font-medium">Description:</div>
                        <div>{{ $product['excerpt'] }}</div>
                    </div>
                @endif
            </div>
        </div>

        <div class="space-y-4">
            <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="font-medium mb-2">Recipe Details</h4>

                <div class="space-y-2">
                    @if(isset($product['price']))
                        <div class="flex justify-between">
                            <span class="text-sm font-medium">Price:</span>
                            <span class="text-sm">{{ number_format($product['price'], 2) }}</span>
                        </div>
                    @endif

                    @if(isset($product['vendor']['title']))
                        <div class="flex justify-between">
                            <span class="text-sm font-medium">Vendor:</span>
                            <span class="text-sm">{{ $product['vendor']['title'] }}</span>
                        </div>
                    @endif

                    @if(isset($product['externalId']))
                        <div class="flex justify-between">
                            <span class="text-sm font-medium">External ID:</span>
                            <span class="text-sm">{{ $product['externalId'] }}</span>
                        </div>
                    @endif

                    @if(isset($product['location']['city']) && isset($product['location']['countryCode']))
                        <div class="flex justify-between">
                            <span class="text-sm font-medium">Location:</span>
                            <span class="text-sm">{{ $product['location']['city'] }}, {{ $product['location']['countryCode'] }}</span>
                        </div>
                    @endif
                </div>
            </div>

            @if(isset($product['keywords']) && !empty($product['keywords']))
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="font-medium mb-2">Keywords</h4>
                    <div class="flex flex-wrap gap-2">
                        @foreach($product['keywords'] as $keyword)
                            <span class="px-2 py-1 bg-gray-200 rounded-full text-xs">{{ $keyword }}</span>
                        @endforeach
                    </div>
                </div>
            @endif

            @if(isset($product['customFields']) && !empty($product['customFields']))
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="font-medium mb-2">Custom Fields</h4>
                    <div class="space-y-2">
                        @foreach($product['customFields'] as $field)
                            <div class="flex justify-between">
                                <span class="text-sm font-medium">{{ $field['title'] }}:</span>
                                <span class="text-sm">{{ $field['value'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
