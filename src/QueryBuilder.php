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

    public function whereField($field, $boost = null): self
    {
        $this->multiMatch->addField($field, new Field($field, $boost));

        return $this;
    }

    public function matches($queryString): self
    {
        $this->queryString = $queryString;

        return $this;
    }

    public function get()
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
