<?php

declare(strict_types=1);

namespace App\Application\EventSubscriber;

use App\Domain\Event\StatusChangedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class StatusChangedEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private MailerInterface $mailer,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            StatusChangedEvent::class => 'onStatusChanged',
        ];
    }

    public function onStatusChanged(StatusChangedEvent $event)
    {
        $email = (new Email())
            ->from('hello@example.com')
            ->to('user@example.com')
            ->subject('Тестовое письмо')
            ->text(sprintf('Привет! Таска %s переведена в статус done.', $event->taskId))
            ->html('<p>Привет! Это <strong>HTML</strong> письмо.</p>');
        $this->mailer->send($email);
    }
}
