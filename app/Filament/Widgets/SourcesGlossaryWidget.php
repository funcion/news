<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class SourcesGlossaryWidget extends Widget
{
    protected string $view = 'filament.widgets.sources-glossary-widget';

    protected int | string | array $columnSpan = 'full';
}