<?php

namespace App\Listeners;

use App\Events\UserAccess as UserAccessEvent;

class UserAccess extends BasicListener
{
    /**
     * Handle the event.
     *
     * @param  UserAccess  $event
     * @return void
     */
    public function handle(UserAccessEvent $event)
    {
        $this->statut->setStatut();
    }
}
