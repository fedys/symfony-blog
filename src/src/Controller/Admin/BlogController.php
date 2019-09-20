<?php

namespace App\Controller\Admin;

use App\Collection\BlogCollection;
use App\Entity\Blog;
use App\Form\BlogType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="blog", name="blog_")
 */
class BlogController extends AbstractController
{
    /**
     * @var BlogCollection
     */
    private $blogCollection;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @param BlogCollection         $blogCollection
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(BlogCollection $blogCollection, EntityManagerInterface $entityManager)
    {
        $this->blogCollection = $blogCollection->setEnabled(null);
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
        $blogs = $this->blogCollection->getPager()
            ->setCurrentPage($page);

        return $this->render('admin/blog/list.html.twig', [
            'blogs' => $blogs,
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
        return $this->form($request, new Blog());
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
        $blog = $this->blogCollection->find($id);

        if (!$blog) {
            throw $this->createNotFoundException();
        }

        return $this->form($request, $blog);
    }

    /**
     * @param Request $request
     * @param Blog    $blog
     *
     * @return Response
     */
    private function form(Request $request, Blog $blog): Response
    {
        $form = $this->createForm(BlogType::class, $blog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($blog);
            $this->entityManager->flush();
            $this->addFlash('success', sprintf('Blog "%s" has been successfully saved', $blog->getTitle()));

            return $this->redirectToRoute('admin_blog_update', ['id' => $blog->getId()]);
        }

        return $this->render('admin/blog/form.html.twig', [
            'form' => $form->createView(),
            'blog' => $blog,
        ]);
    }
}
