<?php

namespace App\Tests\Functional\Admin;

use App\Entity\Post;
use App\Tests\Functional\Traits\AdminTrait;
use App\Tests\Functional\Traits\ClientTrait;
use App\Tests\Functional\Traits\FormTrait;
use App\Tests\Functional\Traits\PostTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\DomCrawler\Form;

class PostControllerTest extends WebTestCase
{
    use ClientTrait;
    use PostTrait;
    use AdminTrait;
    use FormTrait;

    public function testListUnauthenticated()
    {
        $this->client->request('GET', '/admin/post');
        $this->assertResponseRedirects('/admin/login');
    }

    public function testList()
    {
        $first = $this->createPost('first');
        $second = $this->createPost('second');
        $second->setEnabled(false);
        $third = $this->createPost('third');
        $third->setViews(35);
        $posts = [$first, $second, $third];
        $this->persistAndFlush($posts);

        $this->logInAdmin($this->client);
        $crawler = $this->client->request('GET', '/admin/post');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('.card-header', 'Post - list');

        $tableCrawler = $crawler->filter('table.table');
        $this->assertSame(1, $tableCrawler->count());

        $rowCrawler = $tableCrawler->filterXPath('.//tbody/tr');
        $this->assertSame(count($posts), $rowCrawler->count());

        foreach (array_reverse($posts) as $index => $post) {
            $this->assertPostRow($rowCrawler->eq($index), $post);
        }
    }

    public function testInsertUnauthenticated()
    {
        $this->client->request('GET', '/admin/post/insert');
        $this->assertResponseRedirects('/admin/login');
    }

    public function testInsertEmptyData()
    {
        $this->logInAdmin($this->client);
        $crawler = $this->client->request('GET', '/admin/post/insert');
        $form = $this->getForm($crawler);
        $crawler = $this->client->submit($form);
        $formCrawler = $crawler->filter('form[name=post]');

        $expectedErrors = ['This value should not be blank.'];
        $this->assertFormElementErrors($formCrawler, 'post[title]', $expectedErrors);
        $this->assertFormElementErrors($formCrawler, 'post[url]', $expectedErrors);
        $this->assertFormElementErrors($formCrawler, 'post[text]', $expectedErrors);
    }

    public function testInsertUrlClashingSystemOne()
    {
        $systemUrl = '/admin';
        $this->logInAdmin($this->client);
        $crawler = $this->client->request('GET', '/admin/post/insert');
        $form = $this->getForm($crawler);
        $form['post[url]']->setValue($systemUrl);
        $crawler = $this->client->submit($form);
        $formCrawler = $crawler->filter('form[name=post]');

        $expectedErrors = [sprintf('The URL "%s" clashes with a system URL.', $systemUrl)];
        $this->assertFormElementErrors($formCrawler, 'post[url]', $expectedErrors);
    }

    public function testInsertDuplicateUrl()
    {
        $post = $this->createPost();
        $this->persistAndFlush([$post]);

        $this->logInAdmin($this->client);
        $crawler = $this->client->request('GET', '/admin/post/insert');
        $form = $this->getForm($crawler);
        $form['post[url]']->setValue($post->getUrl());
        $crawler = $this->client->submit($form);
        $formCrawler = $crawler->filter('form[name=post]');

        $expectedErrors = [sprintf('The URL "%s" is already used by another post.', $post->getUrl())];
        $this->assertFormElementErrors($formCrawler, 'post[url]', $expectedErrors);
    }

    public function testInsertValid()
    {
        $data = [
            'post[title]' => 'Some title',
            'post[url]' => '/some-title',
            'post[date]' => '2019-09-23',
            'post[text]' => '<p>Some text.</p>',
            'post[tags]' => 'first, second',
        ];

        $this->logInAdmin($this->client);
        $crawler = $this->client->request('GET', '/admin/post/insert');
        $form = $this->getForm($crawler);

        foreach ($data as $name => $value) {
            $form[$name]->setValue($value);
        }

        $this->client->followRedirects();
        $crawler = $this->client->submit($form);
        $successMessage = sprintf('The post "%s" has been successfully saved.', $data['post[title]']);
        $this->assertSelectorTextContains('.alert-success', $successMessage);
        $form = $this->getForm($crawler);

        foreach ($data as $name => $value) {
            $this->assertSame($data[$name], $form[$name]->getValue());
        }
    }

