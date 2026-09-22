<?php

namespace App\Http\Controllers;

use Closure;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

abstract class Controller
{
    use AuthorizesRequests;

    /**
     * Retry the given callback when it fails due to a unique constraint collision.
     *
     * This complements Laravel's transaction retries, which only recover from
     * deadlocks and lock timeouts, not duplicate key errors raised when two
     * requests race to claim the same generated sequence number.
     *
     * @template TReturn
     *
     * @param  Closure(): TReturn  $callback
     * @return TReturn
     */
    protected function retryOnUniqueViolation(Closure $callback, int $attempts = 3): mixed
    {
        $attempt = 0;

        while (true) {
            $attempt++;

            try {
                return $callback();
            } catch (QueryException $exception) {
                if ($attempt >= $attempts || ! $this->isUniqueConstraintViolation($exception)) {
                    throw $exception;
                }
            }
        }
    }

    private function isUniqueConstraintViolation(QueryException $exception): bool
    {
        $code = (string) $exception->getCode();

        if ($code === '23000' || $code === '23505' || $code === '19') {
            return true;
        }

        $message = $exception->getMessage();

        return str_contains($message, 'Duplicate entry')
            || str_contains($message, 'UNIQUE constraint failed')
            || str_contains($message, 'duplicate key value')
            || str_contains($message, 'Integrity constraint violation');
    }
}
