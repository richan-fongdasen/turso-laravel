<?php

declare(strict_types=1);

namespace RichanFongdasen\Turso\Http;

use Illuminate\Support\Collection;
use InvalidArgumentException;

class ResponseBody
{
    protected ?string $baseUrl = null;

    protected ?string $baton = null;

    protected Collection $queryResponses;

    protected array $rawResponse;

    protected RequestBody $requestBody;

    public function __construct(RequestBody $requestBody, array $response)
    {
        $this->rawResponse = $response;
        $this->requestBody = $requestBody;

        $this->baseUrl = data_get($response, 'base_url');
        $this->baton = data_get($response, 'baton');

        $this->queryResponses = $this->extractQueryResponses($response);
    }

    protected function extractQueryResponses(array $response): Collection
    {
        $queryResponses = new Collection();

        collect((array) data_get($response, 'results', []))
            ->each(function (array $queryResponse, int $index) use ($queryResponses) {
                $queryResponses->push(new QueryResponse(
                    $this->requestBody->getQuery($index),
                    $queryResponse
                ));
            });

        return $queryResponses;
    }

    public function getBaseUrl(): ?string
    {
        return $this->baseUrl;
    }

    public function getBaton(): ?string
    {
        return $this->baton;
    }

    public function getQueryResponse(int $index): QueryResponse
    {
        if (! $this->queryResponses->has($index)) {
            throw new InvalidArgumentException('Can not find the QueryResponse instance with the specified index: ' . $index . '.');
        }

        return $this->queryResponses->get($index);
    }

    public function getQueryResponses(): Collection
    {
        return $this->queryResponses;
    }

    public function getRawResponse(): array
    {
        return $this->rawResponse;
    }
}
