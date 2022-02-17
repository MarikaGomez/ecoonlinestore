<?php

namespace App\Controller;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderErrorController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this -> entityManager = $entityManager;
    }

    #[Route('/order/error/{stripeSessionId}', name: 'order_error')]
    public function index($stripeSessionId): Response
    {

        $order = $this -> entityManager -> getRepository(Order::class) -> findOneBy(['stripeSessionId' => $stripeSessionId]);

        if (!$order || $order -> getUser() != $this -> getUser()) {
            return $this -> redirectToRoute('home');
        }

        return $this->render('order_error/index.html.twig', [
            'order' => $order,
        ]);
    }
}
