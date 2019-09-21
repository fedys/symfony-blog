<?php

namespace App\Controller\Front;

use App\Collection\BlogCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * @Route(name="blog_")
 */
class BlogController extends AbstractController
{
    /**
     * @var BlogCollection
     */
    private $blogCollection;

    /**
     * @param BlogCollection                $blogCollection
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(BlogCollection $blogCollection, AuthorizationCheckerInterface $authorizationChecker)
    {
        $enabled = $authorizationChecker->isGranted('ROLE_ADMIN') ? null : true;
        $this->blogCollection = $blogCollection->setEnabled($enabled);
    }

    /**
     * @Route(path="/{page<\d+>?1}", name="list")
     *
     * @param int $page
     *
     * @return Response
     */
    public function list(int $page = 1): Response
    {
        $blogs = $this->blogCollection->setOrder(['date DESC', 'id DESC'])
            ->getPager()
            ->setNormalizeOutOfRangePages(false)
            ->setMaxPerPage(2)
            ->setCurrentPage($page);

        return $this->render('front/blog/list.html.twig', [
            'blogs' => $blogs,
        ]);
    }
}
