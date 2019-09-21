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
     * @var string|null
     */
    private $url;

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
     * @param string|null $url
     *
     * @return PostCollection
     */
    public function setUrl(?string $url): PostCollection
    {
        return $this->setProperty('url', $url);
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

        if (isset($this->url)) {
            $this->andWhere($queryBuilder, 'url', $this->url);
        }
    }
}
