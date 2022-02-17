<?php

namespace App\Controller;

use App\CustomClass\Mail;
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
        $success = null;
        $error = null;

        // instanciation class User
        $user = new User();

        // instanciation du formulaire
        $form = $this -> createForm(RegisterType::class, $user);

        //
        $form -> handleRequest($request);
        if ($form -> isSubmitted() && $form -> isValid()) {
            $user = $form -> getData();

            $search_email = $this -> entityManager -> getRepository(User::class) -> findOneBy(['email' => $user -> getEmail()]);
            if (!$search_email) {
                $password = $hasher -> hashPassword($user, $user -> getPassword());

                $user -> setPassword($password);

                //dd($password);

                $this -> entityManager -> persist($user);
                $this -> entityManager -> flush();

                // send mail
                $mail = new Mail();
                $content =
                    'OH, HEY YOU! <br>
Welcome to eco. online store, it\'s great to meet you! <br><br>
We promise to keep you up-to-date with awesome products, super sales and throw in the odd surprise and special offer. <br>
We\'re pretty sure there\'s something for you in here. <br>
Come see for yourself...';
                $mail -> send(
                    $user->getEmail(),
                    $user->getFullName(),
                    'Welcome to eco. online store',
                    $content,
                );


                $success = 'New account created !';
            } else {
                $error = 'An account linked to this email already exist.';
            }


        }

        // le formulaire est passé comme variable pour le template
        return $this -> render('register/index.html.twig', [
            'form' => $form -> createView(),
            'success' => $success,
            'error' => $error,
        ]);
    }
}