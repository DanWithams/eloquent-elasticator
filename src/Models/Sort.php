<?php
namespace DanWithams\EloquentElasticator\Models;

use Illuminate\Support\Collection;

class Sort
{
    protected Collection $sorts;

    public function __construct()
    {
        $this->sorts = collect();
    }

    public function addSort(SortItem $sort): self
    {
        $this->sorts->push($sort);

        return $this;
    }

    public function count(): int
    {
        return $this->sorts->count();
    }

    public function toArray(): array
    {
        return $this->sorts->map->toArray();
    }
}
