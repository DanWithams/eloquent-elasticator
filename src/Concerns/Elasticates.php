<?php

namespace DanWithams\EloquentElasticator\Concerns;

use DanWithams\EloquentElasticator\QueryBuilder;

trait Elasticates
{
    public static function bootElasticates()
    {
        static::saved(function ($model) {
            $model->elasticate();
        });

        static::deleted(function ($model) {
            $model->elasticate();
        });
    }

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
