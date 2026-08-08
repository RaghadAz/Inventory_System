<?php

namespace App\Filament\Admin\Resources\Sales\Pages;

use App\Filament\Admin\Resources\Sales\SaleResource;
use Filament\Resources\Pages\CreateRecord;

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
            $product = \App\Models\Product::find($item->product_id);
            if ($product) {
                $product->decrement('quantity', $item->quantity);
            }
        }

        if ($record->payment_type === 'debt' && $record->remaining_price > 0) {
            $existingDebt = \App\Models\Debt::where('sale_id', $record->id)->first();

            if (!$existingDebt) {
                \App\Models\Debt::create([
                    'person_name' => $record->customer_name,
                    'type' => 'customer',
                    'amount' => $record->remaining_price,
                    'reason' => 'Sales Invoice  : ' . $record->id,
                    'sale_id' => $record->id,
                    'is_paid' => false,
                    'notes' => 'Auto‑Generated Debt from Sales Invoice ',
                ]);
            }
        }
    }
}
