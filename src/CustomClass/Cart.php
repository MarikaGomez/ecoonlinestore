<?php

namespace App\CustomClass;

use Symfony\Component\HttpFoundation\RequestStack;

class Cart
{
    private $stack;

    public function __construct(RequestStack $stack)
    {
        $this -> stack = $stack -> getSession();
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

    public function remove()
    {
        return $this -> stack -> remove('cart');
    }
}