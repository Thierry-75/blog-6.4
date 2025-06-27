<?php

namespace App\Controller;


use App\Service\IntraController;
use function PHPUnit\Framework\isObject;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\JwtService;
use Symfony\Component\Messenger\MessageBusInterface;


final class MainController extends AbstractController
{

    #[Route('/', name: 'app_main')]
    public function index(IntraController $intraController, JwtService $jwtService, MessageBusInterface $messageBus): Response
    {
        if ($this->getUser()) {
            //force to validate email
            if ($this->getUser()->isVerified() === false) {
                $subject = 'Activation de votre compte';
                $destination = 'check_user';
                $nomTemplate = 'register';
                $intraController->emailValidate($this->getUser(), $jwtService, $messageBus, $destination, $subject, $nomTemplate);
                $this->addFlash('alert-warning','Vous devez confirmer votre adresse mail !');
            }
            if ($this->getUser()->isVerified() === true && $this->getUser()->isFull() === false) {
                $this->addFlash('alert-warning','Vous devez indiquez votre avatar !');
                return $this->redirectToRoute('app_add_avatar');
            }
        }
        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }
}
