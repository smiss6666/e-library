<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Contracts\UserInterface;
use App\Form\Type\User\AdminUserType;
use App\Service\Manager\UserManager;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="/user/admin")
 */
class UserAdminController extends AbstractController
{

    protected UserManager $userManager;

    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * @Route(path="/list", name="admin.list")
     */
    public function index(Request $request): Response
    {
        $filter = array_merge($request->query->all(), ['role' => UserInterface::ROLE_ADMIN]);
        $users  = $this->userManager->paginate($filter);

        return $this->render(
            'default/user/admin/index.html.twig',
            [
                'users' => $users,
            ]
        );
    }

    /**
     * @Route(path="/add", name="admin.add")
     */
    public function add(Request $request): Response
    {
        $form = $this->userManager->form(AdminUserType::class);
        if ($request->isMethod(Request::METHOD_POST) &&
            !($errors = $this->userManager->handleForm($form, $request))
        ) {
            $data         = $form->getData();
            $data['role'] = UserInterface::ROLE_ADMIN;
            $this->userManager->create($data);

            return $this->redirectToRoute('admin.list');
        }

        return $this->render(
            'default/user/admin/add.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route(path="/{id}/edit", name="admin.edit")
     */
    public function edit(Request $request, int $id): Response
    {
        $data = $this->userManager->get($id);
        unset($data['password']);
        $form = $this->userManager->form(AdminUserType::class, $data ?? [], ['id' => $id]);
        if ($request->isMethod(Request::METHOD_POST) &&
            !($errors = $this->userManager->handleForm($form, $request))
        ) {
            $data = $form->getData();
            $this->userManager->update($id, $data);

            return $this->redirectToRoute('admin.list');
        }

        return $this->render(
            'default/user/admin/edit.html.twig',
            [
                'id'   => $id,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route(path="/{id}/delete", name="admin.delete")
     */
    public function delete(int $id): RedirectResponse
    {
        $this->userManager->delete($id);

        return $this->redirectToReferer('admin.list');
    }

}
