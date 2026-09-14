<?php

declare(strict_types=1);

namespace GildedRose;

final class GildedRose
{
    private const AGED_BRIE = 'Aged Brie';
    private const BACKSTAGE_PASSES = 'Backstage passes to a TAFKAL80ETC concert';
    private const SULFURAS = 'Sulfuras, Hand of Ragnaros';

    /**
     * @param Item[] $items
     */
    public function __construct(
        private array $items
    ) {
    }

    public function updateQuality(): void
    {
        foreach ($this->items as $item) {
            if ($item->name === self::SULFURAS) {
                // legendary item, never sold and never changes
                continue;
            }

            $item->sellIn--;

            if ($item->name === self::AGED_BRIE) {
                $this->increaseQuality($item, $item->sellIn < 0 ? 2 : 1);
            } elseif ($item->name === self::BACKSTAGE_PASSES) {
                $this->updateBackstagePasses($item);
            } elseif (str_starts_with($item->name, 'Conjured')) {
                // conjured items degrade twice as fast as normal ones
                $this->decreaseQuality($item, $item->sellIn < 0 ? 4 : 2);
            } else {
                $this->decreaseQuality($item, $item->sellIn < 0 ? 2 : 1);
            }
        }
    }

    private function updateBackstagePasses(Item $item): void
    {
        if ($item->sellIn < 0) {
            $item->quality = 0;

            return;
        }

        if ($item->sellIn < 5) {
            $this->increaseQuality($item, 3);
        } elseif ($item->sellIn < 10) {
            $this->increaseQuality($item, 2);
        } else {
            $this->increaseQuality($item, 1);
        }
    }

    private function increaseQuality(Item $item, int $amount): void
    {
        $item->quality = min(50, $item->quality + $amount);
    }

    private function decreaseQuality(Item $item, int $amount): void
    {
        $item->quality = max(0, $item->quality - $amount);
    }
}
