<?php

namespace App\Http\Controllers\Auth;

use Session;
use Auth;

use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\RegisterFormRequest;
use App\Models\User;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers {
        redirectPath as traitRedirectPath;
    }

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //parent::__construct();
        $this->middleware('guest');
    }

    protected function validator(array $data) {
        $registerFormRequest = new RegisterFormRequest();
        return Validator::make($data, $registerFormRequest->rules());
    }

    public function redirectPath()
    {
        $redirectPath = $this->traitRedirectPath();

        if (Session::get('redirectToAfterAuth')) {
            $redirectPath = Session::get('redirectToAfterAuth');
            Session::forget('redirectToAfterAuth');
        }

        return $redirectPath;
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        $user = new User();
        $user->fill([
            'username' => str_replace('@', '_', $data['email']),
            'email' => $data['email']
        ]);
        $user->setPassword($data['password']);
        $user->confirmation_code = str_random(32);

        $user->createUser();

        session(['plan' => $data['plan']]);

        return $user;
    }
}
