<?php

namespace App\Filament\Admin\Resources\Debts\Pages;

use App\Filament\Admin\Resources\Debts\DebtResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateDebt extends CreateRecord
{
    protected static string $resource = DebtResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id();

        return $data;
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
