<?php

namespace DanWithams\EloquentElasticator\Commands;

use Illuminate\Console\Command;
use DanWithams\EloquentElasticator\Contracts\Elasticatable;

class ImportElasticDocuments extends Command
{
    protected $signature = 'elasticator:import';

    protected $description = 'Imports Eloquent models to elastic search';

    public function handle()
    {
        collect(scandir(app_path('Models')))
            ->filter((fn ($file) => strpos($file, '.php')))
            ->map(fn ($file) => 'App\Models\\' . str_replace('.php', '', $file))
            ->filter(fn ($classname) =>  collect((new \ReflectionClass($classname))
                ->getInterfaceNames())
                ->contains(Elasticatable::class)
            )
            ->each(function ($classname) {
                collect(call_user_func($classname . '::all'))
                    ->each->elasticate();
            });

        return 0;
    }
}
