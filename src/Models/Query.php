<?php
namespace DanWithams\EloquentElasticator\Models;

use Illuminate\Support\Collection;

class Query
{
    protected Collection $matches;

    public function __construct()
    {
        $this->matches = collect();
    }

    public function toArray(): array
    {
        return $this->matches->each->toArray();
    }
}
