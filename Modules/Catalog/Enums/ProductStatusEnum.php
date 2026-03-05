<?php

namespace Modules\Catalog\Enums;

enum ProductStatusEnum: string
{
    case DRAFT = 'draft';
    case ACTIVE = 'active';
    case OUT_OF_STOCK = 'out_of_stock';
    case DISCONTINUED = 'discontinued';

    public function isVisible(): bool
    {
        return in_array($this, [self::ACTIVE, self::OUT_OF_STOCK]);
    }
    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::ACTIVE => 'Active',
            self::OUT_OF_STOCK => 'Out of Stock',
            self::DISCONTINUED => 'Discontinued',
        };
    }
}
