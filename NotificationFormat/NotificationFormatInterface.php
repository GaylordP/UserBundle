<?php

namespace GaylordP\UserBundle\NotificationFormat;

interface NotificationFormatInterface
{
    public function format(array $notifications): array;
}
