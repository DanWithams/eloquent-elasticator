<?php

namespace DanWithams\EloquentElasticator;

use Illuminate\Support\Arr;
use Elastic\Elasticsearch\ClientBuilder;
use Elastic\Elasticsearch\Client as ElasticsearchClient;

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
        return $this->client->index([
            'index' => $this->index,
            'body'  => $body,
        ])->asArray();
    }

    public function delete($id)
    {
        return $this->client->delete([
            'index' => $this->index,
            'id' => $id,
        ]);
    }

    public function query($body)
    {
        return $this->client->search([
            'index' => $this->index,
            'body'  => $body,
        ])->asArray();

//        printf("Total docs: %d\n", $response['hits']['total']['value']);
//        printf("Max score : %.4f\n", $response['hits']['max_score']);
//        printf("Took      : %d ms\n", $response['took']);
//        print_r($response['hits']['hits']); // documents

    }
}
