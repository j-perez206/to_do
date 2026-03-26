<?php
namespace App\EventSubscriber;

use App\Event\TareaCreada;
use App\Message\EnviarCorreoMessage;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class EnviarCorreoInformativoSubscriber implements EventSubscriberInterface
{
    // Inyecta el bus Messenger para poder enviar mensajes a la cola
    public function __construct(private MessageBusInterface $bus) {}

    // Aquí se indica qué evento debe escuchar y qué función llamar
    public static function getSubscribedEvents(): array
    {
        return [TareaCreada::class => 'onTareaCreada'];
    }

    // Se ejecuta cuando se crea una tarea. Crea el mensaje con el texto de la tarea y lo mete en cola
    // A partir de aquí se encarga el handler
    public function onTareaCreada(TareaCreada $event): void
    {
        $this->bus->dispatch(new EnviarCorreoMessage(
            $event->getPost()->getText()
        ));
    }
}
