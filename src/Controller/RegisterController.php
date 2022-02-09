<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this -> entityManager = $entityManager;
    }

    #[Route('/signup', name: 'register')]
    public function index(Request $request,UserPasswordHasherInterface $hasher): Response
    {
        // instanciation class User
        $user = new User();

        // instanciation du formulaire
        $form = $this -> createForm(RegisterType::class, $user);

        //
        $form -> handleRequest($request);
        if ($form -> isSubmitted() && $form -> isValid()) {
            $user = $form -> getData();
            $password = $hasher -> hashPassword($user, $user -> getPassword());

            $user -> setPassword($password);

            //dd($password);

            $this -> entityManager -> persist($user);
            $this -> entityManager -> flush();
        }

        // le formulaire est passé comme variable pour le template
        return $this -> render('register/index.html.twig', [
            'form' => $form -> createView(),
        ]);
    }
}