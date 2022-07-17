<?php

declare(strict_types=1);

namespace App\DTO;

class ErrorDTO
{
    private int $code;

    private string $message;

    private ?array $errors;

    public function __construct(int $code, string $message, ?array $errors = null)
    {
        $this->code    = $code;
        $this->message = $message;
        $this->errors  = $errors;
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getErrors(): ?array
    {
        return $this->errors;
    }
}
