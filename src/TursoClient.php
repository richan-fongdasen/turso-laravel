<?php

declare(strict_types=1);

namespace RichanFongdasen\Turso;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use RichanFongdasen\Turso\Http\QueryResponse;
use RichanFongdasen\Turso\Http\RequestBody;
use RichanFongdasen\Turso\Http\ResponseBody;
use RichanFongdasen\Turso\Queries\ExecuteQuery;

class TursoClient
{
    protected string $baseUrl;

    protected ?string $baton = null;

    protected Collection $config;

    protected bool $loggingQueries;

    protected Collection $queryLog;

    protected ?PendingRequest $httpRequest = null;

    public function __construct(array $config = [])
    {
        $this->config = new Collection(array_merge(
            $config,
            config('turso-laravel', [])
        ));

        $this->queryLog = new Collection();

        $this->enableQueryLog();
        $this->resetHttpClientState();
    }

    public function __destruct()
    {
        $this->close();
    }

    public function close(): void
    {
        if ((string) $this->baton === '') {
            return;
        }

        $body = RequestBody::create($this->baton)
            ->withCloseRequest();

        $this->httpRequest()
            ->baseUrl($this->baseUrl)
            ->post('/v3/pipeline', $body->toArray());

        $this->resetHttpClientState();
    }

    protected function createHttpRequest(): PendingRequest
    {
        $this->resetHttpClientState();

        $accessToken = $this->config->get('access_token');

        return Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->connectTimeout((int) Arr::get($this->config, 'client.connect_timeout', 2))
            ->timeout((int) Arr::get($this->config, 'client.timeout', 5))
            ->withToken($accessToken)
            ->acceptJson();
    }

    public function disableQueryLog(): void
    {
        $this->loggingQueries = false;
    }

    public function enableQueryLog(): void
    {
        $this->loggingQueries = true;
    }

    public function freshHttpRequest(): PendingRequest
    {
        $this->httpRequest = $this->createHttpRequest();

        return $this->httpRequest;
    }

    public function getBaseUrl(): ?string
    {
        return $this->baseUrl;
    }

    public function getBaton(): ?string
    {
        return $this->baton;
    }

    public function getQueryLog(): Collection
    {
        return $this->queryLog;
    }

    public function query(string $statement, array $bindingValues = []): QueryResponse
    {
        $query = new ExecuteQuery($statement, $bindingValues);

        $requestBody = RequestBody::create($this->baton)
            ->withForeignKeyConstraints()
            ->push($query);

        $httpResponse = $this->httpRequest()
            ->baseUrl($this->baseUrl)
            ->post('/v3/pipeline', $requestBody->toArray());

        if ($httpResponse->failed()) {
            $this->resetHttpClientState();
            $httpResponse->throw();
        }

        $responseBody = new ResponseBody($requestBody, $httpResponse->json() ?? []);

        if ($this->loggingQueries) {
            $this->queryLog->push([
                'request'   => $requestBody->toArray(),
                'response'  => $responseBody->getRawResponse(),
            ]);
        }

        $this->baton = $responseBody->getBaton();
        $baseUrl = (string) $responseBody->getBaseUrl();

        if ($baseUrl !== '') {
            $this->baseUrl = $baseUrl;
        }

        return $responseBody->getQueryResponse($query->getIndex());
    }

    public function httpRequest(): PendingRequest
    {
        return ($this->httpRequest === null)
            ? $this->freshHttpRequest()
            : $this->httpRequest;
    }

    public function resetHttpClientState(): void
    {
        $this->baton = null;
        $this->baseUrl = (string) $this->config->get('db_url', '');
    }
}
