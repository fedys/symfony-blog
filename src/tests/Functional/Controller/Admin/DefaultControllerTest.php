<?php

namespace App\Tests\Functional\Admin;

use App\Tests\Functional\Traits\AdminTrait;
use App\Tests\Functional\Traits\ClientTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    use ClientTrait;
    use AdminTrait;

    public function testDashboard()
    {
        $this->logInAdmin($this->client);
        $this->client->request('GET', '/admin');
        $this->assertResponseRedirects('/admin/post');
    }
}
