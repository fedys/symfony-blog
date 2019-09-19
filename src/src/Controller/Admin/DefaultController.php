<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route(path="/", name="dashboard")
     *
     * @return Response
     */
    public function dashboard(): Response
    {
        return $this->redirectToRoute('admin_blog_list', [], Response::HTTP_MOVED_PERMANENTLY);
    }
}
