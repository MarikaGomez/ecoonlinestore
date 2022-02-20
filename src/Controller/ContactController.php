<?php

namespace App\Controller;

use App\CustomClass\Mail;
use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact')]
    public function index(Request $request): Response
    {
        $success = null;
        $form = $this -> createForm(ContactType::class);

        $form -> handleRequest($request);

        if ($form -> isSubmitted() && $form -> isValid()) {
            $from_name = $form-> get('firstname') -> getData().' '.$form-> get('lastname') -> getData();
            $from_mail = $form-> get('email') -> getData();
            $subject = $form-> get('subject') -> getData();
            $message = $form-> get('content') -> getData();

            //dd($from_name, $from_mail, $subject, $message);

            $mail = new Mail();
            $content =
                'From: '.$from_name.', <br>
Email: '.$from_mail.'<br>
Subject: '.$subject.' <br>
Message: '.$message.'<br>';
            $mail -> send(
                'gmz.marika@gmail.com',
                'Admin',
                'New request from customer - eco. online store',
                $content,
            );
            $success = 'Your message has been sent !';
        }

        return $this->render('contact/index.html.twig', [
            'form' => $form -> createView(),
            'success' => $success,
        ]);
    }
}
