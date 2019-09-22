<?php

namespace App\Tests\Functional\Traits;

use App\Entity\Post;

trait PostTrait
{
    use DbTrait;

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        $this->truncateEntities([Post::class]);

        parent::tearDown();
    }
}
