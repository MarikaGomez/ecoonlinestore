<?php

namespace App\Controller;

use App\CustomClass\Cart;
use App\CustomClass\Mail;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderSuccessController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this -> entityManager = $entityManager;
    }

    #[Route('/order/success/{stripeSessionId}', name: 'order_success')]
    public function index($stripeSessionId, Cart $cart): Response
    {
        $order = $this -> entityManager -> getRepository(Order::class) -> findOneBy(['stripeSessionId' => $stripeSessionId]);

        if (!$order || $order -> getUser() != $this -> getUser()) {
            return $this -> redirectToRoute('home');
        }

        if ($order -> getState() === 0) {
            // empty cart
            $cart -> remove();

            // change order status | boolean
            $order -> setState(1);
            $this -> entityManager -> flush();

            // send mail to confirm order
            $mail = new Mail();
            $content =
                'Hi '.$order -> getUser() -> getFirstname().', <br>
Thanks for your purchase! <br><br>
Your order <strong>#'.$order -> getReference() .'</strong> has been confirmed. <br>
We hope that it’s exactly what you were looking for. Let us know how you like it. <br><br>
Thank you for ordering from eco. online store&copy;.';
            $mail -> send(
                $order -> getUser() -> getEmail(),
                $order -> getUser() -> getFullName(),
                'Order confirmed.',
                $content,
            );
        }

        return $this->render('order_success/index.html.twig',[
            'order' => $order,
        ]);
    }
}
