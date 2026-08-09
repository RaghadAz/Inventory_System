<?php

namespace App\Filament\Admin\Resources\Sales\Pages;

use App\Filament\Admin\Resources\Sales\SaleResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Product;
use App\Models\Debt;
use App\Models\StockMovement;

class CreateSale extends CreateRecord
{
    protected static string $resource = SaleResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        $data['sale_date'] = now();
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['user_id'] = auth()->id();
        $data['sale_date'] = now();
        return $data;
    }

    protected function afterCreate(): void
    {
        $record = $this->getRecord();

        foreach ($record->saleItems as $item) {
            $product = Product::find($item->product_id);
            if ($product) {
                $product->decrement('quantity', $item->quantity);

                StockMovement::create([
                    'product_id' => $product->id,
                    'user_id'    => auth()->id() ?? $record->user_id,
                    'type'       => 'out',
                    'quantity'   => $item->quantity, 
                    'date'       => now(),
                ]);
            }
        }


        if ($record->payment_type === 'debt' && $record->remaining_price > 0) {
            $existingDebt = Debt::where('sale_id', $record->id)->first();

            if (!$existingDebt) {
                Debt::create([
                    'person_name' => $record->customer_name,
                    'type'        => 'customer',
                    'amount'      => $record->remaining_price,
                    'reason'      => 'Sales Invoice : ' . $record->id,
                    'sale_id'     => $record->id,
                    'user_id'     => auth()->id(),
                    'is_paid'     => false,
                    'notes'       => 'Auto-Generated Debt from Sales Invoice',
                ]);
            }
        }
    }
}
