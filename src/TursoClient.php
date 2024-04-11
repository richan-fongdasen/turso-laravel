<?php

declare(strict_types=1);

namespace RichanFongdasen\Turso;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use RichanFongdasen\Turso\Exceptions\TursoQueryException;

class TursoClient
{
    protected string $baseUrl;

    protected ?string $baton = null;

    protected array $config = [];

    protected bool $loggingQueries;

    protected Collection $queryLog;

    protected ?PendingRequest $request = null;

    public function __construct()
    {
        $this->config = config('database.connections.turso', []);

        $this->queryLog = new Collection();

        $this->disableQueryLog();
        $this->resetClientState();
    }

    public function __destruct()
    {
        $this->close();
    }

    public function close(): void
    {
        $this->request()
            ->baseUrl($this->baseUrl)
            ->post('/v3/pipeline', $this->createRequestBody('close'));

        $this->resetClientState();
    }

    protected function createRequest(): PendingRequest
    {
        $accessToken = data_get($this->config, 'access_token');

        return Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->connectTimeout(2)
            ->timeout(5)
            ->withToken($accessToken)
            ->acceptJson();
    }

    protected function createRequestBody(string $type, ?string $statement = null, array $bindings = []): array
    {
        $requestBody = [];

        if (($this->baton !== null) && ($this->baton !== '')) {
            $requestBody['baton'] = $this->baton;
        }

        $requestBody['requests'] = ($type === 'close')
            ? [['type' => 'close']]
            : [[
                'type' => 'execute',
                'stmt' => [
                    'sql' => $statement,
                ],
            ]];

        if (($type !== 'close') && (count($bindings) > 0)) {
            $requestBody['requests'][0]['stmt']['args'] = $bindings;
        }

        return $requestBody;
    }

    public function disableQueryLog(): void
    {
        $this->loggingQueries = false;
    }

    public function enableQueryLog(): void
    {
        $this->loggingQueries = true;
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

    public function query(string $statement, array $bindingValues = []): array
    {
        $response = $this->request()
            ->baseUrl($this->baseUrl)
            ->post('/v3/pipeline', $this->createRequestBody('execute', $statement, $bindingValues));

        if ($response->failed()) {
            $this->resetClientState();
            $response->throw();
        }

        $jsonResponse = $response->json();

        if ($this->loggingQueries) {
            $this->queryLog->push([
                'statement' => $statement,
                'bindings'  => $bindingValues,
                'response'  => $jsonResponse,
            ]);
        }

        $this->baton = data_get($jsonResponse, 'baton', null);
        $baseUrl = (string) data_get($jsonResponse, 'base_url', $this->baseUrl);

        if (($baseUrl !== '') && ($baseUrl !== $this->baseUrl)) {
            $this->baseUrl = $baseUrl;
        }

        $result = new Collection(data_get($jsonResponse, 'results.0', []));

        if ($result->get('type') === 'error') {
            $this->resetClientState();

            $errorCode = (string) data_get($result, 'error.code', 'UNKNOWN_ERROR');
            $errorMessage = (string) data_get($result, 'error.message', 'Error: An unknown error has occurred');

            throw new TursoQueryException($errorCode, $errorMessage, $statement);
        }

        return data_get($result, 'response', []);
    }

    public function request(): PendingRequest
    {
        if ($this->request === null) {
            $this->request = $this->createRequest();
        }

        return $this->request;
    }

    public function resetClientState(): void
    {
        $this->baton = null;
        $this->baseUrl = data_get($this->config, 'turso_url');
    }
}
