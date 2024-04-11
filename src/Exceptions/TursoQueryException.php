<?php

declare(strict_types=1);

namespace RichanFongdasen\Turso\Exceptions;

use LogicException;

class TursoQueryException extends LogicException
{
    public function __construct(
        protected string $errorCode,
        protected string $errorMessage,
        protected string $statement
    ) {
        parent::__construct($this->__toString());
    }

    public function __toString(): string
    {
        return sprintf(
            '(%s) %s, while executing the following statement: %s',
            $this->errorCode,
            $this->errorMessage,
            trim($this->statement)
        );
    }
}
