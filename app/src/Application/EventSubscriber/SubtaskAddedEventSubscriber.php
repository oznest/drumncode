<?php

declare(strict_types=1);

namespace App\Application\EventSubscriber;

use App\Domain\Event\SubtaskAddedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class SubtaskAddedEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private MailerInterface $mailer,
    ) {
    }

    public static function getSubscribedEvents()
    {
        return [
            SubtaskAddedEvent::class => 'onSubtaskAdded',
        ];
    }

    public function onSubtaskAdded(SubtaskAddedEvent $event)
    {
        $message = sprintf('Subtask %s added to task %s', $event->child->getId(), $event->parent->getId());
        $email = (new Email())
            ->from('hello@example.com')
            ->to('user@example.com')
            ->subject('Тестовое письмо')
            ->text($message)
            ->html('<p>Привет! Это <strong>HTML</strong> письмо.</p>');
        $this->mailer->send($email);
    }
}
