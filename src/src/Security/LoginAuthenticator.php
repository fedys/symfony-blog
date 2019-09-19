<?php

namespace App\Security;

use App\Form\LoginType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;

class LoginAuthenticator extends AbstractFormLoginAuthenticator
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @param FormFactoryInterface         $formFactory
     * @param RouterInterface              $router
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(FormFactoryInterface $formFactory, RouterInterface $router, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->formFactory = $formFactory;
        $this->router = $router;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(Request $request): bool
    {
        return $request->isMethod(Request::METHOD_POST)
            && $request->getPathInfo() === $this->router->generate('admin_security_login');
    }

    /**
     * {@inheritdoc}
     */
    public function getCredentials(Request $request): ?array
    {
        $form = $this->formFactory->create(LoginType::class);
        $form->handleRequest($request);

        if (!$form->isValid()) {
            throw new AuthenticationException();
        }

        $data = $form->getData();

        // keep last username (it is rendered in the login form in case of an invalid login attempt)
        $request->getSession()->set(Security::LAST_USERNAME, $data['username']);

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getUser($credentials, UserProviderInterface $userProvider): ?UserInterface
    {
        return $userProvider->loadUserByUsername($credentials['username']);
    }

    /**
     * {@inheritdoc}
     */
    public function checkCredentials($credentials, UserInterface $user): bool
    {
        return $this->passwordEncoder->isPasswordValid($user, $credentials['password']);
    }

    /**
     * {@inheritdoc}
     */
    protected function getLoginUrl(): string
    {
        return $this->router->generate('admin_security_login');
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): Response
    {
        return new RedirectResponse($this->router->generate('admin_dashboard'));
    }
}
