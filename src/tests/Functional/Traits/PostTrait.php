<?php

namespace App\Tests\Functional\Traits;

use App\Entity\Post;

trait PostTrait
{
    use DbTrait;

    protected function tearDown(): void
    {
        $this->truncate([Post::class]);

        parent::tearDown();
    }

    /**
     * @param string $suffix
     *
     * @return Post
     */
    private function createPost(string $suffix = 'first'): Post
    {
        $post = new Post();
        $post->setTitle(sprintf('Title %s', $suffix));
        $post->setText(sprintf('Text %s', $suffix));
        $post->setUrl(sprintf('/title-%s', $suffix));
        $post->setTags([
            sprintf('tag %s', $suffix),
            sprintf('another tag %s', $suffix),
        ]);
        $post->setEnabled(true);

        return $post;
    }
}
