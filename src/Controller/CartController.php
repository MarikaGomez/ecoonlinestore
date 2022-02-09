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
        return $this->render('cart/index.html.twig', [
            'cart' => $cart -> getProductData()
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

    #[Route('/delete/{id}', name: 'delete_product')]
    public function delete(Cart $cart, $id): Response
    {
        $cart -> delete($id);

        return $this->redirectToRoute('cart');
    }

    #[Route('/delete-item/{id}', name: 'delete_item')]
    public function deleteOneItem(Cart $cart, $id): Response
    {
        $cart -> deleteOneItem($id);

        return $this->redirectToRoute('cart');
    }
}
