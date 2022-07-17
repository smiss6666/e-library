<?php

declare(strict_types=1);

namespace App\Security\Authenticator;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class UserAuthenticator extends AbstractFormLoginAuthenticator
{
    use TargetPathTrait;

    protected RouterInterface              $router;
    protected EntityManagerInterface       $em;
    protected UserPasswordEncoderInterface $passwordEncoder;

    /**
     * UserAuthenticator constructor.
     * @param RouterInterface $router
     * @param EntityManagerInterface $em
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(
        RouterInterface $router,
        EntityManagerInterface $em,
        UserPasswordEncoderInterface $passwordEncoder
    ) {
        $this->router = $router;
        $this->em = $em;
        $this->passwordEncoder = $passwordEncoder;
    }


    protected function getLoginUrl()
    {
        return $this->router->generate('login');
    }

    public function supports(Request $request)
    {
        return 'login' === $request->attributes->get('_route') && $request->isMethod('POST');
    }

    public function getCredentials(Request $request)
    {
        $credentials = [
            'username' => $request->request->get('_username'),
            'password' => $request->request->get('_password'),
        ];

        $request->getSession()->set(Security::LAST_USERNAME, $credentials['username']);

        if (empty($credentials['username']) || empty($credentials['password'])) {
            throw new AuthenticationException('ERROR.NO_CREDENTIALS');
        }

        return $credentials;
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $user = $this->em->getRepository(User::class)->loadUserByUsername($credentials['username']);
        if (!$user) {
            $e = new UsernameNotFoundException('ERROR.USER_NOT_FOUND');
            $e->setUsername($credentials['username']);

            throw $e;
        }

        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        if ($this->passwordEncoder->isPasswordValid($user, $credentials['password'])) {
            return true;
        }

        throw new AuthenticationException('ERROR.WRONG_PASSWORD');
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        // remember last username
        if ($request->hasSession()) {
            $request->getSession()->set(Security::LAST_USERNAME, $request->get('_username'));
        }

        // call parent
        return parent::onAuthenticationFailure($request, $exception);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
//        if ($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
//            return new RedirectResponse($targetPath);
//        }

        return new RedirectResponse($this->router->generate('homepage'));
    }
}
