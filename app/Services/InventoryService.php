<?php

namespace App\Services;

use App\Exceptions\InsufficientStockException;
use App\Models\Product;
use App\Models\ProductVariant;
use InvalidArgumentException;
use RuntimeException;

class InventoryService
{
    public function available(Product|ProductVariant $stockable): ?int
    {
        return $stockable->stock;
    }

    public function hasAvailableStock(Product|ProductVariant $stockable, int $quantity): bool
    {
        if ($quantity < 1) {
            return false;
        }

        return $stockable->stock === null || $stockable->stock >= $quantity;
    }

    public function canFulfill(Product|ProductVariant|null $stockable, int $quantity): bool
    {
        return $stockable !== null && $this->hasAvailableStock($stockable, $quantity);
    }

    public function decrement(Product|ProductVariant $stockable, int $quantity): bool
    {
        $this->ensurePositiveQuantity($quantity);

        $stock = $stockable->newQuery()
            ->whereKey($stockable->getKey())
            ->lockForUpdate()
            ->value('stock');

        if ($stock === null) {
            return false;
        }

        $updated = $stockable->newQuery()
            ->whereKey($stockable->getKey())
            ->where('stock', '>=', $quantity)
            ->decrement('stock', $quantity);

        if ($updated !== 1) {
            $stockable->refresh();

            if ($stockable->stock === null) {
                return false;
            }

            throw new InsufficientStockException;
        }

        $stockable->refresh();

        return true;
    }

    public function increment(Product|ProductVariant $stockable, int $quantity): void
    {
        $this->ensurePositiveQuantity($quantity);

        $stock = $stockable->newQuery()
            ->whereKey($stockable->getKey())
            ->lockForUpdate()
            ->value('stock');

        if ($stock === null) {
            return;
        }

        $updated = $stockable->newQuery()
            ->whereKey($stockable->getKey())
            ->whereNotNull('stock')
            ->increment('stock', $quantity);

        if ($updated !== 1) {
            $stockable->refresh();

            if ($stockable->stock === null) {
                return;
            }

            throw new RuntimeException('The stock record could not be updated.');
        }

        $stockable->refresh();
    }

    public function adjust(Product|ProductVariant $stockable, int $adjustment): void
    {
        if ($adjustment === 0) {
            return;
        }

        if ($adjustment > 0) {
            $this->initializeStock($stockable, $adjustment);

            return;
        }

        if (! $this->decrement($stockable, abs($adjustment))) {
            throw new InsufficientStockException;
        }
    }

    private function initializeStock(Product|ProductVariant $stockable, int $quantity): void
    {
        $updated = $stockable->newQuery()
            ->whereKey($stockable->getKey())
            ->whereNull('stock')
            ->update(['stock' => $quantity]);

        if ($updated !== 1) {
            $stockable->refresh();

            if ($stockable->stock === null) {
                throw new RuntimeException('The stock record could not be initialized.');
            }

            $this->increment($stockable, $quantity);

            return;
        }

        $stockable->refresh();
    }

    private function ensurePositiveQuantity(int $quantity): void
    {
        if ($quantity < 1) {
            throw new InvalidArgumentException('Quantity must be greater than zero.');
        }
    }
}
