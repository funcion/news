<?php

use App\Models\Source;

$source = Source::where('slug', 'jina')->first();
if ($source) {
    if ($source->type !== 'scraping') {
        $source->type = 'scraping';
        $source->save();
        echo "Source 'jina' updated to 'scraping' type.\n";
    } else {
        echo "Source 'jina' already has 'scraping' type.\n";
    }
} else {
    echo "Source 'jina' not found.\n";
}
