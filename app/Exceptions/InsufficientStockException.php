<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Exception yang dilempar oleh StockService ketika stok bahan baku
 * tidak mencukupi untuk memproses order.
 */
class InsufficientStockException extends RuntimeException
{
    /**
     * @param  string  $message
     * @param  array<int, array{ingredient: string, unit: string, needed: float, available: float}>  $insufficients
     */
    public function __construct(
        string $message,
        private readonly array $insufficients = [],
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return array<int, array{ingredient: string, unit: string, needed: float, available: float}>
     */
    public function getInsufficientItems(): array
    {
        return $this->insufficients;
    }

    /**
     * Kembalikan pesan error yang ramah pengguna untuk ditampilkan di UI.
     */
    public function getUserFriendlyMessage(): string
    {
        if (empty($this->insufficients)) {
            return $this->getMessage();
        }

        $lines = ['Stok bahan baku tidak mencukupi:'];

        foreach ($this->insufficients as $item) {
            $lines[] = sprintf(
                '• %s: butuh %.2f %s, tersedia %.2f %s',
                $item['ingredient'],
                $item['needed'],
                $item['unit'],
                $item['available'],
                $item['unit']
            );
        }

        return implode("\n", $lines);
    }
}
