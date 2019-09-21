<?php

namespace App\Collection;

use App\Entity\Post;
use Doctrine\ORM\QueryBuilder;

/**
 * @method Post|null find(mixed $id)
 */
class PostCollection extends AbstractCollection
{
    use CollectionTrait;

    /**
     * @var string
     */
    const ROOT_ALIAS = 'Post';

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
     * @return PostCollection
     */
    public function setEnabled(?bool $enabled): PostCollection
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
            ->from(Post::class, self::ROOT_ALIAS);
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
