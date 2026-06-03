<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-home';

    public function getHeading(): string
    {
        return 'Dashboard — Glodaxia';
    }

    public function getSubheading(): string | null
    {
        return 'Panel de administración editorial';
    }

    public function getColumns(): int | array
    {
        return 2;
    }
}