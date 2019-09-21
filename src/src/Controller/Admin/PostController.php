<?php

namespace App\Controller\Admin;

use App\Collection\PostCollection;
use App\Entity\Post;
use App\Form\PostType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="post", name="post_")
 */
class PostController extends AbstractController
{
    /**
     * @var PostCollection
     */
    private $postCollection;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @param PostCollection         $postCollection
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(PostCollection $postCollection, EntityManagerInterface $entityManager)
    {
        $this->postCollection = $postCollection->setEnabled(null);
        $this->entityManager = $entityManager;
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
        $posts = $this->postCollection->getPager()
            ->setCurrentPage($page);

        return $this->render('admin/post/list.html.twig', [
            'posts' => $posts,
        ]);
    }

    /**
     * @Route(path="/insert", name="insert")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function insert(Request $request): Response
    {
        return $this->form($request, new Post());
    }

    /**
     * @Route(path="/update/{id<\d+>}", name="update")
     *
     * @param Request $request
     * @param int     $id
     *
     * @return Response
     */
    public function update(Request $request, int $id): Response
    {
        $post = $this->postCollection->find($id);

        if (!$post) {
            throw $this->createNotFoundException();
        }

        return $this->form($request, $post);
    }

    /**
     * @param Request $request
     * @param Post    $post
     *
     * @return Response
     */
    private function form(Request $request, Post $post): Response
    {
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($post);
            $this->entityManager->flush();
            $this->addFlash('success', sprintf('The post "%s" has been successfully saved.', $post->getTitle()));

            return $this->redirectToRoute('admin_post_update', ['id' => $post->getId()]);
        }

        return $this->render('admin/post/form.html.twig', [
            'form' => $form->createView(),
            'post' => $post,
        ]);
    }
}
