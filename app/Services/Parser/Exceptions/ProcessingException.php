<?php
// app/Services/Parser/Exceptions/ProcessingException.php

namespace App\Services\Parser\Exceptions;

/**
 * Exception untuk error saat processing
 */
class ProcessingException extends ParserException
{
    public function __construct(
        string $orderId,
        string $reason,
        string $userMessage = ""
    ) {
        $message = "Order {$orderId} processing failed: {$reason}";
        $userMessage = $userMessage ?: "Pesanan {$orderId} gagal diproses. Silakan hubungi administrator.";
        
        parent::__construct($message, $userMessage, 'error');
    }
}
