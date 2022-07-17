<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Reading;
use App\Form\Type\ReadingProlongType;
use App\Form\Type\ReadingType;
use App\Service\Manager\AuthorManager;
use App\Service\Manager\BookManager;
use App\Service\Manager\ReadingManager;
use App\Service\Manager\UserManager;
use DateTime;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="/reading")
 */
class ReadingController extends AbstractController
{

    protected AuthorManager  $authorManager;
    protected BookManager    $bookManager;
    protected ReadingManager $readingManager;
    protected UserManager    $userManager;

    public function __construct(
        AuthorManager $authorManager,
        BookManager $bookManager,
        ReadingManager $readingManager,
        UserManager $userManager
    ) {
        $this->authorManager  = $authorManager;
        $this->bookManager    = $bookManager;
        $this->readingManager = $readingManager;
        $this->userManager    = $userManager;
    }

    /**
     * @Route(path="/list", name="reading.list")
     */
    public function index(Request $request): Response
    {
        $filter       = $request->query->all();
        $readings     = $this->readingManager->paginate($filter);
        $readingTypes = Reading::READING_TYPES;

        return $this->render('default/reading/index.html.twig',
            [
                'readings'       => $readings,
                'readingTypes'   => $readingTypes,
                'authorManager'  => $this->authorManager,
                'bookManager'    => $this->bookManager,
                'readingManager' => $this->readingManager,
                'userManager'    => $this->userManager,
            ]
        );
    }

    /**
     * @Route(path="/prolong", name="reading.prolong")
     */
    public function prolong(Request $request): Response
    {
        $filter = array_merge($request->query->all(), ['isProlong' => true]);
        $readings     = $this->readingManager->paginate($filter);
        $readingTypes = Reading::READING_TYPES;

        return $this->render('default/reading/prolong.html.twig',
            [
                'readings'       => $readings,
                'readingTypes'   => $readingTypes,
                'authorManager'  => $this->authorManager,
                'bookManager'    => $this->bookManager,
                'readingManager' => $this->readingManager,
                'userManager'    => $this->userManager,
            ]
        );
    }

    /**
     * @Route(path="/add", name="reading.add")
     */
    public function add(Request $request): Response
    {
        $form = $this->readingManager->form(ReadingType::class);
        if ($request->isMethod(Request::METHOD_POST) &&
            !($errors = $this->readingManager->handleForm($form, $request))
        ) {
            $data = $form->getData();
            $this->readingManager->create($data);

            return $this->redirectToRoute('reading.list');
        }

        return $this->render(
            'default/reading/add.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route(path="/{id}/edit", name="reading.edit")
     */
    public function edit(Request $request, int $id): Response
    {
        $data = $this->readingManager->get($id);

        // convert dates
        $data['start_at'] = $data['start_at'] ? DateTime::createFromFormat('Y-m-d', $data['start_at']) : null;
        $data['end_at']   = $data['end_at'] ? DateTime::createFromFormat('Y-m-d', $data['end_at']) : null;

        // create form
        $form = $this->readingManager->form(ReadingType::class, $data ?? [], ['id' => $id]);
        if ($request->isMethod(Request::METHOD_POST) &&
            !($errors = $this->readingManager->handleForm($form, $request))
        ) {
            $data = $form->getData();
            $this->readingManager->update($id, $data);

            return $this->redirectToRoute('reading.list');
        }

        return $this->render(
            'default/reading/edit.html.twig',
            [
                'id'   => $id,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route(path="/{id}/delete", name="reading.delete")
     */
    public function delete(int $id): Response
    {
        $this->readingManager->delete($id);

        return $this->redirectToRoute('reading.list');
    }

    /**
     * @Route(path="/{id}/prolong-cancel", name="reading.prolong.cancel")
     */
    public function prolongCancel(Request $request, int $id): Response
    {
        $this->readingManager->prolongCancel($id);

        return $this->redirectToRoute('reading.prolong');
    }

    /**
     * @Route(path="/{id}/prolong-accept", name="reading.prolong.accept")
     */
    public function prolongAccept(Request $request, int $id): Response
    {
        $this->readingManager->prolongAccept($id);

        return $this->redirectToRoute('reading.prolong');
    }


    /**
     * @Route(path="/my", name="reading.list.my")
     */
    public function my(Request $request): Response
    {
        $userId       = $this->getUser() ? $this->getUser()->getId() : 0;
        $filter       = array_merge($request->query->all(), ['user_id' => $userId]);
        $readings     = $this->readingManager->paginate($filter);
        $readingTypes = Reading::READING_TYPES;

        return $this->render('default/reading/my.index.html.twig',
            [
                'readings'       => $readings,
                'readingTypes'   => $readingTypes,
                'authorManager'  => $this->authorManager,
                'bookManager'    => $this->bookManager,
                'readingManager' => $this->readingManager,
                'userManager'    => $this->userManager,
            ]
        );
    }

    /**
     * @Route(path="/my/{id}/prolong-add", name="reading.my.prolong.add")
     */
    public function myProlongAdd(Request $request, int $id): Response
    {
        $data = $this->readingManager->get($id);

        // convert dates
        $data['prolong_at'] = $data['prolong_at'] ? DateTime::createFromFormat('Y-m-d', $data['prolong_at']) : null;

        // create form
        $form = $this->readingManager->form(ReadingProlongType::class, $data ?? [], ['id' => $id]);
        if ($request->isMethod(Request::METHOD_POST) &&
            !($errors = $this->readingManager->handleForm($form, $request))
        ) {
            $data = $form->getData();
            $this->readingManager->prolong($id, $data);

            return $this->redirectToRoute('reading.list.my');
        }

        return $this->render(
            'default/reading/my.prolong.add.html.twig',
            [
                'id'   => $id,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route(path="/my/{id}/prolong-cancel", name="reading.my.prolong.cancel")
     */
    public function myProlongCancel(Request $request, int $id): Response
    {
        $this->readingManager->prolongCancel($id);

        return $this->redirectToRoute('reading.list.my');
    }

}
