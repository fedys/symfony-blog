<?php

namespace App\Controller\Front;

use App\Collection\PostCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * @Route(name="post_")
 */
class PostController extends AbstractController
{
    /**
     * @var PostCollection
     */
    private $postCollection;

    /**
     * @param PostCollection                $postCollection
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(PostCollection $postCollection, AuthorizationCheckerInterface $authorizationChecker)
    {
        $enabled = $authorizationChecker->isGranted('ROLE_ADMIN') ? null : true;
        $this->postCollection = $postCollection->setEnabled($enabled);
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
        $posts = $this->postCollection->setOrder(['date DESC', 'id DESC'])
            ->getPager()
            ->setNormalizeOutOfRangePages(false)
            ->setMaxPerPage(2)
            ->setCurrentPage($page);

        return $this->render('front/post/list.html.twig', [
            'posts' => $posts,
        ]);
    }
}
