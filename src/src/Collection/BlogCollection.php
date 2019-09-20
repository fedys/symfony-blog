<?php

namespace App\Collection;

use App\Entity\Blog;
use Doctrine\ORM\QueryBuilder;

/**
 * @method Blog|null find(mixed $id)
 */
class BlogCollection extends AbstractCollection
{
    use CollectionTrait;

    /**
     * @var string
     */
    const ROOT_ALIAS = 'Blog';

    /**
     * @var bool|null
     */
    private $enabled = true;

    /**
     * {@inheritdoc}
     */
    protected function init(): void
    {
        $this->order = ['id DESC'];
    }

    /**
     * @param bool|null $enabled
     *
     * @return BlogCollection
     */
    public function setEnabled(?bool $enabled): BlogCollection
    {
        return $this->setProperty('enabled', $enabled);
    }

    /**
     * {@inheritdoc}
     */
    protected function createQueryBuilder(): QueryBuilder
    {
        return $this->entityManager->createQueryBuilder()
            ->select(self::ROOT_ALIAS)
            ->from(Blog::class, self::ROOT_ALIAS);
    }

    /**
     * {@inheritdoc}
     */
    protected function setupQueryBuilder(QueryBuilder $queryBuilder): void
    {
        if (isset($this->enabled)) {
            $this->andWhere($queryBuilder, 'enabled', (int) (bool) $this->enabled);
        }
    }
}
