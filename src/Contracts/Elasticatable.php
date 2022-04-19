<?php

namespace Danwithams\EloquentElasticator;

interface Elasticatable
{
    public function shouldElasticate(): bool;

    public function toElastic(): array;

    public function elasticatableAs(): string;

}
