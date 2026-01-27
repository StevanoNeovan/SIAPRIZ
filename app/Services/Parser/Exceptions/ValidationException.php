<?php
// app/Services/Parser/Exceptions/ValidationException.php

namespace App\Services\Parser\Exceptions;

/**
 * Exception untuk validation error
 */
class ValidationException extends ParserException
{
    public function __construct(
        string $orderId,
        string $field,
        string $reason,
        string $userMessage = ""
    ) {
        $message = "Order {$orderId} validation failed on {$field}: {$reason}";
        $userMessage = $userMessage ?: "Pesanan {$orderId} tidak valid ({$field}: {$reason})";
        
        parent::__construct($message, $userMessage, 'validation');
    }
}
