<?php

namespace Danwithams\EloquentElasticator\Commands;

use Illuminate\Console\Command;

class ImportElasticDocuments extends Command
{
    protected $signature = 'elasticator:import';

    protected $description = 'Command description';

    public function handle()
    {
        $this->info('Test ImportElasticDocuments');

        return 0;
    }
}
