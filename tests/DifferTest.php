<?php

namespace JmvDevelop\Differ\Tests;

use PHPUnit\Framework\TestCase;
use function JmvDevelop\Differ\diff;

/**
 * @psalm-type UserType = array{id: int, name: string, token: string}
 */
final class DifferTest extends TestCase
{
    public function testDiff()
    {
        /** @var list<UserType> $original */
        $original = [
            $this->user(1, 'joseph', '1'),
            $this->user(2, 'vanessa', '1'),
            $this->user(3, 'yona', '1'),
            $this->user(4, 'naéli', '1'),
        ];

        /** @var list<UserType> $next */
        $next = [
            $this->user(1, 'joseph', '1'),
            $this->user(2, 'vanessa', '2'),
            $this->user(5, 'allon', '2'),
            $this->user(6, 'marc', '2'),
            $this->user(7, 'paul', '2'),
        ];

        $results = diff(
            $next,
            $original,
            /**
             * @param UserType $a
             * @param UserType $b
             */
            function (array $a, array $b): bool {
                return $a['id'] === $b['id'];
            },
            /**
             * @param UserType $a
             * @param UserType $b
             */
            function (array $a, array $b): bool {
                return $a['token'] === $b['token'];
            },
        );

        $this->assertEquals([
            $this->user(5, 'allon', '2'),
            $this->user(6, 'marc', '2'),
            $this->user(7, 'paul', '2'),
        ], $results->toCreate());

        $this->assertEquals([
            $this->user(3, 'yona', '1'),
            $this->user(4, 'naéli', '1'),
        ], $results->toDelete());

        $this->assertEquals([
            $this->user(2, 'vanessa', '2'),
        ], $results->toUpdate());

        $this->assertEquals([
            $this->user(1, 'joseph', '1'),
        ], $results->toStay());
    }

    public function testDiff_withDifferentTypes()
    {
        $original = [
            $this->user(1, 'joseph', '1'),
            $this->user(2, 'vanessa', '1'),
            $this->user(3, 'yona', '1'),
            $this->user(4, 'naéli', '1'),
        ];

        $next = [1, 2, 5, 6, 7,];

        $results = diff(
            $next,
            $original,
            /** @param UserType $user */
            function (int $id, array $user): bool {
                return $user['id'] === $id;
            },
            /** @param UserType $user */
            function (int $id, array $user): bool {
                return $user['id'] === $id;
            }
        );

        $this->assertEquals([5, 6, 7,], $results->toCreate());

        $this->assertEquals([
            $this->user(3, 'yona', '1'),
            $this->user(4, 'naéli', '1'),
        ], $results->toDelete());

        $this->assertEquals([], $results->toUpdate());

        $this->assertEquals([1, 2], $results->toStay());
    }

    /** @return UserType */
    private function user(int $id, string $name, string $token): array
    {
        return ['id' => $id, 'name' => $name, 'token' => $token];
    }
}
