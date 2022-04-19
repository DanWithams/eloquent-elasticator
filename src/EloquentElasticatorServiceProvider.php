<?php

namespace DanWithams\EloquentElasticator;

use Spatie\LaravelPackageTools\Package;
use DanWithams\EloquentElasticator\Client;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use DanWithams\EloquentElasticator\Commands\ImportElasticDocuments;
use DanWithams\EloquentElasticator\Concerns\Client as ClientContract;

class EloquentElasticatorServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package->name('eloquent-elasticator')
            ->hasConfigFile()
            ->hasCommand(ImportElasticDocuments::class);
    }

    public function register()
    {
        parent::register();
        $this->app->bind(ClientContract::class, Client::class);
    }

    public function boot()
    {
        parent::boot();
    }
}
