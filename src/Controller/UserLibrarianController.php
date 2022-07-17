<?php
declare(strict_types=1);

namespace App\Controller;

use App\Controller\AbstractController;
use App\Entity\Contracts\UserInterface;
use App\Form\Type\User\LibrarianUserType;
use App\Service\Manager\UserManager;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="/user/librarian")
 */
class UserLibrarianController extends AbstractController
{
    protected UserManager $userManager;

    public function __construct(
        UserManager $userManager
    ) {
        $this->userManager = $userManager;
    }

    /**
     * @Route(path="/list", name="librarian.list")
     */
    public function index(Request $request): Response
    {
        $filter = array_merge($request->query->all(), ['role' => UserInterface::ROLE_LIBRARIAN]);
        $users  = $this->userManager->paginate($filter);

        return $this->render(
            'default/user/librarian/index.html.twig',
            [
                'users' => $users,
            ]
        );
    }

    /**
     * @Route(path="/add", name="librarian.add")
     */
    public function add(Request $request): Response
    {
        $form = $this->userManager->form(LibrarianUserType::class);
        if ($request->isMethod(Request::METHOD_POST) &&
            !($errors = $this->userManager->handleForm($form, $request))
        ) {
            $data         = $form->getData();
            $data['role'] = UserInterface::ROLE_LIBRARIAN;
            $this->userManager->create($data);

            return $this->redirectToRoute('librarian.list');
        }

        return $this->render(
            'default/user/librarian/add.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route(path="/{id}/edit", name="librarian.edit")
     */
    public function edit(Request $request, int $id): Response
    {
        $data = $this->userManager->get($id);
        unset($data['password']);
        $form = $this->userManager->form(LibrarianUserType::class, $data ?? [], ['id' => $id]);
        if ($request->isMethod(Request::METHOD_POST) &&
            !($errors = $this->userManager->handleForm($form, $request))
        ) {
            $data = $form->getData();
            $this->userManager->update($id, $data);

            return $this->redirectToRoute('librarian.list');
        }

        return $this->render(
            'default/user/librarian/edit.html.twig',
            [
                'id'   => $id,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route(path="/{id}/delete", name="librarian.delete")
     */
    public function delete(int $id): RedirectResponse
    {
        $this->userManager->delete($id);

        return $this->redirectToReferer('librarian.list');
    }

}
