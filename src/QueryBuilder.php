<?php

namespace DanWithams\EloquentElasticator;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Traits\ForwardsCalls;
use DanWithams\EloquentElasticator\Models\Sort;
use DanWithams\EloquentElasticator\Models\Query;
use DanWithams\EloquentElasticator\Models\Field;
use DanWithams\EloquentElasticator\Concerns\Client;
use DanWithams\EloquentElasticator\Models\SortItem;
use DanWithams\EloquentElasticator\Models\MultiMatch;
use DanWithams\EloquentElasticator\Models\Contracts\QueryCriteria;

class QueryBuilder
{
    use ForwardsCalls;

    protected string $index;
    protected Builder $query;
    protected QueryCriteria $criteria;
    protected Sort $sort;

    public function __construct(protected string $model)
    {
        $this->criteria = new MultiMatch();
        $this->sort = new Sort();
        $this->index = (new $this->model)->elasticatableAs();
        $this->query = call_user_func($this->model . '::query');
    }

    public function __call($name, $arguments)
    {
        $this->forwardCallTo($this->query, $name, $arguments);

        return $this;
    }

    public function whereField($field, $boost = null): self
    {
        $this->criteria->addField(new Field($field, $boost));

        return $this;
    }

    public function fuzzy($fuzziness = 'AUTO'): self
    {
        $this->criteria->setFuzziness($fuzziness);

        return $this;
    }

    public function matches($queryString): self
    {
        $this->criteria->setQueryString($queryString);

        return $this;
    }

    public function orderBy($field, $direction): self
    {
        $this->sort->addSort(
            new SortItem($field, $direction)
        );

        return $this;
    }

    public function get()
    {
        if ($this->criteria->getQueryString()) {
            $client = app(Client::class, ['index' => $this->index]);

            $body = [
                'query' => (new Query())
                    ->setMatch($this->criteria)
                    ->toArray(),
            ];

            if ($this->sort->count()) {
                $body['sort'] = $this->sort->toArray();
            }

            $documents = $client->query($body);

            $ids = collect(data_get($documents, 'hits.hits'))
                ->pluck('_id');

            $this->query->whereIn('id', $ids->all());
        } else {
            $ids = collect();
        }

        $models = $this->query->get();

        return $models->sort(fn ($a, $b) => $ids->search($a->id) <=> $ids->search($b->id))
            ->values();
    }
}
