<?php

namespace App\Filament\Admin\Resources\Sales\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SaleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('product_id')->required()->numeric(),
                TextInput::make('user_id')->required()->numeric(),
                TextInput::make('quantity')->required()->numeric(),
                TextInput::make('total_price')->required()->numeric()->prefix('$'),
                DateTimePicker::make('sale_date')->required(),
            ]);
    }
}