<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Foundation\Auth\RedirectsUsers;
use Illuminate\Foundation\Auth\ThrottlesLogins;

use DB;
use App\Models\Access;
use App\Models\UserType;
use App\Models\PartnerType;
use App\Models\Partner;
use App\Models\User;
use App\Models\VerificationCode;
use App\Models\Country;
use App\Models\Session;
use Mail;
use Carbon\Carbon;
use App\TokenStore\TokenCache;

trait AuthenticatesUsers
{
    use RedirectsUsers, ThrottlesLogins;

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            $user = $this->guard()->user();
            // if($user->is_verified_email == 0){
            //     Auth::logout();
            //     Cache::flush();
            //     return view('auth.verification',compact('user','request'));
            // }
            $verify_mobile = Country::where('name',$user->country)->first()->validate_number;
            if($user->is_verified_mobile == 0 && $verify_mobile == 1 && isset($user->mobile_number) && $user->mobile_number != '-'){
                $old_codes = VerificationCode::where('mobile_number',$user->country_code.$user->mobile_number)->where('status','A')->get();
                foreach ($old_codes as $old_code) {
                    $old_code->status = 'D';
                    $old_code->save();
                }
                $code = new VerificationCode;
                $code->mobile_number = $user->country_code.$user->mobile_number; 
                $code->verification_code = rand(10000,99999); 
                $code->request_date = date('Y-m-d H:i:s');
                $code->save();

                $params = array(
                    'user'      => 'GO3INFOTECH',
                    'password'  => 'TA0828g3i',
                    'sender'    => 'GoETU',
                    'SMSText'   => 'Your GOETU verification code is '.$code->verification_code,
                    'GSM'       => str_replace("-","",$code->mobile_number),
                );
                $send_url = 'https://api2.infobip.com/api/v3/sendsms/plain?' . http_build_query($params);
                $send_response = file_get_contents($send_url);

                Auth::logout();
                //session()->flush();
                return view('auth.verification',compact('user','request'));
            }

            /**
             * Verify user if mobile number does not exist
             */
            if($user->is_verified_email == 0){
                $userUpdate = User::find($user->id);
                $userUpdate->is_verified_email = 1;
                if(!isset($user->mobile_number))
                {
                    $userUpdate->is_verified_mobile = 1;
                }
                $userUpdate->save();
            
            }


