<?php

namespace App\Policies;

use App\Models\Service;
use App\Models\Provider;

class ProviderPolicy
{

    /**
     * Determine if the given post can be changed by the user.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Post  $post
     * @return bool
     */
    public function change(Provider $provider, Service $service)
    {
        return $provider->id === $service->provider_id;
    }

}
