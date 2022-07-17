<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Contracts\UserInterface;
use App\Form\Type\User\ProfileUserType;
use App\Form\Type\User\RegisterUserType;
use App\Repository\UserRepository;
use App\Security\Authenticator\UserAuthenticator;
use App\Service\Manager\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{

    protected UserManager    $userManager;
    protected UserRepository $userRepository;

    public function __construct(
        UserManager $userManager,
        UserRepository $userRepository
    ) {
        $this->userManager    = $userManager;
        $this->userRepository = $userRepository;
    }

    /**
     * @Route("/login", name="login")
     */
    public function login(Request $request, AuthenticationUtils $authUtils): Response
    {
        $error        = $authUtils->getLastAuthenticationError();
        $lastUsername = $authUtils->getLastUsername();

        return $this->render(
            'default/security/login.html.twig',
            [
                'last_username' => $lastUsername,
                'error'         => $error,
            ]
        );
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout(): void
    {
    }

    /**
     * @Route("/register", name="register")
     */
    public function register(
        Request $request,
        UserAuthenticator $authenticator,
        GuardAuthenticatorHandler $guardHandler
    ): Response {
        $form = $this->userManager->form(RegisterUserType::class);
        if ($request->isMethod(Request::METHOD_POST) &&
            !($errors = $this->userManager->handleForm($form, $request))
        ) {
            $data         = $form->getData();
            $data['role'] = UserInterface::ROLE_READER;
            $id           = $this->userManager->create($data);
            $user         = $this->userRepository->find($id);

            // after validating the user and saving them to the database
            // authenticate the user and use onAuthenticationSuccess on the authenticator
            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,              // the User object you just created
                $request,
                $authenticator,     // authenticator whose onAuthenticationSuccess you want to use
                'main'    // the name of your firewall in security.yaml
            );
        }

        return $this->render(
            'default/security/register.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route(path="/profile", name="profile")
     */
    public function index(Request $request): Response
    {
        $id   = $this->getUser() ? (int)$this->getUser()->getId() : null;
        $user = $id ? $this->userManager->get($id) : null;
        if (!$user) {
            return $this->redirectToRoute('homepage');
        }

        unset($user['password']);
        $form = $this->userManager->form(ProfileUserType::class, $user, ['id' => $id]);
        if ($request->isMethod(Request::METHOD_POST) &&
            !($errors = $this->userManager->handleForm($form, $request))
        ) {
            $this->userManager->update($id, $form->getData());

            return $this->redirectToRoute('profile');
        }

        return $this->render('default/security/profile.html.twig',
            [
                'id'   => $id,
                'form' => $form->createView(),
            ]
        );
    }

}
