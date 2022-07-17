<?php
declare(strict_types=1);

namespace App\Controller;

use App\Form\Type\BookType;
use App\Form\Type\OrderBookType;
use App\Service\Manager\BookManager;
use App\Service\Manager\ReadingManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="/book")
 */
class BookController extends AbstractController
{

    protected BookManager    $bookManager;
    protected ReadingManager $readerManager;

    public function __construct(
        BookManager $bookManager,
        ReadingManager $readingManager
    ) {
        $this->bookManager   = $bookManager;
        $this->readerManager = $readingManager;
    }

    /**
     * @Route(path="/list", name="book.list")
     */
    public function index(Request $request): Response
    {
        $filter = $request->query->all();
        $books  = $this->bookManager->paginate($filter);

        return $this->render('default/book/index.html.twig',
            [
                'books'         => $books,
                'bookManager'   => $this->bookManager,
                'readerManager' => $this->readerManager,
            ]
        );
    }

    /**
     * @Route(path="/add", name="book.add")
     */
    public function add(Request $request): Response
    {
        $form = $this->bookManager->form(BookType::class);
        if ($request->isMethod(Request::METHOD_POST) &&
            !($errors = $this->bookManager->handleForm($form, $request))
        ) {
            $data = $form->getData();
            $this->bookManager->create($data);

            return $this->redirectToRoute('book.list');
        }

        return $this->render(
            'default/book/add.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route(path="/{id}/edit", name="book.edit")
     */
    public function edit(Request $request, int $id): Response
    {
        $data            = $this->bookManager->get($id);
        $data['authors'] = !empty($data['authors']) ? array_keys($data['authors']) : [];
        $form            = $this->bookManager->form(BookType::class, $data ?? [], ['id' => $id]);
        if ($request->isMethod(Request::METHOD_POST) &&
            !($errors = $this->bookManager->handleForm($form, $request))
        ) {
            $data = $form->getData();
            $this->bookManager->update($id, $data);

            return $this->redirectToRoute('book.list');
        }

        return $this->render(
            'default/book/edit.html.twig',
            [
                'id'   => $id,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route(path="/{id}/delete", name="book.delete")
     */
    public function delete(int $id): Response
    {
        $this->bookManager->delete($id);

        return $this->redirectToRoute('book.list');
    }

    /**
     * @Route(path="/{id}/order", name="book.order")
     */
    public function order(Request $request, int $id): Response
    {
        $userId = $this->getUser() ? $this->getUser()->getId() : null;
        $book   = $this->bookManager->get($id);
        $form   = $this->bookManager->form(OrderBookType::class, [], ['book_id' => $id, 'user_id' => $userId]);
        if ($request->isMethod(Request::METHOD_POST) &&
            !($errors = $this->bookManager->handleForm($form, $request))
        ) {
            $data = $form->getData();
            $this->bookManager->order($data);

            return $this->redirectToRoute('book.list');
        }

        return $this->render(
            'default/book/order.html.twig',
            [
                'id'   => $id,
                'book' => $book,
                'form' => $form->createView(),
            ]
        );
    }
}