            return $this->sendLoginResponse($request);
        } 


        $userTest = User::where('email_address',$request->username)->get();

        if(count($userTest)>0)
        {
            $this->guard()->logout();
            $request->session()->invalidate();
            Cache::flush();
            return redirect('/login')->with('invalid','Invalid Password. Please input the correct password and try again.');    
        }   

        $userTest = User::where('username',$request->username)->get();
        if(count($userTest)>0)
        {
            $this->guard()->logout();
            $request->session()->invalidate();
            Cache::flush();
            return redirect('/login')->with('invalid','Invalid Password. Please input the correct password and try again.');    
        }   


        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    public function request_code(Request $request)
    {
        $old_codes = VerificationCode::where('mobile_number',$request->mobile_number)->where('status','A')->get();
        foreach ($old_codes as $old_code) {
            $old_code->status = 'D';
            $old_code->save();
        }
        $code = new VerificationCode;
        $code->mobile_number = $request->mobile_number; 
        $code->verification_code = rand(10000,99999); 
        $code->request_date = date('Y-m-d H:i:s');
        $code->save();     
        $params = array(
            'user'      => 'GO3INFOTECH',
            'password'  => 'TA0828g3i',
            'sender'    => 'GoETU',
            'SMSText'   => 'Your GOETU verification code is '.$code->verification_code,
            'GSM'       => str_replace("-","",$code->mobile_number),
        );
        $send_url = 'https://api2.infobip.com/api/v3/sendsms/plain?' . http_build_query($params);
        $send_response = file_get_contents($send_url);
        if (!($send_response != '')) {
            return Array('success' => false);
        } else {
            if (strstr($send_response, '<status>0</status>') === false) {
                return Array('success' => false);
            } else {
                return Array('success' => true);
            }
        }
    }

    public function verify_code(Request $request)
    {
        $code = VerificationCode::where('mobile_number',$request->mobile_number)->where('status','A')->where('verification_code',$request->verification_code)->first();
        if(isset($code))
        {          
            $code->status = 'S';
            $code->verified_date = date('Y-m-d H:i:s');
            $code->save();  
            if ($this->attemptLogin($request)) {
                $user = $this->guard()->user();
                $userUpdate = User::find($user->id);
                $userUpdate->is_verified_mobile = 1;
                $userUpdate->is_verified_email = 1;
                $userUpdate->save();
                $this->sendLoginResponse($request);
                return Array('success' => true); 
            }
        }
        return Array('success' => false); 
    }   

    public function passwordReset(Request $request)
    {
        $mobile = substr($request->email_address,3);

        $mobile_number = strlen($mobile) == 10 ? preg_replace("/^(\d{3})(\d{3})(\d{4})$/", "-$1-$2-$3", $mobile) : preg_replace("/^(\d{1})(\d{3})(\d{3})(\d{4})$/", "-$1-$2-$3-$4", $mobile);
        if ($mobile_number == "") $mobile_number = $request->email_address; // to avoid getting multiple user records
        //$user = User::where('email_address',$request->email_address)->first() !== null ? User::where('email_address',$request->email_address)->first() : User::where('mobile_number',$mobile_number)->first();
        $user = User::where('email_address',$request->email_address)
                ->orWhere('mobile_number', 'LIKE', '%'. $mobile_number)
                ->orWhere('username', $request->email_address)
                ->get();

        if(count($user)==0){
            return redirect('/login')->with('failed','User email does not exist!');
        }

        if(count($user)>1)
        {
            return redirect('/login')->with('failed','Reset password not allowed using email address. System found '. count($user).' accounts under this email. Please use username instead.');    
        }
        
        $user= $user[0];
        
        $default_password = rand(1111111, 99999999);
        $default_encrypted_password = bcrypt($default_password);

        $user->password = $default_encrypted_password;
        $user->is_online = 0;
        $user->save();

        if (preg_match("/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/",$user->email_address)) {
            $data = array(
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'password' => $default_password,
                'email_address' => $user->email_address,
                'username' => $user->username,
            );
    
            Mail::send(['html'=>'mails.resetpassword'],$data,function($message) use ($data){
    
                $message->to($data['email_address'],$data['first_name'].' '.$data['last_name']);
                $message->subject('[GoETU] Password Reset');
                $message->from('no-reply@goetu.com');
            });
    
            if (Mail::failures()) {
                return redirect('/login')->with('failed','Failed to send email.');
            }
        } else {
            $mobile_number = $user->country_code.'-'.$user->mobile_number;
            $params = array(
                'user'      => 'GO3INFOTECH',
                'password'  => 'TA0828g3i',
                'sender'    => 'GoETU',
                'SMSText'   => 'Hi '.$user->first_name. ' ' .$user->last_name. ', GoETU Password Reset. Your password is: ' .$default_password,
                'GSM'       => str_replace("-","",$mobile_number),
            );
            $send_url = 'https://api2.infobip.com/api/v3/sendsms/plain?' . http_build_query($params);
            $send_response = file_get_contents($send_url);

            if (!($send_response != '') || 
                strstr($send_response, '<status>0</status>') === false) {
                return redirect('/login')->with('failed','Failed to send message.');
            }
        }
        
        return redirect('/login')->with('success','Password sent to mail');
    }  
    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function validateLogin(Request $request)
    {
        $this->validate($request, [
            $this->username() => 'required|string',
            'password' => 'required|string',
        ]);
    }

    /**
     * Attempt to log the user into the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {
        // $loginEmail = $this->guard()->attempt(
        //     $this->credentials($request), $request->filled('remember')
        // );

        // $loginMobile = $this->guard()->attempt(
        //     $this->credentialsMobile($request), $request->filled('remember')
        // );

        // return $loginEmail || $loginMobile;

        $login = $this->guard()->attempt(
            $this->credentials($request), $request->filled('remember')
        );

        $loginEmail = $this->guard()->attempt(
            $this->credentialsEmail($request), $request->filled('remember')
        );

        return $login || $loginEmail;

    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        return array_merge($request->only($this->username(), 'password'), ['status' => 'A']);
    }

    protected function credentialsEmail(Request $request)
    {
        return [
            'email_address' => $request->username,
            'password' => $request->password,
            'status' => 'A',
            'is_original_partner' => 0
        ];

    }

    protected function credentialsMobile(Request $request)
    {
        $number = str_replace("-","",$request->email_address);

        $mobile = strpos($number,"86") === false ? substr($number, strlen($number) - 10) : substr($number, strlen($number) - 11); // US | PH

        $mobile_number = strlen($mobile) == 10 ? preg_replace("/^(\d{3})(\d{3})(\d{4})$/", "-$1-$2-$3", $mobile) : preg_replace("/^(\d{1})(\d{3})(\d{3})(\d{4})$/", "-$1-$2-$3-$4", $mobile);

        return [
            'mobile_number' => $mobile_number,
            'password' => $request->password,
            'status' => 'A'
        ];
    }

    /**
     * Send the response after the user was authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    protected function sendLoginResponse(Request $request)
    {
        $request->session()->regenerate();

        $this->clearLoginAttempts($request);

        return $this->authenticated($request, $this->guard()->user())
                ?: redirect()->intended($this->redirectPath());
    }

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        //remove previous access rights
        //session()->flush();
        
        $userTest = User::where('email_address',$request->username)->get();

        if(count($userTest)>1)
        {
            $this->guard()->logout();
            $request->session()->invalidate();
            Cache::flush();
            return redirect('/login')->with('invalid','System found '. count($userTest).' accounts under this email. Please use username instead.');    
        }     

        $check = User::find($user->id);
        if($check->is_online == 1 && $check->user_type_id != '1')
        {
            $activity = new Carbon($check->last_activity);
            $now = Carbon::now();
            $difference = $activity->diffInMinutes($now);
            if($difference <= 20){
                $this->guard()->logout();
                $request->session()->invalidate();
                Cache::flush();
                return redirect('/login')->with('exist','User is currently logged in');                
            }
        }else{
            $check->is_online = 1;
        }

        //ADD FOR MULTI COMPANY
        if($user->company_id == -1 || $user->company_id == 0){
            foreach($check->companies as $company){
                $user->company_id = $company->company_id;
                $check->company_id = $user->company_id;
                break;
            }
        }

        $user_type = "";

        foreach($check->departments as $dep){
            if($dep->user_type->create_by == "SYSTEM"){
                $user_type .= $dep->user_type_id.",";
            }else{
                if($dep->user_type->company_id == $user->company_id){
                     $user_type .= $dep->user_type_id.",";
                     if($dep->user_type->create_by != "SYSTEM"){
                        $user->reference_id = $check->company_id;
                        $check->reference_id = $check->company_id;
                     }
                }   
            }
        }



        $user_type = $user_type == "" ? $user->user_type_id : substr($user_type, 0, strlen($user_type) - 1);
        $check->user_type_id = $user_type;
        $user->user_type_id = $user_type;
        $check->save();

        //Generate cache for access rights
        $ids = explode(",",$user->user_type_id);
        $all_user_access = Access::generateAllUserAccess($user->user_type_id);

        //create partner type access
        $partner_type_all_access="";
        $user_types = UserType::where('create_by','SYSTEM')->where('status','A')->get();
        foreach($user_types as $user_type){
            if (isset($all_user_access[strtolower($user_type->description)])){
                if(strpos($all_user_access[strtolower($user_type->description)], 'add') !== false){
                    $id = PartnerType::where('name',$user_type->description)->get();
                    $partner_type_all_access = $partner_type_all_access .  $id[0]->id . ",";
                }        
            }
        }

        if (strlen($partner_type_all_access) > 0){
            $partner_type_all_access = substr($partner_type_all_access, 0, strlen($partner_type_all_access) - 1);         
        }

        //create partner type access excluding leads and prospects
        $partner_type_access="";
        $user_types = UserType::where('create_by','SYSTEM')->where('status','A')->where('description','NOT LIKE','LEAD')->where('description','NOT LIKE','PROSPECT')->where('description','NOT LIKE','Merchant')->get();
        foreach($user_types as $user_type){
            if (isset($all_user_access[strtolower($user_type->description)])){
                if(strpos($all_user_access[strtolower($user_type->description)], 'add') !== false){
                    $id = PartnerType::where('name',$user_type->description)->get();
                    $partner_type_access = $partner_type_access .  $id[0]->id . ",";
                }        
            }
        }

        //create partner type access excluding leads and prospects
        $partner_type_access_view="";
        $user_types = UserType::where('create_by','SYSTEM')->where('status','A')->where('description','NOT LIKE','LEAD')->where('description','NOT LIKE','PROSPECT')->where('description','NOT LIKE','Merchant')->get();
        foreach($user_types as $user_type){
            if (isset($all_user_access[strtolower($user_type->description)])){
                if(strpos($all_user_access[strtolower($user_type->description)], 'view') !== false){
                    $id = PartnerType::where('name',$user_type->description)->get();
                    $partner_type_access_view = $partner_type_access_view .  $id[0]->id . ",";
                }        
            }
        }

        if (strlen($partner_type_access) > 0){
            $partner_type_access = substr($partner_type_access, 0, strlen($partner_type_access) - 1);         
        }

        $userType = UserType::find($user->user_type_id);
        $user_type_display = "";
        if ($userType->create_by!=="SYSTEM") $user_type_display =$userType->display_name;

        $permissions = Access::getPermissions($user->user_type_id);
        $permissions = array_flip($permissions);  

        $partner_type = Partner::with('partner_company')->find($user->reference_id);
        $not_parent = Partner::with('partner_company')->find($user->reference_id);
        $partner_type_not_parent = -1;
        if (isset($not_parent->partner_type_id))
        {
            $partner_type_not_parent = $user->reference_id == -1 ? -1 : $not_parent->partner_type_id;
        }
        $partner_type_id = -1;
        $company_name = "NO COMPANY";
        if (isset($partner_type->partner_company->company_name)) $company_name=$partner_type->partner_company->company_name;
        if (isset($partner_type->partner_type_id)) $partner_type_id = $partner_type->partner_type_id;

        $user_check = User::where('id',$user->id)->isInternal()->first();   
        $is_internal = true;
        if (!isset($user_check)) {
            $is_internal = false;
        }
        
        session(['partner_type_all_access' => $partner_type_all_access]);
        session(['partner_type_access' => $partner_type_access]);
        session(['partner_type_access_view' => $partner_type_access_view]);
        session(['all_user_access' => $all_user_access]);
        session(['username' => $user->username]);
        session(['email' => $request['email']]);
        session(['user_type_desc' => $userType->description]);
        session(['user_type_display' => $user_type_display]);
        session(['permissions' => $permissions]);
        session(['partner_type_id' => $partner_type_id]);
        session(['company_name' => $company_name]);
        session(['partner_type_not_parent' => $partner_type_not_parent]);
        session(['is_internal' => $is_internal]);

        //logging of info to Session table
        $session = new Session;
        $session->user_id = $user->id;
        $session->session_id = $user->id.time();
        $session->session_date = date('Y-m-d H:i:s');
        $session->login_date = date('Y-m-d H:i:s');
        $session->ip_address = \Request::ip();
        $session->status = 'A';
        $session->save();

        session(['session_id' => $session->session_id]);
    }

    /**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws ValidationException
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        throw ValidationException::withMessages([
            $this->username() => [trans('auth.failed')],
        ]);
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        // return 'email_address';
        return 'username';
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $session = Session::where('session_id',session('session_id'))->first();
        if(isset($session))
        {
            $session->logout_date = date('Y-m-d H:i:s');
            $session->status = 'I';
            $session-save();
        }

        $this->guard()->logout();    
        $request->session()->invalidate();

        $tokenCache = new TokenCache();
        $tokenCache->clearTokens();

        Cache::flush();
        return redirect('/');
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard();
    }
}
