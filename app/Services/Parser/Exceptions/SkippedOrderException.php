<?php
// app/Services/Parser/Exceptions/SkippedOrderException.php

namespace App\Services\Parser\Exceptions;

/**
 * Exception untuk pesanan yang di-skip (dibatalkan, refund, dll)
 * Ini bukan error, tapi informasi bahwa pesanan tidak diproses
 */
class SkippedOrderException extends ParserException
{
    public function __construct(
        string $orderId,
        string $reason,
        string $userMessage = ""
    ) {
        $message = "Order {$orderId} skipped: {$reason}";
        $userMessage = $userMessage ?: "Pesanan {$orderId} tidak diproses ({$reason})";
        
        parent::__construct($message, $userMessage, 'skipped');
    }
}
