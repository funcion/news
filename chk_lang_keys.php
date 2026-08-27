<?php
$en = require '/app/lang/en/ui.php';
$es = require '/app/lang/es/ui.php';

echo "=== LANG KEYS CHECK ===\n";
echo "categories_meta_desc in EN: " . (isset($en['categories_meta_desc']) ? 'FOUND: ' . $en['categories_meta_desc'] : 'MISSING') . "\n";
echo "categories_meta_desc in ES: " . (isset($es['categories_meta_desc']) ? 'FOUND: ' . $es['categories_meta_desc'] : 'MISSING') . "\n";

echo "categories_title in EN: " . (isset($en['categories_title']) ? 'FOUND: ' . $en['categories_title'] : 'MISSING') . "\n";
echo "categories_title in ES: " . (isset($es['categories_title']) ? 'FOUND: ' . $es['categories_title'] : 'MISSING') . "\n";

echo "about_meta_desc in EN: " . (isset($en['about_meta_desc']) ? 'FOUND: ' . $en['about_meta_desc'] : 'MISSING') . "\n";
echo "about_meta_desc in ES: " . (isset($es['about_meta_desc']) ? 'FOUND: ' . $es['about_meta_desc'] : 'MISSING') . "\n";

echo "contact_meta_desc in EN: " . (isset($en['contact_meta_desc']) ? 'FOUND: ' . $en['contact_meta_desc'] : 'MISSING') . "\n";
echo "contact_meta_desc in ES: " . (isset($es['contact_meta_desc']) ? 'FOUND: ' . $es['contact_meta_desc'] : 'MISSING') . "\n";

echo "tag_meta_desc in EN: " . (isset($en['tag_meta_desc']) ? 'FOUND: ' . $en['tag_meta_desc'] : 'MISSING') . "\n";
echo "tag_meta_desc in ES: " . (isset($es['tag_meta_desc']) ? 'FOUND: ' . $es['tag_meta_desc'] : 'MISSING') . "\n";

echo "category_meta_desc in EN: " . (isset($en['category_meta_desc']) ? 'FOUND: ' . $en['category_meta_desc'] : 'MISSING') . "\n";
echo "category_meta_desc in ES: " . (isset($es['category_meta_desc']) ? 'FOUND: ' . $es['category_meta_desc'] : 'MISSING') . "\n";