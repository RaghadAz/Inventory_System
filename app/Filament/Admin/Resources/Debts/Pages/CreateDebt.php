<?php

namespace App\Filament\Admin\Resources\Debts\Pages;

use App\Filament\Admin\Resources\Debts\DebtResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDebt extends CreateRecord
{
    protected static string $resource = DebtResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    }
