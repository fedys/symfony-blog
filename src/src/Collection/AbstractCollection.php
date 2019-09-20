<?php

namespace App\Collection;

use App\Collection\Exception\NonUniqueException;
use App\Collection\Exception\NotFoundException;
use ArrayIterator;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

abstract class AbstractCollection implements CollectionInterface
{
    use CollectionTrait;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var array|null
     */
    protected $order;

    /**
     * @var array|null
     */
    private $excludeIds;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->init();
    }

    /**
     * {@inheritdoc}
     */
    public function find($id): ?object
    {
        $collection = $this->setOrder(null);
        $queryBuilder = $collection->getQueryBuilder();
        $this->andWhere($queryBuilder, 'id', $id);

        try {
            return $queryBuilder->getQuery()->getSingleResult();
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * {@inheritdoc}
     *
     * @return AbstractCollection
     */
    public function setOrder(?array $order): CollectionInterface
    {
        return $this->setProperty('order', $order);
    }

    /**
     * {@inheritdoc}
     */
    public function getPager(): Pagerfanta
    {
        return (new Pagerfanta($this->getPagerAdapter()))
            ->setNormalizeOutOfRangePages(true)
            ->setMaxPerPage(20);
    }

    /**
     * {@inheritdoc}
     */
    public function getOne(bool $unique = false, $throwException = false): ?object
    {
        $count = 0;
        $length = $unique ? 2 : 1;

        foreach ($this->getSlice(0, $length) as $item) {
            ++$count;
        }

        if ($unique && $count > 1) {
            throw new NonUniqueException();
        }

        if (!isset($item) && $throwException) {
            throw new NotFoundException();
        }

        return $item ?? null;
    }

    /**
     * {@inheritdoc}
     */
    public function getSlice(int $offset, int $length): array
    {
        return $this->getPagerAdapter()
            ->getSlice($offset, $length)
            ->getArrayCopy();
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        $result = $this->getQuery()
            ->getResult();

        return new ArrayIterator($result);
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return (new Paginator($this->getQuery(), false))->count();
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return $this->getQuery()
            ->getResult();
    }

    /**
     * @param array|null $excludeIds
     *
     * @return AbstractCollection
     */
    public function setExcludeIds(?array $excludeIds): CollectionInterface
    {
        return $this->setProperty('excludeIds', $excludeIds);
    }

    /**
     * Creates a new query builder.
     *
     * @return QueryBuilder
     */
    abstract protected function createQueryBuilder(): QueryBuilder;

    /**
     * Hook method.
     */
    protected function init(): void
    {
    }

    /**
     * Hook method.
     *
     * @param QueryBuilder $queryBuilder
     */
    protected function setupQueryBuilder(QueryBuilder $queryBuilder): void
    {
    }

    /**
     * @return QueryBuilder
     */
    protected function getQueryBuilder(): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder();

        if ($this->excludeIds) {
            $queryBuilder->andWhere($queryBuilder->expr()
                ->notIn($this->getField($queryBuilder, 'id'), $this->excludeIds));
        }

        $this->setupOrder($queryBuilder);
        $this->setupQueryBuilder($queryBuilder);

        return $queryBuilder;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param string       $field
     * @param string|null  $alias
     *
     * @return string
     */
    protected function getField(QueryBuilder $queryBuilder, string $field, string $alias = null): string
    {
        return ($alias ?? $queryBuilder->getRootAliases()[0]).'.'.$field;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param string       $field
     * @param mixed        $value
     * @param string|null  $alias
     */
    protected function andWhere(QueryBuilder $queryBuilder, string $field, $value, string $alias = null): void
    {
        $queryBuilder->andWhere($this->getField($queryBuilder, $field, $alias).' = :'.$field)
            ->setParameter($field, $value);
    }

    /**
     * @param QueryBuilder $queryBuilder
     */
    private function setupOrder(QueryBuilder $queryBuilder): void
    {
        if (isset($this->order)) {
            foreach ($this->order as $value) {
                if ($value instanceof Expr\OrderBy) {
                    $queryBuilder->addOrderBy($value);
                } else {
                    $parts = preg_split('/\s+/', trim($value));
                    $sort = preg_match('/\./', $parts[0]) ? $parts[0] : $this->getField($queryBuilder, $parts[0]);
                    $order = isset($parts[1]) ? $parts[1] : null;

                    $queryBuilder->addOrderBy($sort, $order);
                }
            }
        }
    }

    /**
     * @return DoctrineORMAdapter
     */
    private function getPagerAdapter(): DoctrineORMAdapter
    {
        return new DoctrineORMAdapter($this->getQuery(), false);
    }

    /**
     * @return Query
     */
    private function getQuery(): Query
    {
        return $this->getQueryBuilder()->getQuery();
    }
}
