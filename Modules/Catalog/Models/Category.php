<?php

namespace Modules\Catalog\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'parent_id',
        'image_path',
        'is_active',
        'sort_order'
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }
    protected static function boot(): void
    {
        parent::boot();
        static::creating(fn($category) => $category->slug ??= Str::slug($category->name));
    }

    // Relations
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
