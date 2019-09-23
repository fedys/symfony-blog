<?php

namespace App\Tests\Functional\Traits;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;

trait ClientTrait
{
    /**
     * @var KernelBrowser
     */
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }
}
