<?php

namespace App\Services;

use RuntimeException;

class GhlException extends RuntimeException
{
    /** 4xx other than 429 will never succeed on retry. */
    public function retryable(): bool
    {
        $c = $this->getCode();

        return $c === 0 || $c === 429 || $c >= 500;
    }
}
