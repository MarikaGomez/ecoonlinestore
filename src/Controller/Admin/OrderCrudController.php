<?php

namespace App\Controller\Admin;

use App\CustomClass\Mail;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;

class OrderCrudController extends AbstractCrudController
{
    private $entityManager;
    private $adminUrlGenerator;

    public function __construct(EntityManagerInterface $entityManager, AdminUrlGenerator $adminUrlGenerator)
    {
        $this -> entityManager = $entityManager;
        $this -> adminUrlGenerator = $adminUrlGenerator;
    }

    public static function getEntityFqcn(): string
    {
        return Order::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        $updateProcessingState = Action::new('updateProcessingState','Processing','fas fa-box-open') -> linkToCrudAction('updateProcessingState');
        $updateShippingState = Action::new('updateShippingState','Shipped','fas fa-truck') -> linkToCrudAction('updateShippingState');

        return $actions
            ->add('detail', $updateProcessingState)
            ->add('detail', $updateShippingState)
            ->add('index', 'detail');
    }

    public function updateProcessingState(AdminContext $context)
    {
        $order = $context -> getEntity() -> getInstance();
        $order -> setState(2);
        $this -> entityManager -> flush();

        $this->addFlash('notice','<div class="alert alert-info">The order #'.$order -> getReference().' is now processing.</div>');

        // send mail
        $mail = new Mail();
        $content =
        $content =
            'Hi '.$order -> getUser() -> getFirstname().', <br><br>
Thank you for shopping with us. We are pleased to confirm that your order <strong>#'.$order -> getReference() .'</strong> is in progress. <br><br>
We hope that it’s exactly what you were looking for. Let us know how you like it. <br><br>
Thank you for ordering from eco. online store&copy;.';
        $mail -> send(
            $order -> getUser() ->getEmail(),
            $order -> getUser() ->getFullName(),
            'Confirmation order',
            $content,
        );

        $url = $this -> adminUrlGenerator
            ->setController(OrderCrudController::class)
            ->setAction('index')
            ->generateUrl();


        return $this->redirect($url);
    }

    public function updateShippingState(AdminContext $context)
    {
        $order = $context -> getEntity() -> getInstance();
        $order -> setState(3);
        $this -> entityManager -> flush();

        $this->addFlash('notice','<div class="alert alert-info">The order #'.$order -> getReference().' has been shipped.</div>');

        // send mail
        $mail = new Mail();
        $content =
        $content =
            'Hi '.$order -> getUser() -> getFirstname().', <br><br>
Your beautifully packaged order <strong>#'.$order -> getReference() .'</strong>  is on its way and will be with you soon! <br><br>
We hope you are happy with your purchase and that you had a wonderful experience on our website. Hoping to see you again soon!<br><br>
Thank you for ordering from eco. online store&copy;.';
        $mail -> send(
            $order -> getUser() ->getEmail(),
            $order -> getUser() ->getFullName(),
            'Order Shipped',
            $content,
        );

        $url = $this -> adminUrlGenerator
            ->setController(OrderCrudController::class)
            ->setAction('index')
            ->generateUrl();


        return $this->redirect($url);
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud -> setDefaultSort(['id' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            DateTimeField::new('createdAt'),
            TextField::new('user.fullname', 'User'),
            TextEditorField::new('delivery','Address')->formatValue(function ($value) { return $value; })->onlyOnDetail(),
            MoneyField::new('total') -> setCurrency('USD'),
            TextField::new('carrierName', 'Delivery'),
            MoneyField::new('carrierPrice', 'Shipping Fees') -> setCurrency('USD'),
            ChoiceField::new('state') -> setChoices([
                'Unpaid' => 0,
                'Paid' => 1,
                'Processing' => 2,
                'Shipped' => 3,
            ]),
            ArrayField::new('orderDetails', 'Products')->hideOnIndex(),
        ];
    }
}
