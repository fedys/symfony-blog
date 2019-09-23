<?php

namespace App\Tests\Functional\Front;

use App\Entity\Post;
use App\Tests\Functional\Traits\AdminTrait;
use App\Tests\Functional\Traits\ClientTrait;
use App\Tests\Functional\Traits\PostTrait;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PostControllerTest extends WebTestCase
{
    use ClientTrait;
    use PostTrait;
    use AdminTrait;

    public function testList()
    {
        $first = $this->createPost('first');
        $first->setDate(new DateTime('2019-09-23'));
        $second = $this->createPost('second');
        $second->setDate(new DateTime('2019-09-22'));
        $third = $this->createPost('third');
        $third->setDate(new DateTime('2019-09-21'));
        $this->persistAndFlush([$first, $second, $third]);

        $crawler = $this->client->request('GET', '/');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Blog');
        $this->assertSame($first->getTitle(), $crawler->filter('h2')->eq(0)->text());
        $this->assertSame($second->getTitle(), $crawler->filter('h2')->eq(1)->text());
        $this->assertSame(0, $crawler->filter('h2')->eq(2)->count());
        $this->assertSame(0, $crawler->filter('a:contains("Newer posts")')->count());
        $this->assertSame(1, $crawler->filter('a:contains("Older posts")')->count());

        $crawler = $this->client->request('GET', '/2');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Blog');
        $this->assertSame($third->getTitle(), $crawler->filter('h2')->eq(0)->text());
        $this->assertSame(0, $crawler->filter('h2')->eq(1)->count());
        $this->assertSame(1, $crawler->filter('a:contains("Newer posts")')->count());
        $this->assertSame(0, $crawler->filter('a:contains("Older posts")')->count());

        $this->client->request('GET', '/3');
        $this->assertResponseStatusCodeSame(404);
    }

    public function testListEmpty()
    {
        $this->client->request('GET', '/');
        $this->assertEmptyList();
    }

    public function testListDisabledPostCannotBeSeen()
    {
        $post = $this->createPost();
        $post->setEnabled(false);
        $this->persistAndFlush([$post]);

        $this->client->request('GET', '/');
        $this->assertEmptyList();
    }

    public function testListDisabledPostCanBeSeenByAdmin()
    {
        $post = $this->createPost();
        $post->setEnabled(false);
        $this->persistAndFlush([$post]);

        $this->logInAdmin($this->client);
        $this->client->request('GET', '/');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Blog');
        $this->assertSelectorTextContains('h2', $post->getTitle());
    }

    public function testDetail()
    {
        $post = $this->createPost();
        $this->persistAndFlush([$post]);
        $views = $post->getViews();

        $this->client->request('GET', $post->getUrl());
        $this->assertDetail($post);
        $this->assertSame($views + 1, $post->getViews());
    }

    public function testDetailDisabledPostCannotBeSeen()
    {
        $post = $this->createPost();
        $post->setEnabled(false);
        $this->persistAndFlush([$post]);

        $this->client->request('GET', $post->getUrl());
        $this->assertResponseStatusCodeSame(404);
    }

    public function testDetailDisabledPostCanBeSeenByAdmin()
    {
        $post = $this->createPost();
        $post->setEnabled(false);
        $this->persistAndFlush([$post]);
        $views = $post->getViews();

        $this->logInAdmin($this->client);
        $this->client->request('GET', $post->getUrl());
        $this->assertDetail($post);
        $this->assertSame($views, $post->getViews());
    }

    private function assertEmptyList(): void
    {
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Blog');
        $this->assertSelectorNotExists('h2');
    }

    /**
     * @param Post $post
     */
    private function assertDetail(Post $post): void
    {
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', $post->getTitle());
        $this->assertSelectorTextContains('.text-muted', $post->getDate()->format('m/d/Y'));
        $this->assertSelectorTextContains('.mt-5', $post->getText());
        $this->assertSelectorTextContains('.mt-3', sprintf('Tags: %s', implode(', ', $post->getTags())));
    }
}
