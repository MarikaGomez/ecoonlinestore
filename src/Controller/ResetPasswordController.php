<?php

namespace App\Controller;

use App\CustomClass\Mail;
use App\Entity\ResetPassword;
use App\Entity\User;
use App\Form\ResetPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class ResetPasswordController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this -> entityManager = $entityManager;
    }

    #[Route('/reset-password', name: 'reset_password')]
    public function index(Request $request): Response
    {
        $success = null;
        $error = null;

        if($this -> getUser()) {
            $this->redirectToRoute('home');
        }

        if($request -> get('email')) {
            $email = $request -> get('email');
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

            if ($user) {
                // create & save forgotten password request
                $reset_password = new ResetPassword();
                $reset_password -> setUser($user);
                $reset_password -> setToken(uniqid());
                $reset_password -> setCreatedAt(new \DateTimeImmutable());

                $this -> entityManager -> persist($reset_password);
                $this -> entityManager -> flush();

                // send mail to reset password
                $url = $this->generateUrl('update_password', ['token' => $reset_password -> getToken()]);

                $mail = new Mail();
                $content =
                    'Hi '.$user -> getFirstname().', <br><br>
We\'ve received a request to reset the password for the account associated with '.$user -> getEmail().'. No changes have been made to your account yet. <br><br>
You can reset your password by clicking the following link: <a href="http://127.0.0.1:8000'.$url.'">Reset your password</a> <br><br>
If you did not request a new password, please let us know immediately by replying to this email. <br><br>
— eco. online store&copy;';
                $mail -> send(
                    $user -> getEmail(),
                    $user -> getFullName(),
                    'Reset Password - eco. online store',
                    $content,
                );

                $success = 'Please, check your mail to proceed.';
            }
            else {
                $error = 'No account registered to this email.';
            }
        }

        return $this->render('reset_password/index.html.twig', [
            'success' => $success,
            'error' => $error,
        ]);
    }

    #[Route('/reset-password/{token}', name: 'update_password')]
    public function update($token, Request $request, UserPasswordHasherInterface $hasher): Response
    {
        $reset_password = $this -> entityManager -> getRepository(ResetPassword::class) -> findOneBy(['token' => $token]);

        if (!$reset_password) {
            return $this -> redirectToRoute('home');
        }

        // disable token with createdAt
        $now = new \DateTimeImmutable();
        if ($now > $reset_password -> getCreatedAt() -> modify('+30 minutes')) {
            $this -> addFlash('notice', 'Your link has expired. Send a new request.');
            return $this->redirectToRoute('reset_password');
        }

        $form = $this -> createForm(ResetPasswordType::class);
        $form -> handleRequest($request);

        if ($form -> isSubmitted() && $form -> isValid()) {
            $new_password = $form -> get('new_password') -> getData();

            // hash new password
            $password = $hasher -> hashPassword($reset_password -> getUser(), $new_password);

            $reset_password -> getUser() -> setPassword($password);
            $this -> entityManager -> flush();

            $this -> addFlash('notice', 'Your password has been reset.');
            return $this -> redirectToRoute('app_login');
        }

        return $this->render('reset_password/update.html.twig', [
            'form' => $form -> createView(),
        ]);
    }
}
