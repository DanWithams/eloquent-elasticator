<?php

namespace DanWithams\EloquentElasticator\Concerns;

use DanWithams\EloquentElasticator\QueryBuilder;
use DanWithams\EloquentElasticator\Concerns\Client;

trait Elasticates
{
    public function elasticate(): self
    {
        if ($this->shouldElasticate()) {
            $index = $this->elasticatableAs();
            $body = $this->toElastic();
            $client = app(Client::class, ['index' => $index]);
            $client->index($body);
        }

        return $this;
    }

    public static function elastic()
    {
        return new QueryBuilder(__CLASS__);
    }
}
