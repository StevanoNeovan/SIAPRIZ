<?php
// app/Repositories/Contracts/PenjualanRepositoryInterface.php

namespace App\Repositories\Contracts;

interface PenjualanRepositoryInterface
{
    public function createTransaction(array $data): int;
    public function createTransactionDetails(int $idTransaksi, array $items): void;
    public function processUpload(int $idUpload): bool;
    public function logUpload(array $logData): int;
    public function updateUploadStatus(int $idUpload, string $status, array $stats): void;
}