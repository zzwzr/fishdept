<?php

declare(strict_types=1);

namespace App\Listener;

use Hyperf\Event\Annotation\Listener;
use Psr\Container\ContainerInterface;
use Hyperf\Event\Contract\ListenerInterface;

#[Listener]
class UserRegisteredListener implements ListenerInterface
{
    public function __construct(protected ContainerInterface $container)
    {
    }

    public function listen(): array
    {
        return [
            \App\Event\UserRegistered::class,
        ];
    }

    public function process(object $event): void
    {
        $smsService = $this->container->get(\App\Service\SmsService::class);
        $notificationService = $this->container->get(\App\Service\NotificationService::class);

        $event->user;

        $smsService->sendWelcomeSms($event->user->mobile);
        $notificationService->sendWelcomeNotification($event->user->id);
    }
}
