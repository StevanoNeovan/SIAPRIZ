<?php

namespace App\Providers;

use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Listeners\AktifkanPenggunaSetelahVerifikasi;
use App\Listeners\CreateCeoAccount;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Verified::class => [
            AktifkanPenggunaSetelahVerifikasi::class,
            CreateCeoAccount::class,
        ],
    ];
    
}
