<?php
namespace DanWithams\EloquentElasticator\Models;

use DanWithams\EloquentElasticator\Models\Contracts\QueryCriteria;

class Query
{
    protected QueryCriteria $match;

    public function __construct()
    {
    }

    public function setMatch(QueryCriteria $match): self
    {
        $this->match = $match;

        return $this;
    }

    public function toArray(): array
    {
        return $this->match->toArray();
    }
}
