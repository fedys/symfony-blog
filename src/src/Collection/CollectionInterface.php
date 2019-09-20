<?php

namespace App\Collection;

use Countable;
use IteratorAggregate;
use Pagerfanta\Pagerfanta;
use App\Collection\Exception\NonUniqueException;
use App\Collection\Exception\NotFoundException;

interface CollectionInterface extends IteratorAggregate, Countable
{
    /**
     * @param mixed $id
     *
     * @return object|null
     */
    public function find($id): ?object;

    /**
     * @param array|null $order
     *
     * @return CollectionInterface
     */
    public function setOrder(?array $order): CollectionInterface;

    /**
     * @return Pagerfanta
     */
    public function getPager(): Pagerfanta;

    /**
     * @param bool $unique
     * @param bool $throwException
     *
     * @return object|null
     *
     * @throws NonUniqueException
     * @throws NotFoundException
     */
    public function getOne(bool $unique = false, $throwException = false): ?object;

    /**
     * @param int $offset
     * @param int $length
     *
     * @return array
     */
    public function getSlice(int $offset, int $length): array;

    /**
     * @return array
     */
    public function toArray(): array;
}
