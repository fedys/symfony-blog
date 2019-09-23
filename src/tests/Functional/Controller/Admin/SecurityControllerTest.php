<?php

namespace App\Tests\Functional\Admin;

use App\Tests\Functional\Traits\AdminTrait;
use App\Tests\Functional\Traits\ClientTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\DomCrawler\Form;

class SecurityControllerTest extends WebTestCase
{
    use ClientTrait;
    use AdminTrait;

    public function testLoginPage()
    {
        $this->client->request('GET', '/admin/login');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('.card-header', 'Admin login');
    }

    public function testLoginPageAlreadyLoggedIn()
    {
        $this->logInAdmin($this->client);
        $this->client->request('GET', '/admin/login');
        $this->assertResponseRedirects('/admin');
    }

    public function testLoginSubmitEmpty()
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', '/admin/login');
        $form = $this->getForm($crawler);
        $this->client->submit($form);

        $this->assertSelectorTextContains('.alert-danger', 'Username could not be found.');
    }

    public function testLoginInvalidUsername()
    {
        $data = [
            'username' => 'invalid'.$_SERVER['TEST_ADMIN_USERNAME'],
            'password' => $_SERVER['TEST_ADMIN_PASSWORD'],
        ];

        $this->client->followRedirects();
        $crawler = $this->client->request('GET', '/admin/login');
        $form = $this->getForm($crawler);
        $form['login[username]']->setValue($data['username']);
        $form['login[password]']->setValue($data['password']);
        $this->client->submit($form);

        $this->assertSelectorTextContains('.alert-danger', 'Username could not be found.');
        $this->assertInputValueSame('login[username]', $data['username']);
        $this->assertInputValueSame('login[password]', '');
    }

    public function testLoginSubmitInvalidPassword()
    {
        $data = [
            'username' => $_SERVER['TEST_ADMIN_USERNAME'],
            'password' => 'invalid'.$_SERVER['TEST_ADMIN_PASSWORD'],
        ];

        $this->client->followRedirects();
        $crawler = $this->client->request('GET', '/admin/login');
        $form = $this->getForm($crawler);
        $form['login[username]']->setValue($data['username']);
        $form['login[password]']->setValue($data['password']);
        $this->client->submit($form);

        $this->assertSelectorTextContains('.alert-danger', 'Invalid credentials.');
        $this->assertInputValueSame('login[username]', $data['username']);
        $this->assertInputValueSame('login[password]', '');
    }

    public function testLoginSubmitValid()
    {
        $crawler = $this->client->request('GET', '/admin/login');
        $form = $this->getForm($crawler);
        $form['login[username]']->setValue($_SERVER['TEST_ADMIN_USERNAME']);
        $form['login[password]']->setValue($_SERVER['TEST_ADMIN_PASSWORD']);

        $this->client->submit($form);
        $this->assertResponseRedirects('/admin');
    }

    public function testLogout()
    {
        $this->logInAdmin($this->client);

        $this->client->request('GET', '/admin');
        $this->assertResponseRedirects('/admin/post');

        $this->client->followRedirects();
        $this->client->request('GET', '/admin/logout');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Blog');
        $this->client->followRedirects(false);

        $this->client->request('GET', '/admin');
        $this->assertResponseRedirects('/admin/login');
    }

    /**
     * @param Crawler $crawler
     *
     * @return Form
     */
    private function getForm(Crawler $crawler): Form
    {
        $formCrawler = $crawler->filter('form[name=login]');
        $this->assertSame(1, $formCrawler->count());

        return $formCrawler->form();
    }
}
