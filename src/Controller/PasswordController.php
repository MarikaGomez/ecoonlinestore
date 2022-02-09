<?php

namespace App\Controller;

use App\Form\UpdatePasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class PasswordController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }

    #[Route('/profile/update-password', name: 'password')]
    public function index(Request $request, UserPasswordHasherInterface $hasher): Response
    {
        $success = null;
        $error = null;

        $user = $this->getUser();
        $form = $this->createForm(UpdatePasswordType::class,$user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $old_password = $form->get('old_password')->getData();

            if ($hasher->isPasswordValid($user, $old_password)){
                $new_password = $form->get('new_password')->getData();
                $password = $hasher->hashPassword($user, $new_password);

                $user->setPassword($password);
                $this->entityManager->flush();

                $success = 'Your password has been updated !';
            } else {
                $error = 'An error has occurred, please try again.';
            }
        }

        return $this->render('profile/password.html.twig', [
            'form' => $form -> createView(),
            'success' => $success,
            'error' => $error,
        ]);
    }
}
