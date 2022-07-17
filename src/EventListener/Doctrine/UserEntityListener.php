<?php

declare(strict_types=1);

namespace App\EventListener\Doctrine;

use App\Entity\Contracts\UserInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class UserEntityListener implements EntityListenerInterface
{
    protected EncoderFactoryInterface $encoderFactory;

    public function __construct(
        EncoderFactoryInterface $encoderFactory
    ) {
        $this->encoderFactory = $encoderFactory;
    }

    public function prePersist(UserInterface $user): void
    {
        $this->updateUserPassword($user);
    }

    public function preUpdate(UserInterface $user): void
    {
        $this->updateUserPassword($user);
    }

    protected function updateUserPassword(UserInterface $user): void
    {
        $plainPassword = $user->getPlainPassword();

        if (null !== $plainPassword) {
            $encoder  = $this->encoderFactory->getEncoder($user);
            $password = $encoder->encodePassword($plainPassword, $user->getSalt());
            $user->setPassword($password);
        }
    }
}