    public function testUpdateUnauthenticated()
    {
        $this->client->request('GET', '/admin/post/update/1');
        $this->assertResponseRedirects('/admin/login');
    }

    public function testUpdateNonExistent()
    {
        $this->logInAdmin($this->client);
        $this->client->request('GET', '/admin/post/update/1');
        $this->assertResponseStatusCodeSame(404);
    }

    public function testUpdateDuplicateUrl()
    {
        $otherPost = $this->createPost('other');
        $post = $this->createPost('update');
        $this->persistAndFlush([$otherPost, $post]);

        $this->logInAdmin($this->client);
        $crawler = $this->client->request('GET', sprintf('/admin/post/update/%d', $post->getId()));
        $form = $this->getForm($crawler);
        $form['post[url]']->setValue($otherPost->getUrl());
        $crawler = $this->client->submit($form);
        $formCrawler = $crawler->filter('form[name=post]');

        $expectedErrors = [sprintf('The URL "%s" is already used by another post.', $otherPost->getUrl())];
        $this->assertFormElementErrors($formCrawler, 'post[url]', $expectedErrors);
    }

    public function testUpdateValid()
    {
        $post = $this->createPost('update');
        $this->persistAndFlush([$post]);

        $data = [
            'post[title]' => 'Another title',
            'post[url]' => '/another-title',
            'post[date]' => '2019-09-22',
            'post[text]' => '<p>Another text.</p>',
            'post[tags]' => 'third, fourth',
        ];

        $this->logInAdmin($this->client);
        $crawler = $this->client->request('GET', sprintf('/admin/post/update/%d', $post->getId()));
        $form = $this->getForm($crawler);

        foreach ($data as $name => $value) {
            $form[$name]->setValue($value);
        }

        $this->client->followRedirects();
        $crawler = $this->client->submit($form);
        $successMessage = sprintf('The post "%s" has been successfully saved.', $data['post[title]']);
        $this->assertSelectorTextContains('.alert-success', $successMessage);
        $form = $this->getForm($crawler);

        foreach ($data as $name => $value) {
            $this->assertSame($data[$name], $form[$name]->getValue());
        }
    }

    /**
     * @param Crawler $rowCrawler
     * @param Post    $post
     */
    private function assertPostRow(Crawler $rowCrawler, Post $post): void
    {
        $cellCrawler = $rowCrawler->filterXPath('//td[1]');
        $this->assertSame(1, $cellCrawler->count());
        $this->assertSame((string) $post->getId(), $cellCrawler->text());

        $cellCrawler = $rowCrawler->filterXPath('//td[2]');
        $this->assertSame(1, $cellCrawler->count());
        $this->assertSame($post->getTitle(), $cellCrawler->text());

        $cellCrawler = $rowCrawler->filterXPath('//td[3]');
        $this->assertSame(1, $cellCrawler->count());
        $this->assertSame($post->getDate()->format('m/d/Y'), $cellCrawler->text());

        $cellCrawler = $rowCrawler->filterXPath('//td[4]');
        $this->assertSame(1, $cellCrawler->count());
        $this->assertSame(implode(', ', $post->getTags()), $cellCrawler->text());

        $cellCrawler = $rowCrawler->filterXPath('//td[5]');
        $this->assertSame(1, $cellCrawler->count());
        $this->assertSame($post->isEnabled() ? 'Yes' : 'No', trim($cellCrawler->text()));

        $cellCrawler = $rowCrawler->filterXPath('//td[6]');
        $this->assertSame(1, $cellCrawler->count());
        $this->assertSame((string) $post->getViews(), trim($cellCrawler->text()));
    }

    /**
     * @param Crawler $crawler
     *
     * @return Form
     */
    private function getForm(Crawler $crawler): Form
    {
        $formCrawler = $crawler->filter('form[name=post]');
        $this->assertSame(1, $formCrawler->count());

        return $formCrawler->form();
    }
}
