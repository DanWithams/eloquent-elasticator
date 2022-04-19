<?php

namespace DanWithams\EloquentElasticator;

use DanWithams\EloquentElasticator\Models\Query;
use DanWithams\EloquentElasticator\Models\Field;
use DanWithams\EloquentElasticator\Models\MultiMatch;

class QueryBuilder
{
    protected string $queryString;
    protected MultiMatch $multiMatch;

    public function __construct()
    {
        $this->multiMatch = new MultiMatch();
    }

    public static function query()
    {
        return new self();
    }

    public function whereField($field, $boost = null)
    {
        $this->multiMatch->addField($field, new Field($field, $boost));
    }

    public function matches($queryString)
    {
        $this->queryString = $queryString;
    }

    public function toArray()
    {
        return [
            'query' => (new Query())
                ->addMatch($this->multiMatch)
                ->toArray(),
        ];
    }
}
