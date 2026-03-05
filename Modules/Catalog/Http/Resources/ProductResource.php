<?php

namespace Modules\Catalog\Http\Resources;

use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;

class ProductResource extends BaseResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'sku' => $this->sku,
            'price' => (float) $this->price,
            'sale_price' => $this->sale_price ? (float) $this->sale_price : null,
            'effective_price' => (float) $this->effectivePrice(),
            'stock' => $this->stock,
            'in_stock' => $this->isInStock(),
            'is_low_stock' => $this->isLowStock(),
            'featured' => $this->featured,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'category' => CategoryResource::make($this->whenLoaded('category')),//returns category model if loaded (eager loaded), else null
            'images' => ProductImageResource::collection($this->whenLoaded('images')),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}
