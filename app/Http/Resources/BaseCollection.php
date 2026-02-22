<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

abstract class BaseCollection extends ResourceCollection
{
    public function with($request): array
    {
        return ['status' => 'success'];
    }
}
