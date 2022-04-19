<?php

namespace DanWithams\EloquentElasticator\Commands;

use Illuminate\Console\Command;

class ImportElasticDocuments extends Command
{
    protected $signature = 'elasticator:import';

    protected $description = 'Imports Eloquent models to elastic search';

    public function handle()
    {
        $this->info('Test ImportElasticDocuments');

        return 0;
    }
}
