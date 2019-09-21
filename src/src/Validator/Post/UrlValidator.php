<?php

namespace App\Validator\Post;

use App\Collection\PostCollection;
use App\Entity\Post;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class UrlValidator extends ConstraintValidator
{
    /**
     * @var UrlMatcherInterface
     */
    private $router;

    /**
     * @var PostCollection
     */
    private $postCollection;

    /**
     * @param UrlMatcherInterface $router
     * @param PostCollection      $postCollection
     */
    public function __construct(UrlMatcherInterface $router, PostCollection $postCollection)
    {
        $this->router = $router;
        $this->postCollection = $postCollection;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($post, Constraint $constraint)
    {
        if (!$post instanceof Post) {
            throw new UnexpectedTypeException($post, Post::class);
        }

        $url = $post->getUrl();

        if (!$url) {
            return;
        }

        if (!preg_match('~^/[a-z0-9-/]*[a-z0-9-]$~', $url, $matches)) {
            $this->addViolation('The URL "{{ string }}" must begin with a slash and may contain lowercase alphanumeric characters, slashes and hyphens.', $url);

            return;
        }

        if ($this->existsDefaultRouterUrl($post)) {
            $this->addViolation('The URL "{{ string }}" clashes with a system URL.', $url);

            return;
        }

        if ($this->existsPostUrl($post)) {
            $this->addViolation('The URL "{{ string }}" is already used by another post.', $url);

            return;
        }
    }

    /**
     * @param Post $post
     *
     * @return bool
     */
    private function existsDefaultRouterUrl(Post $post): bool
    {
        try {
            $this->router->match($post->getUrl());

            return true;
        } catch (ResourceNotFoundException $e) {
            return false;
        }
    }

    /**
     * @param Post $post
     *
     * @return bool
     */
    private function existsPostUrl(Post $post): bool
    {
        $postCollection = $this->postCollection->setEnabled(null)
            ->setUrl($post->getUrl());
        $id = $post->getId();

        if ($id) {
            $postCollection = $postCollection->setExcludeIds([$id]);
        }

        return $postCollection->count() > 0;
    }

    /**
     * @param string $message
     * @param string $url
     */
    private function addViolation(string $message, string $url): void
    {
        $this->context->buildViolation($message)
            ->atPath('url')
            ->setParameter('{{ string }}', $url)
            ->addViolation();
    }
}
