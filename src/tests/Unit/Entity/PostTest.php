<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Post;
use PHPUnit\Framework\TestCase;

class PostTest extends TestCase
{
    /**
     * @var Post
     */
    private $post;

    protected function setUp()
    {
        $this->post = new Post();
    }

    public function testIncViews()
    {
        $this->assertSame(0, $this->post->getViews());
        $this->assertFalse($this->post->isEnabled());
        $this->assertFalse($this->post->incViews());
        $this->assertSame(0, $this->post->getViews());

        $this->post->setEnabled(true);
        $this->assertTrue($this->post->isEnabled());
        $this->assertTrue($this->post->incViews());
        $this->assertSame(1, $this->post->getViews());
    }
}
