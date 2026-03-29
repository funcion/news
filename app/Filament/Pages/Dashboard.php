<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    
    
    public function getHeading(): string
    {
        return 'Dashboard - Noticias Platform';
    }
    
    public function getSubheading(): string | null
    {
        return 'Panel de administración de la plataforma de noticias automatizada';
    }
}