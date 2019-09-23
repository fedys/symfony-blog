<?php

namespace App\Tests\Functional\Traits;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\User;

trait AdminTrait
{
    /**
     * @param KernelBrowser $client
     */
    public function logInAdmin(KernelBrowser $client): void
    {
        $firewall = 'main';
        $session = $client->getContainer()->get('session');
        $user = new User($_SERVER['TEST_ADMIN_USERNAME'], $_SERVER['TEST_ADMIN_PASSWORD_ENCODED'], ['ROLE_ADMIN']);
        $token = new UsernamePasswordToken($user, null, $firewall, $user->getRoles());
        $session->set('_security_'.$firewall, serialize($token));
        $session->save();
        $cookie = new Cookie($session->getName(), $session->getId());
        $client->getCookieJar()->set($cookie);
    }
}
