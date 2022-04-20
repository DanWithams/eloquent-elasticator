<?php

namespace DanWithams\EloquentElasticator;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Traits\ForwardsCalls;
use DanWithams\EloquentElasticator\Models\Query;
use DanWithams\EloquentElasticator\Models\Field;
use DanWithams\EloquentElasticator\Concerns\Client;
use DanWithams\EloquentElasticator\Models\MultiMatch;

class QueryBuilder
{
    use ForwardsCalls;

    protected string $index;
    protected Builder $query;
    protected MultiMatch $multiMatch;

    public function __construct(protected string $model)
    {
        $this->index = (new $this->model)->elasticatableAs();
        $this->multiMatch = new MultiMatch();
        $this->query = call_user_func($this->model . '::query');
    }

    public function __call($name, $arguments)
    {
        $this->forwardCallTo($this->query, $name, $arguments);

        return $this;
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
            ->pluck('_id');

        $models = $this->query->whereIn('id', $ids->all())
            ->get();

        return $models->sort(fn ($a, $b) => $ids->search($a->id) <=> $ids->search($b->id))
            ->values();
    }
}
