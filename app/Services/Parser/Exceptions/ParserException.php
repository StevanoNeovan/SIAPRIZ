<?php
// app/Services/Parser/Exceptions/ParserException.php

namespace App\Services\Parser\Exceptions;

use Exception;

/**
 * Base exception untuk Parser
 */
class ParserException extends Exception
{
    protected $userMessage;
    protected $errorCode;
    protected $errorType; // 'validation', 'skipped', 'error'
    
    public function __construct(
        string $message = "",
        string $userMessage = "",
        string $errorType = 'error',
        int $code = 0,
        Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        
        $this->userMessage = $userMessage ?: $message;
        $this->errorType = $errorType;
    }
    
    /**
     * Get user-friendly message
     */
    public function getUserMessage(): string
    {
        return $this->userMessage;
    }
    
    /**
     * Get error type
     */
    public function getErrorType(): string
    {
        return $this->errorType;
    }
}
