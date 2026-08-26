<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class RawArticlesGlossaryWidget extends Widget
{
    protected string $view = 'filament.widgets.raw-articles-glossary-widget';

    protected int | string | array $columnSpan = 'full';
}