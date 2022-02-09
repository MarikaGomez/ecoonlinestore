<?php

namespace App\Controller;

use App\Entity\Address;
use App\Form\AddressType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AddressController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this -> entityManager = $entityManager;
    }

    #[Route('/profile/address', name: 'address')]
    public function index(): Response
    {
        return $this->render('profile/address.html.twig');
    }

    #[Route('/profile/add-new-address', name: 'add_new_address')]
    public function add(Request $request): Response
    {
        $address = new Address();

        $form = $this -> createForm(AddressType::class, $address);
        $form -> handleRequest($request);

        if ($form -> isSubmitted() && $form -> isValid()) {
            $address -> setUser($this -> getUser());

            $this -> entityManager -> persist($address);
            $this -> entityManager -> flush();

            return $this -> redirectToRoute('address');

            //dd($address);
        }

        return $this->render('profile/address_form.html.twig', [
            'form' => $form -> createView()
        ]);
    }

    #[Route('/profile/edit-address/{id}', name: 'edit_address')]
    public function update(Request $request, $id): Response
    {
        $address = $this -> entityManager -> getRepository(Address::class) -> findOneBy(['id' => $id]);

        if (!$address || $address -> getUser() != $this -> getUser()) {
            return $this -> redirectToRoute('address');
        }

        $form = $this -> createForm(AddressType::class, $address);
        $form -> handleRequest($request);

        if ($form -> isSubmitted() && $form -> isValid()) {

            $this -> entityManager -> flush();
            return $this -> redirectToRoute('address');
        }

        return $this->render('profile/address_form.html.twig', [
            'form' => $form -> createView()
        ]);
    }

    #[Route('/profile/delete-address/{id}', name: 'delete_address')]
    public function delete($id): Response
    {
        $address = $this -> entityManager -> getRepository(Address::class) -> findOneBy(['id' => $id]);

        if ($address && $address -> getUser() == $this -> getUser()) {
            $this -> entityManager -> remove($address);
            $this -> entityManager -> flush();
        }

        return $this -> redirectToRoute('address');
    }
}
