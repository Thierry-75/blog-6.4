<?php

namespace App\Controller;

use App\Entity\Avatar;
use App\Entity\User;
use App\Form\AvatarFormTypeForm;
use App\Message\SendEmailNotification;
use App\Service\IntraController;
use App\Service\PhotoService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class AvatarController extends AbstractController
{
    #[Route('/profil/show/{id}', name: 'app_avatar_profil',methods:['GET'])]
    public function showProfil(User $user): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        return $this->render('avatar/index.html.twig', ['user'=>$user]);
    }

    #[Route('/profil/add/',name:'app_add_avatar',methods:['POST','GET'])]
    public function addAvatar(Request $request, ValidatorInterface $validator,PhotoService $photoService,
    EntityManagerInterface $em, MessageBusInterface $messageBus,IntraController $intraController): Response
    {
        $avatar = new Avatar();
        $form = $this->createForm(AvatarFormTypeForm::class,$avatar);
        $form->handleRequest($request);
        if($request->isMethod('POST')){
            $errors = $validator->validate($request);
            if(count($errors)>0){
                return $this->render('avatar/add.html.twig',['avatarForm'=>$form->createView(),'errors'=>$errors]);
            }
            if($form->isSubmitted() && $form->isValid()){
                try{
                    $photo = $form->get('image')->getData();
                    if($photo->getClientOriginalExtension()=='png'){
                        $user = $em->getRepository(User::class)->find($this->getUser());
                        $fichier = $photoService->add($photo,$user->getEmail(),$intraController->getFolder(),128,128);
                        $avatar->setName($fichier);
                        $avatar->setSuscriber($user);
                        $user->setIsFull(true);
                        $em->persist($avatar);
                        $em->persist($user);
                        $em->flush();
                        $url = $this->generateUrl('app_main',[],UrlGeneratorInterface::ABSOLUTE_URL);
                        $messageBus->dispatch(new SendEmailNotification($intraController->getWebmaster(),$user->getEmail(),'Profil complet','confirmation',['user'=>$user,'url'=>$url]));
                        $this->addFlash('alert-success','Votre avatar a été ajouté !');
                        return $this->redirectToRoute('app_avatar_profil',['id'=>$user->getId()]);
                    }

                }catch(EntityNotFoundException $e){
                    return $this->redirectToRoute('app_error',['exception'=>$e]);
                }
            }
        }



        return $this->render('avatar/add.html.twig',['avatarForm'=>$form->createView()]);
    }
}
