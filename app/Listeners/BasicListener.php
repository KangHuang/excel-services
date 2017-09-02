<?php

namespace App\Listeners;


class BasicListener
{
    /**
     * The Statut instance.
     *
     * @var Statut
     */
    protected $statut;

    /**
     * Create the event listener.
     *
     * @param Statut $statut  
     * @return void
     */
    public function __construct(Statut $statut)
    {
        $this->statut = $statut;
    }
}
