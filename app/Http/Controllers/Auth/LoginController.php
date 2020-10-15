<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Middleware\AuthenticatesUsers;

use Auth;
use Cache;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Session;

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

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/admin/acl';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Logout
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function logout(Request $request)
    {
        $session = Session::where('session_id',session('session_id'))->first();
        if(isset($session))
        {
            $session->logout_date = date('Y-m-d H:i:s');
            $session->status = 'I';
            $session->save();
        }

        if(isset(auth()->user()->id)){
            $user = User::find((auth()->user()->id));
            $user->is_online = 0;
            $user->save();           
        }

        Auth::logout();
        session()->flush();
        return redirect('/');
    }

}
