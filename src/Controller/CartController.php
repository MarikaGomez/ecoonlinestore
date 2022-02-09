<?php

namespace App\Controller;

use App\CustomClass\Cart;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this -> entityManager = $entityManager;
    }

    #[Route('/cart', name: 'cart')]
    public function index(Cart $cart): Response
    {
        //dd($cart -> get());

        $cartData = [];
        foreach ($cart -> get() as $id => $quantity) {
            $cartData[] = [
                'product' => $this -> entityManager -> getRepository(Product::class) -> findOneBy(['id' => $id]),
                'quantity' => $quantity
            ];
        }

        return $this->render('cart/index.html.twig', [
            'cart' => $cartData
        ]);
    }

    #[Route('/add-to-cart/{id}', name: 'add_cart')]
    public function add(Cart $cart, $id) : Response
    {
        $cart -> add($id);

        return $this->redirectToRoute('cart');
    }

    #[Route('/delete-cart', name: 'delete_cart')]
    public function remove(Cart $cart): Response
    {
        $cart -> remove();

        return $this->redirectToRoute('products');
    }
}
