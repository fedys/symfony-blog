<?php

namespace App\Controller\Admin;

use App\Form\LoginType;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * @Route(name="security_")
 */
class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="login")
     *
     * @param AuthenticationUtils $authenticationUtils
     *
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $form = $this->createForm(LoginType::class, [
            'username' => $authenticationUtils->getLastUsername(),
        ]);

        return $this->render('admin/security/login.html.twig', [
            'form' => $form->createView(),
            'error' => $authenticationUtils->getLastAuthenticationError(),
        ]);
    }

    /**
     * @Route("/logout", name="logout")
     *
     * @throws LogicException
     */
    public function logout(): void
    {
        throw new LogicException('This should not be reached!');
    }
}
