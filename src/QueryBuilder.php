<?php

namespace DanWithams\EloquentElasticator;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Traits\ForwardsCalls;
use DanWithams\EloquentElasticator\Models\Sort;
use DanWithams\EloquentElasticator\Models\Query;
use DanWithams\EloquentElasticator\Models\Field;
use DanWithams\EloquentElasticator\Concerns\Client;
use DanWithams\EloquentElasticator\Models\SortItem;
use DanWithams\EloquentElasticator\Models\MultiMatch;
use DanWithams\EloquentElasticator\Models\QueryString;
use DanWithams\EloquentElasticator\Models\Contracts\QueryCriteria;

class QueryBuilder
{
    use ForwardsCalls;

    protected string $index;
    protected Builder $query;
    protected Collection $criteria;
    protected Sort $sort;
    protected $client;

    public function __construct(protected string $model)
    {
        $this->criteria = collect([
            new MultiMatch(),
            new QueryString()
        ]);
        $this->sort = new Sort();
        $this->index = (new $this->model)->elasticatableAs();
        $this->query = call_user_func($this->model . '::query');
        $this->client = app(Client::class, ['index' => $this->index]);
    }

    public function __call($name, $arguments)
    {
        $this->forwardCallTo($this->query, $name, $arguments);

        return $this;
    }

    public function whereField($field, $boost = null): self
    {
        $this->criteria->each->addField(new Field($field, $boost));

        return $this;
    }

    public function fuzzy($fuzziness = 'AUTO'): self
    {
        $this->criteria->each->setFuzziness($fuzziness);

        return $this;
    }

    public function matches($queryString): self
    {
        $this->criteria->each->setQueryString($queryString);

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
        $ids = $this->criteria->map(function (QueryCriteria $criteria) {
                $body = [
                    'query' => (new Query())
                        ->setMatch($criteria)
                        ->toArray(),
                ];

                if ($this->sort->count()) {
                    $body['sort'] = $this->sort->toArray();
                }

                $documents = $this->client->query($body);

                return data_get($documents, 'hits.hits');
            })
            ->flatten(1)
            ->groupBy('_id')
            ->map(function ($hits) {
                $hit = $hits->first();
                $hit['_score'] = $hits->avg('_score');
                return $hit;
            })
            ->sort(fn ($a, $b) => $a['_score'] <=> $b['_score'])
            ->values()
            ->pluck('_id');

        $this->query->whereIn('id', $ids->all());

        $models = $this->query->get();

        return $models->sort(fn ($a, $b) => $ids->search($a->id) <=> $ids->search($b->id))
            ->values();
    }
}
