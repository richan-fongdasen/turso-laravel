<?php

use RichanFongdasen\Turso\Exceptions\TursoQueryException;
use RichanFongdasen\Turso\Http\QueryResponse;
use RichanFongdasen\Turso\Queries\ExecuteQuery;

beforeEach(function () {
    $this->query = new ExecuteQuery(
        'SELECT * FROM "users" WHERE "id" = ?',
        [
            [
                'type'  => 'integer',
                'value' => 1,
            ],
        ]
    );

    $this->originalResponse = [
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
                    [
                        'name'     => 'email',
                        'decltype' => 'TEXT',
                    ],
                ],
                'rows' => [
                    [
                        [
                            'type'  => 'integer',
                            'value' => '31',
                        ],
                        [
                            'type'  => 'text',
                            'value' => 'John Doe',
                        ],
                        [
                            'type'  => 'text',
                            'value' => 'john.doe@gmail.com',
                        ],
                    ],
                    [
                        [
                            'type'  => 'integer',
                            'value' => '32',
                        ],
                        [
                            'type'  => 'text',
                            'value' => 'Jane Doe',
                        ],
                        [
                            'type'  => 'text',
                            'value' => 'jane.doe@gmail.com',
                        ],
                    ],
                    [
                        [
                            'type'  => 'integer',
                            'value' => '33',
                        ],
                        [
                            'type'  => 'text',
                            'value' => 'June Monroe',
                        ],
                        [
                            'type'  => 'text',
                            'value' => 'june.monroe@gmail.com',
                        ],
                    ],
                ],
                'affected_row_count' => 3,
                'last_insert_rowid'  => '123',
                'replication_index'  => 8,
            ],
        ],
    ];

    $this->response = new QueryResponse($this->query, $this->originalResponse);
});

test('it can extract the response data', function () {
    expect($this->response->getAffectedRows())->toBe(3)
        ->and($this->response->getLastInsertId())->toBe('123')
        ->and($this->response->getQuery())->toBe($this->query)
        ->and($this->response->getReplicationIndex())->toBe(8)
        ->and($this->response->getResponseType())->toBe('ok')
        ->and($this->response->getRawResponse())->toBe($this->originalResponse);
})->group('QueryResponseTest', 'UnitTest');

test('it can extract columns from the response', function () {
    $columns = $this->response->getColumns()->toArray();

    expect($columns)->toBeArray()
        ->toHaveCount(3)
        ->toBe(['id', 'name', 'email']);
})->group('QueryResponseTest', 'UnitTest');

test('it can extract the rows from the response', function () {
    $rows = $this->response->getRows()->toArray();

    expect($rows)->toBeArray()
        ->and($rows)->toHaveCount(3)
        ->and($rows[0])->toBe([
            'id'    => 31,
            'name'  => 'John Doe',
            'email' => 'john.doe@gmail.com',
        ])
        ->and($rows[1])->toBe([
            'id'    => 32,
            'name'  => 'Jane Doe',
            'email' => 'jane.doe@gmail.com',
        ])
        ->and($rows[2])->toBe([
            'id'    => 33,
            'name'  => 'June Monroe',
            'email' => 'june.monroe@gmail.com',
        ]);
})->group('QueryResponseTest', 'UnitTest');

test('it can raise TursoQueryException if the response contains any error', function () {
    $response = new QueryResponse($this->query, [
        'type'  => 'error',
        'error' => [
            'code'    => 'QUERY_ERROR',
            'message' => 'Error: An unknown error has occurred',
        ],
    ]);
})->throws(TursoQueryException::class)->group('QueryResponseTest', 'UnitTest');
