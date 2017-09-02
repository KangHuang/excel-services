<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Repositories\UserRepository,
    App\Repositories\ProviderRepository;
use App\Jobs\SendMail;
use App\Models\User, App\Models\Provider;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\View\Factory;
use Illuminate\Support\Facades\Password;
use Illuminate\Mail\Message;
use App\Http\Requests\ResetLinkRequest;
use App\Http\Requests\ResetFormRequest;

class UserAuthController extends Controller {

    use AuthenticatesAndRegistersUsers,ThrottlesLogins,ResetsPasswords {
        AuthenticatesAndRegistersUsers::guestMiddleware insteadof ResetsPasswords;
        AuthenticatesAndRegistersUsers::getGuard insteadof ResetsPasswords;      
        AuthenticatesAndRegistersUsers::redirectPath insteadof ResetsPasswords;
        }

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
        $logVal = $request->input('log');

        $logIdent = filter_var($logVal, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $tryAttempt = in_array(
                ThrottlesLogins::class, class_uses_recursive(get_class($this))
        );

        if ($tryAttempt && $this->hasTooManyLoginAttempts($request)) {
            return redirect('/auth/login')
                            ->with('error', trans('front/login.maxattempt'))
                            ->withInput($request->only('log'));
        }

        $credentialArray = [
            $logIdent => $logVal,
            'password' => $request->input('password')
        ];

        if (!$auth->validate($credentialArray)) {
            if ($tryAttempt) {
                $this->incrementLoginAttempts($request);
            }

            return redirect('/auth/login')
                            ->with('error', trans('front/login.credentials'))
                            ->withInput($request->only('log'));
        }

        $user = $auth->getLastAttempted();

        if ($user->confirmed) {
            if ($tryAttempt) {
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
     * @param  App\Repositories\UserRepository $user_handler
     * @return Response
     */
    public function postRegister(
    RegisterRequest $request, UserRepository $user_handler, ProviderRepository $provider_handler) {
        $user = new User();
        $provider = new Provider();
        
        if ($request->input('role') == 'use') {
            $user = $user_handler->store(
                    $request->all(), $confirmation_code = str_random(30)
                    );
            $this->dispatch(new SendMail($user,$provider,0));
            $request->session()->put('twoRole','use');

        } else{
            $provider = $provider_handler->store($request->all(), $confirmation_code = str_random(30));
            $this->dispatch(new SendMail($user,$provider,1));
            $request->session()->put('twoRole','pro');
        }

        return redirect('/auth/login')->with('error', trans('front/verify.message'));
    }

    /**
     * Handle a confirmation request.
     *
     * @param  App\Repositories\UserRepository $user_handler
     * @param  string  $confirmation_code
     * @return Response
     */
    public function getConfirm(
    UserRepository $user_handler, ProviderRepository $provider_handler, $confirmation_code) {
        if(session()->get('twoRole')=='use')
            $user = $user_handler->confirm($confirmation_code);
        else
            $provider = $provider_handler->confirm($confirmation_code);

        return redirect('/auth/login')->with('error', trans('front/verify.success'));
    }

    /**
     * Handle a resend request.
     *
     * @param  App\Repositories\UserRepository $user_handler
     * @param  Illuminate\Http\Request $request
     * @return Response
     */
    public function getResend(
    UserRepository $user_handler, ProviderRepository $provider_handler, Request $request) {
        $user = new User();
        $provider = new Provider();
        if ($request->session()->has('user_id')) {
            if($request->session()->get('twoRole')=='use'){
                $user = $user_handler->getById($request->session()->get('user_id'));
                $this->dispatch(new SendMail($user,$provider,0));
            }
            else{
                $provider = $provider_handler->getById($request->session()->get('user_id'));
                $this->dispatch(new SendMail($user,$provider,1));
            }
            return redirect('/auth/login')->with('error', trans('front/verify.resend'));
        }


        return redirect('/');
    }
    
     /**
     * Send a reset link to the given user.
     *
     * @param  ResetLinkRequest  $request
     * @param  Illuminate\View\Factory $view
     * @return Response
     */
    public function postEmail(
    ResetLinkRequest $request, Factory $view) {
            $view->composer('emails.auth.password', function($view) {
                $view->with([
                    'title' => trans('front/password.email-title'),
                    'intro' => trans('front/password.email-intro'),
                    'link' => trans('front/password.email-link'),
                    'expire' => trans('front/password.email-expire'),
                    'minutes' => trans('front/password.minutes'),
                ]);
            });
            IF ($request->input('role') == 'use'){
                 $response = Password::broker('users')->sendResetLink($request->only('email'), function (Message $message) {
                    $message->subject(trans('front/password.reset'));
                });
                $request->session()->put('twoRole', 'use');
            }
            else {
                $response = Password::broker('providers')->sendResetLink($request->only('email'), function (Message $message) {
                    $message->subject(trans('front/password.reset'));
                });
                $request->session()->put('twoRole', 'pro');
            }
        switch ($response) {
            case Password::RESET_LINK_SENT:
                return redirect()->back()->with('status', trans($response));

            case Password::INVALID_USER:
                return redirect()->back()->with('error', trans($response));
        }
    }

    /**
     * Reset the given user's password.
     * 
     * @param  ResetFormRequest  $request
     * @return Response
     */
    public function postReset(ResetFormRequest $request) {
        $credentials = $request->only(
                'email', 'password', 'password_confirmation', 'token'
        );

        if($request->session()->get('twoRole')=='use')
            $response = Password::broker('users')->reset($credentials, function($user, $password) {
                    $this->resetPassword($user, $password);
                });
        else
            $response = Password::broker('providers')->reset($credentials, function($user, $password) {
                    $this->resetPassword($user, $password);
                });

        switch ($response) {
            case Password::PASSWORD_RESET:
                return redirect()->to('/')->with('ok', trans('passwords.reset'));

            default:
                return redirect()->back()
                                ->with('error', trans($response))
                                ->withInput($request->only('email'));
        }
    }

}
