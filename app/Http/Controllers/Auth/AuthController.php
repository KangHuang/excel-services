<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Repositories\UserRepository,
    App\Repositories\ProviderRepository;
use App\Jobs\SendMail;
use App\Models\User, App\Models\Provider;

class AuthController extends Controller {

    use AuthenticatesAndRegistersUsers,
        ThrottlesLogins;

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('guest', ['except' => 'getLogout']);
    }

    /**
     * Handle a login request to the application.
     *
     * @param  App\Http\Requests\LoginRequest  $request
     * @param  Guard  $auth
     * @return Response
     */
    public function postLogin(
    LoginRequest $request, Guard $auth) {
        $twoRole = $request->input('role');
        if($twoRole == 'pro')
            $auth = auth()->guard('providers');
        else
            $auth = auth()->guard('users');            
        $logValue = $request->input('log');

        $logAccess = filter_var($logValue, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $throttles = in_array(
                ThrottlesLogins::class, class_uses_recursive(get_class($this))
        );

        if ($throttles && $this->hasTooManyLoginAttempts($request)) {
            return redirect('/auth/login')
                            ->with('error', trans('front/login.maxattempt'))
                            ->withInput($request->only('log'));
        }

        $credentials = [
            $logAccess => $logValue,
            'password' => $request->input('password')
        ];

        if (!$auth->validate($credentials)) {
            if ($throttles) {
                $this->incrementLoginAttempts($request);
            }

            return redirect('/auth/login')
                            ->with('error', trans('front/login.credentials'))
                            ->withInput($request->only('log'));
        }

        $user = $auth->getLastAttempted();

        if ($user->confirmed) {
            if ($throttles) {
                $this->clearLoginAttempts($request);
            }
            $request->session()->put('twoRole', $twoRole);
            $auth->login($user, $request->has('memory'));

            if ($request->session()->has('user_id')) {
                $request->session()->forget('user_id');
            }

            return redirect('/');
        }

        $request->session()->put('user_id', $user->id);
        $request->session()->put('twoRole',$twoRole);

        return redirect('/auth/login')->with('error', trans('front/verify.again'));
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  App\Http\Requests\RegisterRequest  $request
     * @param  App\Repositories\UserRepository $user_gestion
     * @return Response
     */
    public function postRegister(
    RegisterRequest $request, UserRepository $user_gestion, ProviderRepository $provider_gestion) {
        $user = new User();
        $provider = new Provider();
        
        if ($request->input('role') == 'use') {
            $user = $user_gestion->store(
                    $request->all(), $confirmation_code = str_random(30)
                    );
            $this->dispatch(new SendMail($user,$provider,0));
            $request->session()->put('twoRole','use');

        } else{
            $provider = $provider_gestion->store($request->all(), $confirmation_code = str_random(30));
            $this->dispatch(new SendMail($user,$provider,1));
            $request->session()->put('twoRole','pro');
        }

        return redirect('/auth/login')->with('error', trans('front/verify.message'));
    }

    /**
     * Handle a confirmation request.
     *
     * @param  App\Repositories\UserRepository $user_gestion
     * @param  string  $confirmation_code
     * @return Response
     */
    public function getConfirm(
    UserRepository $user_gestion, ProviderRepository $provider_gestion, $confirmation_code) {
        if(session()->get('twoRole')=='use')
            $user = $user_gestion->confirm($confirmation_code);
        else
            $provider = $provider_gestion->confirm($confirmation_code);

        return redirect('/auth/login')->with('error', trans('front/verify.success'));
    }

    /**
     * Handle a resend request.
     *
     * @param  App\Repositories\UserRepository $user_gestion
     * @param  Illuminate\Http\Request $request
     * @return Response
     */
    public function getResend(
    UserRepository $user_gestion, ProviderRepository $provider_gestion, Request $request) {
        $user = new User();
        $provider = new Provider();
        if ($request->session()->has('user_id')) {
            if($request->session()->get('twoRole')=='use'){
                $user = $user_gestion->getById($request->session()->get('user_id'));
                $this->dispatch(new SendMail($user,$provider,0));
            }
            else{
                $provider = $provider_gestion->getById($request->session()->get('user_id'));
                $this->dispatch(new SendMail($user,$provider,1));
            }
            return redirect('/auth/login')->with('error', trans('front/verify.resend'));
        }


        return redirect('/');
    }

}
