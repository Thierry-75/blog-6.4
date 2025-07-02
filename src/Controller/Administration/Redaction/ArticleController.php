<?php

namespace App\Controller\Administration\Redaction;

use App\Entity\Article;
use App\Form\ArticleForm;
use App\Message\SendEmailNotification;
use App\Repository\ArticleRepository;
use App\Service\IntraController;
use App\Service\PhotoService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ArticleController extends AbstractController
{
    #[Route('/articles',name:'app_articles_index',methods:['GET'])]
    public function index(ArticleRepository $articleRepository,PaginatorInterface $paginatorInterface,Request $request): Response
    {
        
        $data =$articleRepository->findPublished();
        $articles = $paginatorInterface->paginate(
            $data,
            $request->query->getInt('page',1),
            9
        );
        return $this->render('article/index.html.twig',['articles'=>$articles]);
    }


    #[Route('/article/add', name: 'app_article_add', methods: ['GET', 'POST'])]
    public function add_article(Request $request, ValidatorInterface $validator, EntityManagerInterface $em, 
    PhotoService $photoService, IntraController $intra_controller,MessageBusInterface $messageBus): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleForm::class, $article);
        $form->handleRequest($request);
        if ($request->isMethod('POST')) {
            $errors = $validator->validate($request);
            if (count($errors) > 0) {
                return $this->render('article/add_article.html.twig', [
                    'form_article_new' => $form->createView(),
                    'errors' => $errors
                ]);
            }
            if($form->isSubmitted() && $form->isValid()){
                try{
                $image = $form->get('photo')->getData();
                if($image->getClientOriginalExtension()=='jpeg' || $image->getClientOriginalExtension()=='jpg'){
                    $fichier= $photoService->add($image,$article->getTitle(),$intra_controller->getFolderArticle(),1024,768);
                    $article->setImage($fichier);
                    $article->setUser($this->getUser());
                $em->persist($article);
                $em->flush();
                $url = $this->generateUrl('app_main',[],UrlGeneratorInterface::ABSOLUTE_URL);
                $messageBus->dispatch(new SendEmailNotification($intra_controller->getWebmaster(),$this->getUser()->getEmail(),'Nouvel article','new_article',['user'=>$this->getUser(),'url'=>$url]));
                return $this->redirectToRoute('app_main');
                }
                }catch(EntityNotFoundException $e)
                {
                    return $this->redirectToRoute('app_error',['exception'=>$e]);
                }
            }
        }
        return $this->render('article/add_article.html.twig', [
            'form_article_new' => $form->createView(),
        ]);
    }
}
