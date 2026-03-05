<?php

namespace Modules\Catalog\Http\Resources;

use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;

class ProductImageResource extends BaseResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'url' => $this->url,
            'alt_text' => $this->alt_text,
            'is_primary' => $this->is_primary,
            'sort_order' => $this->sort_order,
        ];
    }
}
