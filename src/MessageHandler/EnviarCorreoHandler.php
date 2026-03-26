<?php

namespace App\MessageHandler;

use App\Message\EnviarCorreoMessage;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mime\Email;

#[AsMessageHandler]
class EnviarCorreoHandler
{
    // Inyecta el mailer para poder enviar correos
    public function __construct(private MailerInterface $mailer) {}

    // invoke se ejecuta automático cuando el messenger saca de la cola un mensaje de tipo EnviarCorreoMessage
    public function __invoke(EnviarCorreoMessage $message): void
    {
        $email = (new Email())
            ->from('info@todo.com')
            ->to('destinatario@prueba.com')
            ->subject('Nueva tarea creada')
            ->text('Se ha creado la tarea: ' . $message->getTextoTarea());

        $this->mailer->send($email);
    }
}
