<?php

namespace App\Controller;

use App\CustomClass\Cart;
use App\Entity\Order;
use App\Entity\OrderDetails;
use App\Form\OrderType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this -> entityManager = $entityManager;
    }

    #[Route('/order', name: 'order')]
    public function index(Cart $cart): Response
    {
        if (!$this -> getUser() -> getAddresses() -> getValues()) {
            return $this -> redirectToRoute('add_new_address');
        }

        $form = $this -> createForm(OrderType::class, null, [
            'user' => $this -> getUser(),
        ]);

        return $this->render('order/index.html.twig', [
            'form' => $form -> createView(),
            'cart' => $cart -> getProductData(),
        ]);
    }

    #[Route('/order/summary', name: 'order_summary', methods: ['POST'])]
    public function add(Cart $cart, Request $request): Response
    {
        $form = $this -> createForm(OrderType::class, null, [
            'user' => $this -> getUser(),
        ]);

        $form -> handleRequest($request);

        if ($form -> isSubmitted() && $form -> isValid()) {

//            dd($form -> getData());
            $date = new \DateTimeImmutable();
            $carriers = $form -> get('carriers') -> getData();
            $delivery = $form -> get('addresses') -> getData();

            $delivery_content = $delivery -> getFirstname().' '.$delivery -> getLastname();

            if ($delivery -> getCompany()) {
                $delivery_content .= '<br>'.$delivery -> getCompany();
            }

            $delivery_content .= '<br>'.$delivery -> getAddress();
            $delivery_content .= '<br>'.$delivery -> getZipcode().' '.$delivery -> getCity().' - '.$delivery -> getCountry();
            $delivery_content .= '<br>'.$delivery -> getPhone();

            // Save order in database
            $order = new Order();

            $reference = $date -> format('dmY').'-'.uniqid();
            $order -> setReference($reference);

            $order -> setUser($this-> getUser());
            $order -> setCreatedAt($date);
            $order -> setCarrierName($carriers -> getName());
            $order -> setCarrierPrice($carriers -> getPrice());
            $order -> setDelivery($delivery_content);
            $order -> setState(0);

            $this -> entityManager -> persist($order);

            // Save order details in database
            foreach ($cart -> getProductData() as $product) {
                //dd($product);
                $order_details = new OrderDetails();
                $order_details -> setMyOrder($order);
                $order_details -> setProduct($product['product'] -> getName());
                $order_details -> setQuantity($product['quantity']);
                $order_details -> setPrice($product['product'] -> getPrice());
                $order_details -> setTotal($product['product'] -> getPrice() * $product['quantity']);

                $this -> entityManager -> persist($order_details);
            }

            //dd($products_for_stripe);

            // Flush all in database
            $this -> entityManager -> flush();

            //dd($checkout_session);

            return $this->render('order/add.html.twig', [
                'cart' => $cart -> getProductData(),
                'carrier' => $carriers,
                'delivery' => $delivery_content,
                'reference' => $order -> getReference(),
            ]);
        }

        return $this->redirectToRoute('cart');

    }
}
