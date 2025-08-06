<!-- resources/views/filament/tables/import-button.blade.php -->
<div class="mt-4 flex justify-end">
    <x-filament::button
        wire:click="importSelected"
        wire:loading.attr="disabled"
        color="success"
        :disabled="$selectedCount === 0"
    >
        <span wire:loading.remove wire:target="importSelected">Import Selected ({{ $selectedCount }})</span>
        <span wire:loading wire:target="importSelected">Importing...</span>
    </x-filament::button>
</div>
