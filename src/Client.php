<?php

namespace DanWithams\EloquentElasticator;

use Illuminate\Support\Arr;
use Elastic\Elasticsearch\ClientBuilder;
use Elastic\Elasticsearch\Client as ElasticsearchClient;
use Throwable;

class Client
{
    protected ElasticsearchClient $client;

    public function __construct(protected string $index)
    {
        $this->client = ClientBuilder::create()
            ->setHosts(Arr::wrap(config('elasticator.hosts')))
            ->build();
    }

    public function index($body)
    {
        try {
            return $this->client->index(
                collect([
                    'index' => $this->index,
                    'body'  => $body,
                ])
                ->put('id', data_get($body, 'id', null))
                ->filter()
                ->all()
            )->asArray();
        } catch (Throwable $throwable) {
            return false;
        }
    }

    public function delete($id)
    {
        try {
            return $this->client->delete([
                'index' => $this->index,
                'id' => $id,
            ]);
        } catch (Throwable $throwable) {
            return false;
        }
    }

    public function query($body)
    {
        try {
            return $this->client->search([
                'index' => $this->index,
                'body'  => $body,
            ])->asArray();
        } catch (Throwable $throwable) {
            return false;
        }
    }
}
