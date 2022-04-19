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
        $this->multiMatch->addField(new Field($field, $boost));

        return $this;
    }

    public function fuzzy($fuzziness = 'AUTO'): self
    {
        $this->multiMatch->setFuzziness($fuzziness);

        return $this;
    }

    public function matches($queryString): self
    {
        $this->multiMatch->setQueryString($queryString);

        return $this;
    }

    public function get()
    {
        $client = app(Client::class, ['index' => $this->index]);

        $documents = $client->query([
            'query' => (new Query())
                ->setMatch($this->multiMatch)
                ->toArray(),
        ]);

        $ids = collect(data_get($documents, 'hits.hits'))
            ->pluck('_id')
            ->all();

        return call_user_func($this->model . '::query')
            ->whereIn('id', $ids)
            ->get();
    }
}
