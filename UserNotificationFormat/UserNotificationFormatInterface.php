<?php

namespace GaylordP\UserBundle\UserNotificationFormat;

interface UserNotificationFormatInterface
{
    public function format(array $notifications): array;
}
