<?php

namespace App\CustomClass;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class Cart
{
    private $stack;
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager, RequestStack $stack)
    {
        $this -> stack = $stack -> getSession();
        $this -> entityManager = $entityManager;
    }

    public function add($id)
    {
        $cart = $this -> stack -> get('cart', []);

        if (!empty($cart[$id])) { // if product already exists in cart
            $cart[$id]++;
        } else {
            $cart[$id] = 1;
        }

        $this -> stack -> set('cart', $cart);
    }

    public function get()
    {
        return $this -> stack -> get('cart');
    }

    public function getProductData()
    {
        //dd($cart -> get());

        $cartData = [];
        if ($this -> get()) {
            foreach ($this -> get() as $id => $quantity) {
                $productObject = $this -> entityManager -> getRepository(Product::class) -> findOneBy(['id' => $id]);

                // check if this returns an object
                if (!$productObject) {
                    $this -> delete($id); // if not delete this from cart
                    continue;
                }

                $cartData[] = [
                    'product' => $productObject,
                    'quantity' => $quantity
                ];
            }
        }

        return $cartData;
    }

    public function remove()
    {
        return $this -> stack -> remove('cart');
    }

    public function delete($id)
    {
        $cart = $this -> stack -> get('cart', []);
        unset($cart[$id]);
        return $this -> stack -> set('cart', $cart);
    }

    public function deleteOneItem($id)
    {
        $cart = $this -> stack -> get('cart', []);

        // check if quantity === 1
        if ($cart[$id] > 1) {
            $cart[$id]--;
        } else {
            unset($cart[$id]);
        }
        return $this -> stack -> set('cart', $cart);
    }
}