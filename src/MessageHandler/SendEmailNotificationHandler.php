<?php

namespace App\MessageHandler;

use App\Message\SendEmailNotification;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class SendEmailNotificationHandler
{

    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function __invoke(SendEmailNotification $message): void
    {
        $email = new TemplatedEmail()
            ->from($message->getFrom())
            ->to($message->getTo())
            ->subject($message->getSubject())
            ->htmlTemplate("email/" . $message->getTemplate() . ".html.twig")
            ->context($message->getContext());
            try{
                $this->mailer->send($email);
                
            }catch(TransportExceptionInterface){

            }
    }
}
