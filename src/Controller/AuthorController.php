<?php
declare(strict_types=1);

namespace App\Controller;

use App\Form\Type\AuthorType;
use App\Service\Manager\AuthorManager;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="/author")
 */
class AuthorController extends AbstractController
{

    protected AuthorManager $authorManager;

    public function __construct(AuthorManager $authorManager)
    {
        $this->authorManager = $authorManager;
    }

    /**
     * @Route(path="/list", name="author.list")
     */
    public function index(Request $request): Response
    {
        $filter  = $request->query->all();
        $authors = $this->authorManager->paginate($filter);

        return $this->render(
            'default/author/index.html.twig',
            [
                'authors' => $authors,
            ]
        );
    }

    /**
     * @Route(path="/add", name="author.add")
     */
    public function add(Request $request): Response
    {
        $form = $this->authorManager->form(AuthorType::class);
        if ($request->isMethod(Request::METHOD_POST) &&
            !($errors = $this->authorManager->handleForm($form, $request))
        ) {
            $data = $form->getData();
            $this->authorManager->create($data);

            return $this->redirectToRoute('author.list');
        }

        return $this->render(
            'default/author/add.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route(path="/{id}/edit", name="author.edit")
     */
    public function edit(Request $request, int $id): Response
    {
        $data = $this->authorManager->get($id);
        $form = $this->authorManager->form(AuthorType::class, $data ?? [], ['id' => $id]);
        if ($request->isMethod(Request::METHOD_POST) &&
            !($errors = $this->authorManager->handleForm($form, $request))
        ) {
            $data = $form->getData();
            $this->authorManager->update($id, $data);

            return $this->redirectToRoute('author.list');
        }

        return $this->render(
            'default/author/edit.html.twig',
            [
                'id'   => $id,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route(path="/{id}/delete", name="author.delete")
     */
    public function delete(int $id): RedirectResponse
    {
        $this->authorManager->delete($id);

        return $this->redirectToReferer('author.list');
    }

}
