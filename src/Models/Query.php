<?php
namespace DanWithams\EloquentElasticator\Models;

use Illuminate\Support\Collection;
use DanWithams\EloquentElasticator\Models\Contracts\MatchCriteria;

class Query
{
    protected Collection $matches;

    public function __construct()
    {
        $this->matches = collect();
    }

    public function addMatch(MatchCriteria $match): self
    {
        $this->matches->push($match);

        return $this;
    }

    public function toArray(): array
    {
        return $this->matches->each->toArray();
    }
}
