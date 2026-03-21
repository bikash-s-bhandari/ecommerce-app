<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Admin\Queries\UserStatsQuery;
use Modules\Auth\Models\User;

uses(RefreshDatabase::class);

it('counts total users', function () {
    User::factory()->count(4)->create();

    $stats = (new UserStatsQuery)->get();

    expect((int) $stats->total)->toBe(4);
});

it('counts users registered this week', function () {
    User::factory()->count(3)->create(['created_at' => now()->startOfWeek()->addHour()]);
    User::factory()->count(2)->create(['created_at' => now()->startOfWeek()->subDay()]);

    $stats = (new UserStatsQuery)->get();

    expect((int) $stats->total)->toBe(5)
        ->and((int) $stats->new_week)->toBe(3);
});

it('returns zero when no users exist', function () {
    $stats = (new UserStatsQuery)->get();

    expect((int) $stats->total)->toBe(0)
        ->and((int) $stats->new_week)->toBe(0);
});

it('counts all users registered exactly at week start as new', function () {
    User::factory()->create(['created_at' => now()->startOfWeek()]);

    $stats = (new UserStatsQuery)->get();

    expect((int) $stats->new_week)->toBe(1);
});
