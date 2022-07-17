<?php

declare(strict_types=1);

namespace App\Security\UserChecker;

use App\Entity\User;
use Symfony\Component\Security\Core\Exception\LockedException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserChecker implements UserCheckerInterface
{

    private TranslatorInterface $translator;

    public function __construct(
        TranslatorInterface $translator
    ) {
        $this->translator = $translator;
    }

    public function checkPreAuth(UserInterface $user): void
    {
        $this->checkLoginAllowed($user);
    }

    public function checkPostAuth(UserInterface $user): void
    {
        $this->checkLoginAllowed($user);
    }

    protected function checkLoginAllowed(UserInterface $user): void
    {
        if (!$user instanceof User) {
            return;
        }

        if (!$user->isActive()) {
            throw new LockedException(
                $this->translator->trans('ERROR.USER_LOCKED', ['{{ username }}' => $user->getUsername()], 'security')
            );
        }
    }
}
