<?php

namespace Modules\Admin\Queries;

use Modules\Auth\Models\User;

class UserStatsQuery
{
    public function get(): object
    {
        return User::toBase()
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('COUNT(CASE WHEN created_at >= ? THEN 1 END) as new_week', [now()->startOfWeek()])
            ->first();
    }
}
