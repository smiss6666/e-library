<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as BaseAbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

/**
 * @method User|null getUser()
 **/
abstract class AbstractController extends BaseAbstractController
{

    public function redirectToReferer(string $route): RedirectResponse
    {
        /** @var RequestStack $requestStack */
        $requestStack = $this->get('request_stack');
        $referer      = $requestStack->getMasterRequest()
            ? $requestStack->getMasterRequest()->headers->get('referer')
            : null;

        if ($referer) {
            return new RedirectResponse($referer, Response::HTTP_MOVED_PERMANENTLY);
        }

        return $this->redirectToRoute($route);
    }

}
