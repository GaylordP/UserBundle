<?php

namespace GaylordP\UserBundle\NotificationFormat;

class NotificationFormat
{
    private $notificationsFormat = [];

    public function addNotificationFormat(NotificationFormatInterface $notificationFormat): void
    {
        $this->notificationsFormat[] = $notificationFormat;
    }

    public function format(array $notifications): array
    {
        dump($this->notificationsFormat);
    }
}
