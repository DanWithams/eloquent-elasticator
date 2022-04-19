<?php
namespace DanWithams\EloquentElasticator\Models;

use DanWithams\EloquentElasticator\Models\Contracts\MatchCriteria;

class Query
{
    protected MatchCriteria $match;

    public function __construct()
    {
    }

    public function setMatch(MatchCriteria $match): self
    {
        $this->matches = $match;

        return $this;
    }

    public function toArray(): array
    {
        return $this->match->toArray();
    }
}
