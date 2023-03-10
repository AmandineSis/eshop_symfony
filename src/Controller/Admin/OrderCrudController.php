<?php

namespace App\Controller\Admin;

use App\Classes\Mail;
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
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;

class OrderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Order::class;
    }

    private $entityManager;
    private $adminUrlGenerator;

    public function __construct(EntityManagerInterface $entityManager, AdminUrlGenerator $adminUrlGenerator)
    {
        $this->entityManager = $entityManager;
        $this->adminUrlGenerator = $adminUrlGenerator;
    }

    public function configureActions(Actions $actions): Actions
    {
        $updatePreparation = Action::new('updatePreparation', 'Préparation en cours', 'fa fa-box-open')->linkToCrudAction('updatePreparation');
        $updateDelivery = Action::new('updateDelivery', 'Livraison en cours', 'fa fa-truck')->linkToCrudAction('updateDelivery');

        return $actions
            ->add('index', 'detail')
            ->remove('index', 'edit')
            ->remove('index', 'delete')
            ->remove('detail', 'delete')
            ->remove('detail', 'edit')
            ->add('detail', $updatePreparation)
            ->add('detail', $updateDelivery);
    }

    public function updatePreparation(AdminContext $context)
    {
        $order = $context->getEntity()->getInstance();
        $order->setStatus(2);
        $this->entityManager->flush();

        $this->addFlash('notice', "<span style='color:green;'><strong>La commande " . $order->getReference() . " est bien <u>en cours de préparation</u></strong></span>");

        $url = $this->adminUrlGenerator
            ->setController(OrderCrudController::class)
            ->setAction(Action::INDEX) // ou ('index')
            ->generateUrl();

        $email = new Mail();
        $content = "Bonjour" . $order->getUser()->getFirstName() . "<br/>Votre commande " . $order->getReference() . "est en cours de préparation !<br/>";
        $email->send($order->getUser()->getEmail(), $order->getUser()->getFirstName(), 'Cocorico: Votre commande est en cours de préparation', $content);

        return $this->redirect($url);
    }

    public function updateDelivery(AdminContext $context)
    {
        $order = $context->getEntity()->getInstance();
        $order->setStatus(3);
        $this->entityManager->flush();

        $this->addFlash('notice', "<span style='color:orange;'><strong>La commande " . $order->getReference() . " est bien <u>en cours de livraison</u></strong></span>");

        $url = $this->adminUrlGenerator
            ->setController(OrderCrudController::class)
            ->setAction(Action::INDEX) // ou ('index')
            ->generateUrl();

        $email = new Mail();
        $content = "Bonjour" . $order->getUser()->getFirstName() . "<br/>Votre commande " . $order->getReference() . "est en cours de livraison et sera prochainement chez vous!<br/>";
        $email->send($order->getUser()->getEmail(), $order->getUser()->getFirstName(), 'Cocorico: Votre commande est en cours de livraison', $content);


        return $this->redirect($url);
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setDefaultSort(['id' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            //DateTimeField::new('createdAt', 'Passée le'),
            TextField::new('user.fullname', 'Utilisateur'),
            MoneyField::new('total', 'Total produits')->setCurrency('EUR'),
            TextareaField::new('delivery', 'Adresse de Livraison')->renderAsHtml()->onlyOnDetail(),
            TextField::new('carrierName', 'Transporteur'),
            MoneyField::new('carrierPrice', 'Frais de port')->setCurrency('EUR'),
            ChoiceField::new('status')->setChoices([
                'Non payé' => 0,
                'Payé' => 1,
                'Préparation en cours' => 2,
                'Livraison en cours' => 3
            ]),
            ArrayField::new('orderDetails', 'produits achetés')->hideOnIndex()
        ];
    }
}
