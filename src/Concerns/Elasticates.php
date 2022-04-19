<?php

namespace DanWithams\EloquentElasticator\Concerns;

use DanWithams\EloquentElasticator\Client;

trait Elasticates
{
    public function elasticate()
    {
        if ($this->shouldElasticate()) {
            $index = $this->elasticatableAs();
            $body = $this->toElastic();
            $client = app(Client::class, ['index' => $index]);
            $client->index($body);
        }
    }
}
