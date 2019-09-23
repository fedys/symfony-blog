<?php

namespace App\Controller\Api;

use App\Collection\PostCollection;
use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Swagger\Annotations as SWG;
use Nelmio\ApiDocBundle\Annotation\Model;

/**
 * @Route(path="post", name="post_")
 * @SWG\Tag(name="Post")
 */
class PostController extends AbstractController
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
     * @Route(path="", methods={"GET"}, name="list")
     * @SWG\Get(
     *     summary="Get a list of posts",
     *     description="Returns a list of posts",
     *     produces={"application/json"},
     *     @SWG\Response(
     *         response=200,
     *         description="Returns a list of posts.",
     *         @SWG\Schema(
     *             type="array",
     *             @SWG\Items(ref=@Model(type=App\Entity\Post::class, groups={"list"}))
     *         )
     *     )
     * )
     *
     * @return Response
     */
    public function list(): Response
    {
        return $this->jsonResponse($this->postCollection->toArray(), Post::SERIALIZER_GROUP_LIST);
    }

    /**
     * @Route(path="/{id<\d+>}", methods={"GET"}, name="detail")
     * @SWG\Get(
     *     summary="Get a post detail",
     *     description="Returns a post detail.",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         in="path",
     *         name="id",
     *         type="string",
     *         description="ID of the given post. Example: `54`"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Returns a post detail.",
     *         @Model(type=App\Entity\Post::class, groups={"detail"})
     *     ),
     *     @SWG\Response(
     *         response=404,
     *         description="Returned if the post does not exist."
     *     )
     * )
     *
     * @param int                    $id
     * @param EntityManagerInterface $entityManager
     *
     * @return Response
     */
    public function detail(int $id, EntityManagerInterface $entityManager): Response
    {
        $post = $this->postCollection->find($id);

        if (!$post) {
            return $this->json(null, Response::HTTP_NOT_FOUND);
        }

        if ($post->incViews()) {
            $entityManager->flush();
        }

        return $this->jsonResponse($post, Post::SERIALIZER_GROUP_DETAIL);
    }

    /**
     * @param mixed  $data
     * @param string $group
     *
     * @return JsonResponse
     */
    private function jsonResponse($data, string $group): JsonResponse
    {
        return $this->json($data, 200, [], ['groups' => [$group]]);
    }
}
