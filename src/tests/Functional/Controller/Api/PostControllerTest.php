<?php

namespace App\Tests\Functional\Api;

use App\Entity\Post;
use App\Tests\Functional\Traits\ClientTrait;
use App\Tests\Functional\Traits\PostTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class PostControllerTest extends WebTestCase
{
    use ClientTrait;
    use PostTrait;

    public function testList()
    {
        $first = $this->createPost('first');
        $second = $this->createPost('second');
        $third = $this->createPost('third');
        $posts = [$first, $second, $third];
        $this->persistAndFlush($posts);

        $this->client->request('GET', '/api/post');
        $data = $this->decodeJsonResponse($this->client->getResponse());
        $this->assertIsArray($data);
        $this->assertCount(count($posts), $data);

        foreach (array_reverse($posts) as $index => $post) {
            $this->assertArrayHasKey($index, $data);
            $this->assertPostData($data[$index], $post);
        }
    }

    public function testListEmpty()
    {
        $this->client->request('GET', '/api/post');
        $this->assertSame([], $this->decodeJsonResponse($this->client->getResponse()));
    }

    public function testListDisabledPostCannotBeSeen()
    {
        $post = $this->createPost();
        $post->setEnabled(false);
        $this->persistAndFlush([$post]);

        $this->client->request('GET', '/api/post');
        $this->assertSame([], $this->decodeJsonResponse($this->client->getResponse()));
    }

    public function testDetail()
    {
        $post = $this->createPost();
        $this->persistAndFlush([$post]);
        $views = $post->getViews();

        $this->client->request('GET', sprintf('/api/post/%d', $post->getId()));
        $data = $this->decodeJsonResponse($this->client->getResponse());
        $this->assertIsArray($data);
        $this->assertPostData($data, $post, true);

        $this->assertSame($views + 1, $post->getViews());
    }

    public function testDetailNonExistent()
    {
        $this->client->request('GET', '/api/post/1');
        $this->assertNull($this->decodeJsonResponse($this->client->getResponse(), 404));
    }

    public function testDetailDisabledPostCannotBeSeen()
    {
        $post = $this->createPost();
        $post->setEnabled(false);
        $this->persistAndFlush([$post]);

        $this->client->request('GET', sprintf('/api/post/%d', $post->getId()));
        $this->assertNull($this->decodeJsonResponse($this->client->getResponse(), 404));
    }

    /**
     * @param Response $response
     * @param int      $expectedStatusCode
     *
     * @return mixed
     */
    private function decodeJsonResponse(Response $response, int $expectedStatusCode = 200)
    {
        $this->assertSame($expectedStatusCode, $response->getStatusCode());
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertJson($response->getContent());

        return json_decode($response->getContent(), true);
    }

    /**
     * @param array $data
     * @param Post  $post
     * @param bool  $detail
     */
    private function assertPostData(array $data, Post $post, bool $detail = false): void
    {
        $this->assertArrayHasKey('id', $data);
        $this->assertSame($post->getId(), $data['id']);
        $this->assertArrayHasKey('title', $data);
        $this->assertSame($post->getTitle(), $data['title']);
        $this->assertArrayHasKey('date', $data);
        $this->assertSame($post->getDate()->format('c'), $data['date']);
        $this->assertArrayHasKey('url', $data);
        $this->assertSame($post->getUrl(), $data['url']);

        if ($detail) {
            $this->assertArrayHasKey('text', $data);
            $this->assertSame($post->getText(), $data['text']);
            $this->assertArrayHasKey('tags', $data);
            $this->assertSame($post->getTags(), $data['tags']);
        } else {
            $this->assertArrayNotHasKey('text', $data);
            $this->assertArrayNotHasKey('tags', $data);
        }
    }
}
