<?php

namespace DanWithams\EloquentElasticator;

use DanWithams\EloquentElasticator\Models\Query;
use DanWithams\EloquentElasticator\Models\Field;
use DanWithams\EloquentElasticator\Concerns\Client;
use DanWithams\EloquentElasticator\Models\MultiMatch;

class QueryBuilder
{
    protected string $index;
    protected string $queryString;
    protected MultiMatch $multiMatch;

    public function __construct(protected string $model)
    {
        $this->index = (new $this->model)->elasticatableAs();
        $this->multiMatch = new MultiMatch();
    }

    public function whereField($field, $boost = null)
    {
        $this->multiMatch->addField($field, new Field($field, $boost));
    }

    public function matches($queryString)
    {
        $this->queryString = $queryString;
    }

    public function get()
    {

    }

    public function toArray()
    {
        $client = app(Client::class, ['index' => $this->index]);

        $documents = $client->query([
            'query' => (new Query())
                ->addMatch($this->multiMatch)
                ->toArray(),
        ]);

        dd($documents);
    }
}
