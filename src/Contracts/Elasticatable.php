<?php

namespace Danwithams\EloquentElasticator\Contracts;

interface Elasticatable
{
    public function shouldElasticate(): bool;

    public function toElastic(): array;

    public function elasticatableAs(): string;
}
