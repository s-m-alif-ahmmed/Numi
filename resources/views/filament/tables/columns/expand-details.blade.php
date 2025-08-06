<!-- resources/views/filament/tables/columns/expand-details.blade.php -->
<div x-data="{ open: false }">
    <button
        type="button"
        class="flex items-center text-primary-600 hover:underline"
        x-on:click="open = !open"
    >
        <span x-show="!open">View Details</span>
        <span x-show="open">Hide Details</span>
        <span class="ml-1" x-show="!open">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </span>
        <span class="ml-1" x-show="open">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
            </svg>
        </span>
    </button>

    <div x-show="open" x-transition class="mt-2 p-4 bg-gray-50 rounded-lg">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                @if(isset($getRecord()['keyPhoto']['originalUrl']))
                    <img
                        src="{{ $getRecord()['keyPhoto']['originalUrl'] }}"
                        alt="{{ $getRecord()['title'] }}"
                        class="w-full h-auto rounded-lg"
                    >
                @endif
            </div>
            <div>
                <h3 class="text-lg font-medium">{{ $getRecord()['title'] }}</h3>

                @if(isset($getRecord()['summary']))
                    <p class="text-sm text-gray-600 mt-2">{{ $getRecord()['summary'] }}</p>
                @endif

                <div class="mt-4 space-y-2">
                    @if(isset($getRecord()['price']))
                        <div class="flex justify-between">
                            <span class="text-sm font-medium">Price:</span>
                            <span class="text-sm">{{ number_format($getRecord()['price'], 2) }}</span>
                        </div>
                    @endif

                    @if(isset($getRecord()['vendor']['title']))
                        <div class="flex justify-between">
                            <span class="text-sm font-medium">Vendor:</span>
                            <span class="text-sm">{{ $getRecord()['vendor']['title'] }}</span>
                        </div>
                    @endif

                    @if(isset($getRecord()['location']['city']) && isset($getRecord()['location']['countryCode']))
                        <div class="flex justify-between">
                            <span class="text-sm font-medium">Location:</span>
                            <span class="text-sm">{{ $getRecord()['location']['city'] }}, {{ $getRecord()['location']['countryCode'] }}</span>
                        </div>
                    @endif
                </div>

                <div class="mt-4">
                    <x-filament::button
                        wire:click="importSelected"
                        size="sm"
                    >
                        Import This Product
                    </x-filament::button>
                </div>
            </div>
        </div>
    </div>
</div>
