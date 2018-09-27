<?php

namespace App\Http\Controllers\Auth;

use Session;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use App\Http\Controllers\Controller;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */
    
    use AuthenticatesUsers {
        redirectPath as traitRedirectPath;
    }

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';
    
    public function username()
    {
        return 'username';
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        
        $this->middleware('guest', ['except' => 'logout']);
        parent::__construct();
    }
    
    protected function credentials($request)
    {
        $credentials = $request->only($this->username(), 'password');
        $field = filter_var($credentials[$this->username()], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        
        $newCredentials = [
            $field => $credentials[$this->username()],
            'password' => $credentials['password']
        ];
        
        return $newCredentials;
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
}
