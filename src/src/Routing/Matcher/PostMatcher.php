<?php

namespace App\Routing\Matcher;

use App\Collection\PostCollection;
use App\Controller\Front\PostController;
use App\Routing\UrlGenerator\PostUrlGenerator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\RequestMatcherInterface;

class PostMatcher implements RequestMatcherInterface
{
    /**
     * @var PostCollection
     */
    private $postCollection;

    /**
     * @param PostCollection $postCollection
     */
    public function __construct(PostCollection $postCollection)
    {
        $this->postCollection = $postCollection;
    }

    /**
     * {@inheritdoc}
     */
    public function matchRequest(Request $request)
    {
        $pathInfo = $request->getPathInfo();
        $post = $this->postCollection->setEnabled(null)
            ->setUrl($pathInfo)
            ->getOne(true);

        if (!$post) {
            throw new ResourceNotFoundException(sprintf('No route found for "%s".', $pathInfo));
        }

        return [
            '_route' => PostUrlGenerator::ROUTE_DETAIL,
            '_controller' => PostController::class.'::detail',
            'post' => $post,
        ];
    }
}
