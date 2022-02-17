<?php

namespace App\Controller;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MyOrderController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this -> entityManager = $entityManager;
    }

    #[Route('/profile/orders', name: 'my_orders')]
    public function index(): Response
    {
        $orders = $this -> entityManager -> getRepository(Order::class) -> findSuccessOrder($this->getUser());

        return $this->render('profile/orders.html.twig', [
            'orders' => $orders,
        ]);
    }

    #[Route('/profile/orders/{reference}', name: 'my_order_show')]
    public function show($reference): Response
    {
        $order = $this -> entityManager -> getRepository(Order::class) -> findOneBy(['reference' => $reference]);

        if (!$order || $order -> getUser() !== $this -> getUser()) {
            return $this->redirectToRoute('my_orders');
        }

        return $this->render('profile/order.html.twig', [
            'order' => $order,
        ]);
    }
}
