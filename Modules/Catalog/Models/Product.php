<?php

namespace Modules\Catalog\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Catalog\Enums\ProductStatusEnum;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany, BelongsToMany};
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'sku',
        'price',
        'sale_price',
        'stock',
        'low_stock_threshold',
        'category_id',
        'status',
        'featured',
    ];
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'featured' => 'boolean',
            'status' => ProductStatusEnum::class,
        ];
    }

    protected static function boot(): void
    {
        parent::boot();
        static::creating(fn($product) => $product->slug ??= Str::slug($product->title));
    }

    // Relations
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }
    public function primaryImage(): HasMany
    {
        return $this->hasMany(ProductImage::class)->where('is_primary', true);
    }
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(
            Tag::class,
            'product_tag'
        );
    }
    // Helpers
    //Effective price = actual price customer will pay.
    //Effective price = sale price bhaye sale price, nabhaye original price
    public function effectivePrice(): float
    {
        return $this->sale_price ?? $this->price;
    }

    public function isInStock(): bool
    {
        return $this->stock > 0;
    }
    public function isLowStock(): bool
    {
        return $this->stock > 0 && $this->stock <= $this->low_stock_threshold;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where(
            'status',
            ProductStatusEnum::ACTIVE
        );
    }
    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    //E-commerce ma user lai dekhaune product = ACTIVE ya OUT_OF_STOCK.
    public function scopeVisible($query)
    {
        return $query->whereIn('status', [
            ProductStatusEnum::ACTIVE->value,
            ProductStatusEnum::OUT_OF_STOCK->value
        ]);
    }
}
