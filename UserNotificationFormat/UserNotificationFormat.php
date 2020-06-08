<?php

namespace GaylordP\UserBundle\UserNotificationFormat;

class UserNotificationFormat implements UserNotificationFormatInterface
{
    private $notificationsFormat = [];

    public function addUserNotificationFormat(UserNotificationFormatInterface $notificationFormat): void
    {
        $this->notificationsFormat[] = $notificationFormat;
    }

    public function format(array $notifications): array
    {
        foreach ($this->notificationsFormat as $notificationFormat) {
            $notifications = $notificationFormat->format($notifications);
        }

        $notifications = $this->userBundleNotificationFormat($notifications);

        return $notifications;
    }

    private function userBundleNotificationFormat(array $notifications): array
    {
        return $notifications;
    }
}
