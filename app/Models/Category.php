<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Translatable\HasTranslations;

class Category extends Model
{
    use HasFactory, HasTranslations;

    public array $translatable = ['name', 'description'];

    protected $fillable = [
        'name',
        'slug_en',
        'slug_es',
        'description',
        'parent_id',
        'order',
        'is_active',
        'metadata',
    ];

    protected $casts = [
        'order' => 'integer',
        'is_active' => 'boolean',
        'metadata' => 'array',
    ];

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function articles()
    {
        return $this->hasMany(Article::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeWithParent($query, $parentId)
    {
        return $query->where('parent_id', $parentId);
    }

    /**
     * Get the locale-aware URL for the category.
     */
    public function getUrlAttribute(): string
    {
        $locale = app()->getLocale();
        $slug = $locale === 'es' ? ($this->slug_es ?? $this->slug_en) : ($this->slug_en ?? $this->slug_es);
        return route('categories.show', ['slug' => $slug]);
    }

    public function getBreadcrumbAttribute(): array
    {
        $breadcrumb = [];
        $category = $this;

        while ($category) {
            $breadcrumb[] = [
                'name' => $category->name, // will use current locale via HasTranslations
                'url' => $category->url,
            ];
            $category = $category->parent;
        }

        return array_reverse($breadcrumb);
    }

    public function getAllDescendants()
    {
        $descendants = collect();

        foreach ($this->children as $child) {
            $descendants->push($child);
            $descendants = $descendants->merge($child->getAllDescendants());
        }

        return $descendants;
    }

    public function getAllArticles()
    {
        $categoryIds = $this->getAllDescendants()->pluck('id')->push($this->id);
        return Article::whereIn('category_id', $categoryIds)->published();
    }
}