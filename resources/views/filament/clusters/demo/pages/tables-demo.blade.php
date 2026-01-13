<x-filament::page>
    {{-- Editable Table Section --}}
    <x-filament::section>
        <x-slot name="heading">
            Data Table with Editable Columns
        </x-slot>
        <x-slot name="description">
            This table demonstrates inline editing with TextInputColumn, SelectColumn, and ToggleColumn.
            Changes are saved immediately when you edit a cell.
        </x-slot>

        {{ $this->table }}
    </x-filament::section>

    {{-- Invoice Repeater Section --}}
    <x-filament::section>
        <x-slot name="heading">
            Invoice-Style Repeater
        </x-slot>
        <x-slot name="description">
            A table-layout repeater for managing line items with automatic calculations.
        </x-slot>

        <form wire:submit.prevent>
            {{ $this->invoiceForm }}

            {{-- Invoice Totals --}}
            <div class="mt-6 border-t border-gray-200 dark:border-gray-700 pt-6">
                <div class="flex justify-end">
                    <div class="w-full max-w-xs space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">Subtotal</span>
                            <span class="font-medium text-gray-900 dark:text-white">
                                ${{ number_format($this->getSubtotal(), 2) }}
                            </span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">Tax (10%)</span>
                            <span class="font-medium text-gray-900 dark:text-white">
                                ${{ number_format($this->getTax(), 2) }}
                            </span>
                        </div>
                        <div
                            class="flex justify-between text-base font-semibold border-t border-gray-200 dark:border-gray-700 pt-3">
                            <span class="text-gray-900 dark:text-white">Grand Total</span>
                            <span class="text-primary-600 dark:text-primary-400">
                                ${{ number_format($this->getGrandTotal(), 2) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </x-filament::section>
</x-filament::page>
