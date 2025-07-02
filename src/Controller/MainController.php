<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use App\Service\IntraController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\JwtService;
use Doctrine\ORM\EntityNotFoundException;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;


final class MainController extends AbstractController
{

    #[Route('/', name: 'app_main',methods: ['GET'])]
    public function index(IntraController $intraController, 
    JwtService $jwtService, 
    MessageBusInterface $messageBus,
    ArticleRepository $articleRepository,
    PaginatorInterface $paginator,
    Request $request
    ): Response
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
        try{
        $data= $articleRepository->mainPublished();
        $articles = $paginator->paginate($data, $request->query->getInt('page',1),9);
        }catch(EntityNotFoundException $e){
            return $this->redirectToRoute('app_error',['exceptiopn'=>$e]);
        }
        return $this->render('main/index.html.twig', ['articles'=>$articles]);
    }
}
