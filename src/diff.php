<?php

declare(strict_types=1);

namespace JmvDevelop\Differ;

use function Psl\Iter\any;
use function Psl\Iter\search;
use function Psl\Vec\filter;

/**
 * @template U
 * @template V
 *
 * @param iterable<U>         $nextItems
 * @param iterable<V>         $currentItems
 * @param callable(U, V):bool $comparator
 * @param callable(U, V):bool $eqCallback
 *
 * @return DifferResults<U, V>
 */
function diff(iterable $nextItems, iterable $currentItems, callable $comparator, callable $eqCallback): DifferResults
{
    $toCreate = filter(
        $nextItems,
        /** @param U $u */
        fn ($u): bool => !any(
            $currentItems,
            /** @param V $v */
            fn ($v) => $comparator($u, $v)
        )
    );

    $toDelete = filter(
        $currentItems,
        /** @param V $v */
        fn ($v): bool => !any(
            $nextItems,
            /** @param U $u */
            fn ($u) => $comparator($u, $v)
        )
    );

    $toStay = filter(
        $nextItems,
        /** @param U $u */
        function ($u) use ($currentItems, $comparator, $eqCallback): bool {
            $currentItem = search(
                $currentItems,
                /** @param V $v */
                fn ($v) => $comparator($u, $v)
            );

            if (null === $currentItem) {
                return false;
            } else {
                return $eqCallback($u, $currentItem);
            }
        }
    );

    $toUpdate = filter(
        $nextItems,
        /** @param U $u */
        function ($u) use ($currentItems, $comparator, $eqCallback): bool {
            $currentItem = search(
                $currentItems,
                /** @param V $v */
                fn ($v) => $comparator($u, $v)
            );

            if (null === $currentItem) {
                return false;
            } else {
                return !$eqCallback($u, $currentItem);
            }
        }
    );

    return new DifferResults(
        $toCreate,
        $toDelete,
        $toStay,
        $toUpdate,
    );
}
