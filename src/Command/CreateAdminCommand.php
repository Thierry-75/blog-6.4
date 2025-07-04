<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(name: 'create-administrator',description: 'Creation compte Administrateur')]
class CreateAdminCommand extends Command
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct('app:create-adminsitrator');
        $this->em = $em;
    }

    protected function configure(): void
    {
        $this->addArgument('email', InputArgument::OPTIONAL,'Email')
             ->addArgument('pseudo',InputArgument::OPTIONAL,'pseudo')
            ->addArgument('password', InputArgument::OPTIONAL,'Mot de passe');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int 
    {
        $helper = $this->getHelper('question');
        $io = new SymfonyStyle($input,$output);

        $email = $input->getArgument('email');
        if(!$email){
            $question = new Question('Quelle est votre adresse courriel ? : ');
            $email = $helper->ask($input,$output,$question);
        }
        $pseudo = $input->getArgument('pseudo');
        if(!$pseudo){
            $question = new Question('Quelle est votre pseudo ? : ');
            $pseudo = $helper->ask($input,$output,$question);
        }
        $password = $input->getArgument('password');
        if(!$password){
            $question = new Question('Quel est votre mot de passe (10 caractères) ? : ');
            $plainPassword = $helper->ask($input,$output,$question);
        }

        $admin = new User();
        $admin->setEmail($email)
            ->setPlainPassword($plainPassword)
            ->setPseudo($pseudo)
            ->setRoles(['ROLE_ADMIN'])
            ->setCreatedAt(new  \DateTimeImmutable())
            ->setIsVerified(true)
            ->setIsNewsLetter(true);

        $this->em->persist($admin);
        $this->em->flush();
        $io->success('Le compte administrateur a été créé !');
        return Command::SUCCESS;
    }


}