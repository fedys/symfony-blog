<?php

namespace App\Controller\Front;

use App\Collection\PostCollection;
use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
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
     * @var bool
     */
    private $isAdmin;

    /**
     * @param PostCollection                $postCollection
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(PostCollection $postCollection, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->isAdmin = $authorizationChecker->isGranted('ROLE_ADMIN');
        $this->postCollection = $postCollection->setEnabled($this->isAdmin ? null : true);
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

    /**
     * @param Post                   $post
     * @param EntityManagerInterface $entityManager
     *
     * @return Response
     */
    public function detail(Post $post, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isAdmin) {
            if (!$post->isEnabled()) {
                throw $this->createNotFoundException();
            }

            if ($post->incViews()) {
                $entityManager->flush();
            }
        }

        return $this->render('front/post/detail.html.twig', [
            'post' => $post,
        ]);
    }
}
