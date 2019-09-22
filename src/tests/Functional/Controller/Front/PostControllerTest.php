<?php

namespace App\Tests\Functional\Front;

use App\Entity\Post;
use App\Tests\Functional\Traits\ClientTrait;
use App\Tests\Functional\Traits\PostTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PostControllerTest extends WebTestCase
{
    use ClientTrait;
    use PostTrait;

    public function testListEmpty()
    {
        $this->assertEmptyList();
    }

    public function testListWithDisabledPost()
    {
        $post = $this->createPost();
        $post->setEnabled(false);

        $entityManager = $this->getEntityManager();
        $entityManager->persist($post);
        $entityManager->flush();

        $this->assertEmptyList();
    }

    private function assertEmptyList(): void
    {
        $this->client->request('GET', '/');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Blog');
        $this->assertSelectorNotExists('h2');
    }

    /**
     * @return Post
     */
    private function createPost(): Post
    {
        $post = new Post();
        $post->setTitle('Some title');
        $post->setText('Some text');
        $post->setUrl('/some-title');
        $post->setTags(['first', 'second']);
        $post->setEnabled(true);

        return $post;
    }
}
