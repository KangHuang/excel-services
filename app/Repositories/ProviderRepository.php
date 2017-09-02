<?php

namespace App\Repositories;

use App\Models\User,
    App\Models\Role,
    App\Models\Provider;

class ProviderRepository extends BaseRepository {

    /**
     * Create a new UserRepository instance.
     *
     * @return void
     */
    public function __construct(
    Provider $provider) {
        $this->model = $provider;
    }

    /**
     * Save the User.
     *
     * @param  App\Models\User $provider
     * @param  Array  $inputs
     * @return void
     */
    private function save($provider, $inputs) {
        if (isset($inputs['seen'])) {
            $provider->seen = $inputs['seen'] == 'true';
        } else {

            $provider->username = $inputs['username'];
            $provider->email = $inputs['email'];
        }

        $provider->save();
    }

    /**
     * Create a user.
     *
     * @param  array  $inputs
     * @param  int    $confirmation_code
     * @return App\Models\User 
     */
    public function store($inputs, $confirmation_code = null) {
        $provider = new $this->model;

        $provider->password = bcrypt($inputs['password']);

        if ($confirmation_code) {
            $provider->confirmation_code = $confirmation_code;
        } else {
            $provider->confirmed = true;
        }

        $this->save($provider, $inputs);

        return $provider;
    }

    /**
     * Update a user.
     *
     * @param  array  $inputs
     * @param  App\Models\User $provider
     * @return void
     */
    public function update($inputs, $provider) {
        $provider->confirmed = isset($inputs['confirmed']);

        $this->save($provider, $inputs);
    }

    /**
     * Get statut of authenticated user.
     *
     * @return string
     */
    public function getStatut() {
        return session('statut');
    }

    /**
     * Confirm a user.
     *
     * @param  string  $confirmation_code
     * @return App\Models\User
     */
    public function confirm($confirmation_code) {
        $provider = $this->model->whereConfirmationCode($confirmation_code)->firstOrFail();

        $provider->confirmed = true;
        $provider->confirmation_code = null;
        $provider->save();
    }

}
