<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Order;
use App\Form\Type\OrderType;
use App\Service\Manager\OrderManager;
use DateTime;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="/order")
 */
class OrderController extends AbstractController
{

    protected OrderManager $orderManager;

    public function __construct(OrderManager $orderManager)
    {
        $this->orderManager = $orderManager;
    }

    /**
     * @Route(path="/list", name="order.list")
     */
    public function index(Request $request): Response
    {
        $filter = $request->query->all();
        $orders = $this->orderManager->paginate($filter);

        return $this->render('default/order/index.html.twig',
            [
                'orders'       => $orders,
                'orderManager' => $this->orderManager,
                'statuses'     => Order::STATUSES,
            ]
        );
    }

    /**
     * @Route(path="/add", name="order.add")
     */
    public function add(Request $request): Response
    {
        if ($request.date < DateTime::now()) {
            throw new \Error("you can't choose past date");
        }
        $form = $this->orderManager->form(OrderType::class);
        if ($request->isMethod(Request::METHOD_POST) &&
            !($errors = $this->orderManager->handleForm($form, $request))
        ) {
            $data = $form->getData();
            $this->orderManager->create($data);

            return $this->redirectToRoute('order.list');
        }

        return $this->render(
            'default/order/add.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route(path="/{id}/edit", name="order.edit")
     */
    public function edit(Request $request, int $id): Response
    {
        $data = $this->orderManager->get($id);

        // convert dates
        $data['start_at'] = $data['start_at'] ? DateTime::createFromFormat('Y-m-d', $data['start_at']) : null;
        $data['end_at']   = $data['end_at'] ? DateTime::createFromFormat('Y-m-d', $data['end_at']) : null;

        $form = $this->orderManager->form(OrderType::class, $data ?? [], ['id' => $id]);
        if ($request->isMethod(Request::METHOD_POST) &&
            !($errors = $this->orderManager->handleForm($form, $request))
        ) {
            $data = $form->getData();
            $this->orderManager->update($id, $data);

            return $this->redirectToRoute('order.list');
        }

        return $this->render(
            'default/order/edit.html.twig',
            [
                'id'   => $id,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route(path="/{id}/delete", name="order.delete")
     */
    public function delete(int $id): Response
    {
        $this->orderManager->delete($id);

        return $this->redirectToRoute('order.list');
    }

    /**
     * @Route(path="/{id}/cancel", name="order.cancel")
     */
    public function cancel(int $id): Response
    {
        $this->orderManager->cancel($id);

        return $this->redirectToRoute('order.list');
    }

    /**
     * @Route(path="/{id}/open", name="order.open")
     */
    public function open(int $id): Response
    {
        $this->orderManager->open($id);

        return $this->redirectToRoute('order.list');
    }

    /**
     * @Route(path="/{id}/done", name="order.done")
     */
    public function done(int $id): Response
    {
        $this->orderManager->done($id);

        return $this->redirectToRoute('order.list');
    }

}
