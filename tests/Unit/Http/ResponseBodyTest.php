<?php

use RichanFongdasen\Turso\Http\QueryResponse;
use RichanFongdasen\Turso\Http\RequestBody;
use RichanFongdasen\Turso\Http\ResponseBody;
use RichanFongdasen\Turso\Queries\CloseQuery;
use RichanFongdasen\Turso\Queries\ExecuteQuery;

beforeEach(function () {
    $this->request = RequestBody::create('baton')
        ->push(new ExecuteQuery('SELECT "id", "name" FROM "users"'))
        ->push(new CloseQuery());

    $this->rawResponse = [
        'base_url' => 'https:://example-base-url.turso.io',
        'baton'    => 'baton-string-example',
        'results'  => [
            [
                'type'     => 'ok',
                'response' => [
                    'type'   => 'execute',
                    'result' => [
                        'cols' => [
                            [
                                'name'     => 'id',
                                'decltype' => 'INTEGER',
                            ],
                            [
                                'name'     => 'name',
                                'decltype' => 'TEXT',
                            ],
                        ],
                        'rows' => [
                            [
                                [
                                    'type'  => 'integer',
                                    'value' => '1',
                                ],
                                [
                                    'type'  => 'text',
                                    'value' => 'John Doe',
                                ],
                            ],
                            [
                                [
                                    'type'  => 'integer',
                                    'value' => '2',
                                ],
                                [
                                    'type'  => 'text',
                                    'value' => 'Jane Doe',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'type'     => 'ok',
                'response' => [
                    'type' => 'close',
                ],
            ],
        ],
    ];

    $this->response = new ResponseBody($this->request, $this->rawResponse);
});

test('it can extract the response data', function () {
    expect($this->response->getBaseUrl())->toBe('https:://example-base-url.turso.io')
        ->and($this->response->getBaton())->toBe('baton-string-example')
        ->and($this->response->getRawResponse())->toBe($this->rawResponse)
        ->and($this->response->getQueryResponses())->toHaveCount(2)
        ->and($this->response->getQueryResponse(0))->toBeInstanceOf(QueryResponse::class)
        ->and($this->response->getQueryResponse(1))->toBeInstanceOf(QueryResponse::class);
})->group('ResponseBodyTest', 'UnitTest');

test('it can retrieve the QueryResponse instance by the specified index', function () {
    $response = $this->response->getQueryResponse(0);

    expect($response->getAffectedRows())->toBe(0)
        ->and($response->getLastInsertId())->toBeNull()
        ->and($response->getQuery())->toBeInstanceOf(ExecuteQuery::class)
        ->and($response->getReplicationIndex())->toBe(0)
        ->and($response->getResponseType())->toBe('ok')
        ->and($response->getColumns()->toArray())->toBe(['id', 'name'])
        ->and($response->getRows()->toArray())->toBe([
            [
                'id'   => 1,
                'name' => 'John Doe',
            ],
            [
                'id'   => 2,
                'name' => 'Jane Doe',
            ],
        ]);
})->group('ResponseBodyTest', 'UnitTest');

test('it will raise InvalidArgumentException when the specified index is out of range', function () {
    $this->response->getQueryResponse(2);
})->throws(\InvalidArgumentException::class)->group('ResponseBodyTest', 'UnitTest');
