<?php

namespace App\Controller;

use App\Form\OrderType;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Order;
use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class OrdersController extends AbstractController
{
    /**
     * @var ManagerRegistry
     */
    private $doctrine;
    /**
     * @var OrderRepository
     */
    private $orderRepository;

    public function __construct(ManagerRegistry $doctrine, OrderRepository $orderRepository)
    {
        $this->doctrine = $doctrine;
        $this->orderRepository = $orderRepository;

    }

    /**
     * @Route("/orders", name="orders")
     */
    public function index(): Response
    {
        return $this->render('orders/index.html.twig', [
            'orders' => $this->orderRepository->findAll(),
            'messageERROR' => '',
            'messageOK' => '',
        ]);
    }

    /**
     * @Route("/orders/edit/{id}", name="edit")
     */
    public function edit(int $id, Request $request): Response
    {
        $order = $this->orderRepository->find($id);
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $order = $form->getData();
            $entityManager = $this->doctrine->getManager();
            $entityManager->persist($order);
            $entityManager->flush();
            return $this->redirectToRoute('orders');
        }

        return $this->render('orders/create.html.twig', [
            'h1' => 'Update order',
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/orders/create", name="create")
     */
    public function create(Request $request): Response
    {
        $order = new Order();
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $order = $form->getData();
            $entityManager = $this->doctrine->getManager();
            $entityManager->persist($order);
            $entityManager->flush();
            return $this->redirectToRoute('orders');
        }

        return $this->render('orders/create.html.twig', [
            'h1' => 'Create new',
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/orders/del/{id}", name="del")
     */
    public function delete(int $id): Response
    {
        $message = '';
        try {
            $order = $this->orderRepository->find($id);
            $entityManager = $this->doctrine->getManager();
            $entityManager->remove($order);
            $entityManager->flush();
            $message = 'record deleted successfully';
            return $this->redirectToRoute('orders');
        } catch (\Exception $e) {
            return $this->render('orders/index.html.twig', [
                'orders' => $this->orderRepository->findAll(),
                'messageERROR' => 'Some error',
                'messageOK' => $message,
            ]);
        }

    }
}
