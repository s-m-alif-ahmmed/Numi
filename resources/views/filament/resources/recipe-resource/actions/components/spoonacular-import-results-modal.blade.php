{{-- resources/views/filament/resources/recipe-resource/actions/components/spoonacular-import-results-modal.blade.php --}}

<x-filament::modal id="bokun-import-results" width="7xl">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold">
                Bokun Activity Results
            </h2>

            <div class="flex items-center space-x-2">
                <span class="text-sm text-gray-500">
                    {{ $selectedCount }} of {{ $totalCount }} selected
                </span>

                <x-filament::button
                    color="gray"
                    size="sm"
                    wire:click="selectAll"
                >
                    Select All
                </x-filament::button>

                <x-filament::button
                    color="gray"
                    size="sm"
                    wire:click="deselectAll"
                >
                    Deselect All
                </x-filament::button>
            </div>
        </div>
    </x-slot>

    <div class="space-y-4">
        <div class="border rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Select
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Image
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Title
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Category
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        preparation_time
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        cooking_time
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        total_ready_time
                    </th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                @forelse($searchResults as $activity)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input
                                type="checkbox"
                                wire:model.live="selectedActivities.{{ $activity['recipe_api_id'] }}"
                                class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50"
                            />
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if(isset($activity['image_url']))
                                <img src="{{ $activity['image_url'] }}" class="h-10 w-10 rounded-full object-cover" alt="{{ $activity['title'] }}" />
                            @else
                                <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                    <span class="text-gray-500">N/A</span>
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $activity['title'] }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500">
                                {{ $activity['category'] ?? 'Unknown' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                ${{ number_format($activity['preparation_time'] ?? 0) }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                ${{ number_format($activity['cooking_time'] ?? 0) }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                ${{ number_format($activity['total_ready_time'] ?? 0) }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="https://bokun.io/product/{{ $activity['id'] }}" target="_blank" class="text-primary-600 hover:text-primary-900">
                                View Details
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                            No activities found
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <x-slot name="footer">
        <div class="flex justify-end gap-x-3">
            <x-filament::button
                color="gray"
                x-on:click="close"
            >
                Cancel
            </x-filament::button>

            <x-filament::button
                wire:click="import"
                :disabled="count($selectedActivities) === 0"
            >
                Import Selected ({{ count($selectedActivities) }})
            </x-filament::button>
        </div>
    </x-slot>
</x-filament::modal>
