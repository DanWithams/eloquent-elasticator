<?php

namespace DanWithams\EloquentElasticator\Concerns;

use DanWithams\EloquentElasticator\QueryBuilder;

trait Elasticates
{
    public function elasticate(): self
    {
        $client = app(Client::class, ['index' => $this->elasticatableAs()]);
        $body = $this->toElastic();
        if ($this->shouldElasticate()) {
            $client->index($body);
        } else {
            $client->delete($body['id']);
        }

        return $this;
    }

    public static function elastic(): QueryBuilder
    {
        return new QueryBuilder(__CLASS__);
    }
}
