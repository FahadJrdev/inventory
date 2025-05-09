<?php

namespace App\Filament\Imports;

use App\Models\Product;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class ProductImporter extends Importer
{
    protected static ?string $model = Product::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('product_name')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('internal_reference')
                ->requiredMapping()
                ->rules(['max:255']),
            ImportColumn::make('ean_gtin_code')
                ->rules(['max:255']),
            ImportColumn::make('rfid_code')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('product_picture')
                ->rules(['max:255']),
            ImportColumn::make('brief_description'),
            ImportColumn::make('current_stock')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('product_condition')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('cost_price')
                ->numeric()
                ->rules(['nullable', 'numeric', 'decimal:0,2'])
                ->castStateUsing(function ($state) {
                    return $state !== null ? round((float) $state, 2) : null;
                }),

            ImportColumn::make('sale_price')
                ->numeric()
                ->rules(['nullable', 'numeric', 'decimal:0,2'])
                ->castStateUsing(function ($state) {
                    return $state !== null ? round((float) $state, 2) : null;
                }),
            ImportColumn::make('date_of_discharge')
                ->rules(['datetime']),
            ImportColumn::make('last_updated_date')
                ->rules(['datetime']),
            ImportColumn::make('creator_user')
                ->rules(['max:255']),
            ImportColumn::make('category')
                ->requiredMapping()
                ->relationship(),
            ImportColumn::make('brand')
                ->requiredMapping()
                ->relationship(),
            ImportColumn::make('supplier')
                ->requiredMapping()
                ->relationship(),
            ImportColumn::make('warehouseLocation')
                ->requiredMapping()
                ->relationship(),
        ];
    }

    public function resolveRecord(): ?Product
    {
        // Try to find existing product by RFID first
        if (!empty($this->data['rfid_code'])) {
            $existing = Product::where('rfid_code', $this->data['rfid_code'])->first();
            if ($existing) {
                return $existing;
            }
        }
        
        // Then try by internal reference if RFID not found
        if (!empty($this->data['internal_reference'])) {
            $existing = Product::where('internal_reference', $this->data['internal_reference'])->first();
            if ($existing) {
                return $existing;
            }
        }

        return new Product();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your product import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
