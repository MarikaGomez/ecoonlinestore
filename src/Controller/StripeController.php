<?php

namespace App\Controller;

use App\CustomClass\Cart;
use App\Entity\Order;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StripeController extends AbstractController
{
    #[Route('/order/create-session/{reference}', name: 'stripe_create_session')]
    public function index(EntityManagerInterface $entityManager, $reference): Response
    {
        $products_for_stripe = [];
        $YOUR_DOMAIN = 'http://127.0.0.1:8000';

        $order = $entityManager -> getRepository(Order::class) -> findOneBy(['reference' => $reference]);

        if (!$order){
            return $this->redirectToRoute('order');
        }

        foreach ($order -> getOrderDetails() -> getValues() as $product) {
            $product_object = $entityManager -> getRepository(Product::class) -> findOneBy(['name' => $product -> getProduct()]);


            $products_for_stripe[] = [
                'price_data' => [
                    'currency' => 'usd',
                    'unit_amount' => $product -> getPrice(),
                    'product_data' => [
                        'name' => $product -> getProduct(),
                        'images' => [$YOUR_DOMAIN.'/uploads/files/'.$product_object -> getImage()],
                    ]
                ],
                'quantity' => $product -> getQuantity(),
            ];
        }

        $products_for_stripe[] = [
            'price_data' => [
                'currency' => 'usd',
                'unit_amount' => $order -> getCarrierPrice(),
                'product_data' => [
                    'name' => $order -> getCarrierName(),
                    'images' => [$YOUR_DOMAIN],
                ]
            ],
            'quantity' => 1,
        ];

        //dd($products_for_stripe);

        // Stripe initialization
        Stripe::setApiKey('sk_test_51K332aKn0xm14My0lYXjQFVeeWNyNER3V18fvNiDdNGoPFcbMk1SPc0owarAa40cffwlLEkvTH8NaCWsFVQ4pZIA00QzuaNWwi');


        $checkout_session = Session::create([
            'customer_email' => $this -> getUser() -> getEmail(),
            'payment_method_types' => ['card'],
            'line_items' => [$products_for_stripe],
            'mode' => 'payment',
            'success_url' => $YOUR_DOMAIN . '/order/success/{CHECKOUT_SESSION_ID}',
            'cancel_url' => $YOUR_DOMAIN . '/order/error/{CHECKOUT_SESSION_ID}',
        ]);

        $order -> setStripeSessionId($checkout_session->id);
        $entityManager -> flush();

        return $this -> redirect($checkout_session -> url);
    }
}
