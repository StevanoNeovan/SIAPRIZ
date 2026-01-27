<?php
// app/Services/Parser/Validators/OrderValidator.php

namespace App\Services\Parser\Validators;

use App\Services\Parser\Exceptions\SkippedOrderException;
use App\Services\Parser\Exceptions\ValidationException;

/**
 * Validator untuk order sebelum diproses
 */
class OrderValidator
{
    /**
     * Status yang tidak perlu diproses
     */
    private const SKIPPABLE_STATUSES = [
        'dibatalkan',
        'dikembalikan',
        'refund',
        'return',
    ];
    
    /**
     * Validate order sebelum diproses
     * 
     * @param string $orderId
     * @param array $orderData
     * @return bool
     * @throws SkippedOrderException
     * @throws ValidationException
     */
    public static function validate(string $orderId, array $orderData): bool
    {
        // Check status - skip jika dibatalkan atau dikembalikan
        $status = strtolower($orderData['status_order'] ?? '');
        
        \Illuminate\Support\Facades\Log::debug('OrderValidator checking status', [
            'order_id' => $orderId,
            'status' => $status,
            'skippable_statuses' => self::SKIPPABLE_STATUSES,
            'is_skippable' => in_array($status, self::SKIPPABLE_STATUSES),
        ]);
        
        if (in_array($status, self::SKIPPABLE_STATUSES)) {
            \Illuminate\Support\Facades\Log::info('Order will be skipped', [
                'order_id' => $orderId,
                'status' => $status,
            ]);
            
            throw new SkippedOrderException(
                $orderId,
                "Status pesanan: {$status}",
                "Pesanan dengan status '{$status}' tidak diproses"
            );
        }
        
        // Validate required fields
        self::validateRequiredFields($orderId, $orderData);
        
        // Validate financial data
        self::validateFinancialData($orderId, $orderData);
        
        return true;
    }
    
    /**
     * Validate required fields
     * Hanya check field yang benar-benar penting
     */
    private static function validateRequiredFields(string $orderId, array $orderData): void
    {
        // Hanya check order_id dan status_order yang wajib ada
        $required = ['order_id', 'status_order'];
        
        foreach ($required as $field) {
            if (empty($orderData[$field])) {
                throw new ValidationException(
                    $orderId,
                    $field,
                    "Field tidak boleh kosong",
                    "Pesanan {$orderId} tidak valid: {$field} kosong"
                );
            }
        }
    }
    
    /**
     * Validate financial data
     * Hanya check total_pesanan > 0 untuk pesanan yang valid
     */
    private static function validateFinancialData(string $orderId, array $orderData): void
    {
        $totalPesanan = (float) ($orderData['total_pesanan'] ?? 0);
        
        // Total pesanan harus lebih dari 0 untuk pesanan yang valid
        // Jika 0, kemungkinan pesanan dibatalkan atau tidak valid
        if ($totalPesanan <= 0) {
            throw new ValidationException(
                $orderId,
                'total_pesanan',
                "Total pesanan harus lebih dari 0",
                "Pesanan {$orderId} tidak valid: total pesanan 0 atau negatif"
            );
        }
    }
}
