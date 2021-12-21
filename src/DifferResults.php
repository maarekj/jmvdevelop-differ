<?php

declare(strict_types=1);

namespace JmvDevelop\Differ;

/**
 * @template U
 * @template V
 */
final class DifferResults
{
    /**
     * @param list<U> $toCreate
     * @param list<V> $toDelete
     * @param list<U> $toStay
     * @param list<U> $toUpdate
     */
    public function __construct(
        private array $toCreate,
        private array $toDelete,
        private array $toStay,
        private array $toUpdate
    )
    {
    }

    /** @return list<U> */
    public function toCreate(): array
    {
        return $this->toCreate;
    }

    /** @return list<V> */
    public function toDelete(): array
    {
        return $this->toDelete;
    }

    /** @return list<U> */
    public function toStay(): array
    {
        return $this->toStay;
    }

    /** @return list<U> */
    public function toUpdate(): array
    {
        return $this->toUpdate;
    }
}
