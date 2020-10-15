<?php

namespace App\Http\Controllers\Extras;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

use App\Models\User;
use App\Models\ChatFriendRequest;
use App\Models\Notification;
use App\Models\Partner;
use App\Models\PartnerType;
use App\Models\UserType;
use App\Models\UsZipCode;
use App\Models\PhZipCode;
use App\Models\CnZipCode;
use DB;
use Hash;

use App\Models\Suggestion;

class ExtrasController extends Controller
{
    public function notification(){
    	$active_class = "new";
    	if(isset($_GET['tab'])) $active_class=$_GET['tab'];
    	$new_message_count = Notification::get_new_messages_count();

    	$notification = Notification::get_notifications();
	
		$assignment = Notification::get_available_task_assigments();

        return view('extras.notification',compact('new_message_count','notification','assignment','active_class'));
    }
    public function updateStarred(){
        DB::transaction(function(){
	        $updateNotification = Notification::find(Input::get('id'));
	        $updateNotification->update_by = auth()->user()->username;
	        $updateNotification->is_starred = Input::get('is_starred');

	        if(!$updateNotification->save()){
				return response()->json(array(
		            'success'       => false, 
		            'msg'           => "Unable to update notification", 
		        ), 200);
        	}
        });
    	return response()->json(array(
            'success'       => true, 
            'msg'           => "Notification has been updated!", 
        ), 200);
    }
    public function tagAndRedirect(){
        DB::transaction(function(){
	        $updateNotification = Notification::find(Input::get('id'));
	        $updateNotification->update_by = auth()->user()->username;
	        $updateNotification->status = 'R';

	        if(!$updateNotification->save()){
				return response()->json(array(
		            'success'       => false, 
		            'msg'           => "Unable to update notification", 
		        ), 200);
        	}
        });
    	return response()->json(array(
            'success'       => true, 
            'msg'           => "Notification has been updated!", 
        ), 200);
    }
    public function chatCenter(){
        return view('extras.chatCenter');
    }
    public function friendRequest(){
    	$friendRequest = DB::select(DB::raw("SELECT CONCAT(u.first_name,' ',u.last_name) name, u.id uid, cfr.id id
    					FROM chat_friend_requests as cfr
    					JOIN users as u ON cfr.sender_id = u.id
    					WHERE cfr.recipient = ".auth()->user()->id." AND cfr.is_accepted_or_not = 0 AND u.status = 'A'
    	    			ORDER BY cfr.created_at"));
        return view('extras.friendRequest', compact('friendRequest'));
    }
    public function addUsers (Request $request) {
		$userId = auth()->user()->id;
		$userTypeId = auth()->user()->user_type_id;
		$companyId = auth()->user()->company_id;
		$referenceId = auth()->user()->reference_id;

		$searchItem = isset($request->contact) ? $request->contact : null;

        $dep = User::getDepartmentsByID($userTypeId);
        $is_chat_support = false;
        foreach($dep as $d){
        	if($d->is_chat_support == 1){
				$is_chat_support = true;
        	}
		}

		$adminOwner = UserType::where('description', 'SUPER ADMIN')
			->orWhere('description', 'LIKE', '%OWNER')
			->pluck('id');
		$systemUser = UserType::where('create_by', 'SYSTEM')->pluck('id');
		if ($referenceId != -1) {
			$uplines = Partner::find($referenceId)
				->uplines
				->pluck('id');
			$downlines = Partner::find($referenceId)
				->downlines
				->pluck('id');
		}
		$addedContacts = User::find($userId)
			->requests
			->where('is_accepted_or_not', 1)
			->pluck('sender_id');
		$supportIds = UserType::where('is_chat_support',1)
			->where('status','A')
			->where('company_id', $companyId)
			->pluck('id');

		$query = User::excludeSelf()->isActive();								// SUPER ADMIN || OWNER

		if ($userTypeId == '4' || $userTypeId == '5' 							// ISO || SUBISO
			|| $userTypeId == '6' || $userTypeId == '13' 						// AGENT || SUB-AGENT
			|| $userTypeId == '8') { 											// MERCHANT - BRANCH
			$partnerAccess = $uplines->merge($downlines);
			$chatUsers = User::partnerAccess($partnerAccess)
				->includeSuperAdminOwner($adminOwner)
				->includeContact($addedContacts)
				->nonInternalUsers()
				->excludeSelf()
				->isActive()
				->get();
			$subset1 = $chatUsers->map->only(
				['username', 'full_name', 'email', 'id', 'image', 'company_id'
			]);

			$chatSupports = User::includeChatSupport($supportIds)->get();
			$subset2 = $chatSupports->map->only([
				'username', 'full_name', 'email', 'id', 'image', 'company_id'
			]);

			$merge = $subset1->merge($subset2);
			$results = $merge->sortBy('full_name')->values()->all(); 
		} else {
			$query = User::excludeSelf()->isActive();

			if ($is_chat_support) { 											// CHAT SUPPORT
				$query->whereCompany($companyId)
					->includeSuperAdminOwner($adminOwner)
					->includeContact($addedContacts);
			} else if ($userTypeId == '11') { 									// COMPANY
				$query->internalUsers()
					->includeSuperAdminOwner($adminOwner)
					->partnerAccess($downlines)
					->includeContact($addedContacts);
			} else if (!in_array($userTypeId, $systemUser->values()->all())) { 	// INTERNAL USERS
				$query->internalUsers()
					->includeSuperAdminOwner($adminOwner)
					->includeContact($addedContacts)
					->includeCompany();
			}

			if (isset($searchItem)) {
				$query->searchContact($searchItem);
			}

			$chatUsers = $query->get();
			$subset = $chatUsers->map->only([
				'username', 'full_name', 'email', 'id', 'image', 'company_id'
			]);
	
			$results = $subset->sortBy('full_name')->values()->all();
		}
			
		if ($results) {
	    	return response()->json(array(
	            'data'      => $results, 
	        ), 200);
		} else {
			return response()->json(array(
	            'data'      => 'No results found.', 
		        ), 200);
		}
	}

	public function addToGroup () {
		$added_contacts = Partner::get_added_contacts('contacts',auth()->user()->id);

        if($added_contacts == ''){$added_contacts = 0;}
		
		if (auth()->user()->user_type_id == '1' || auth()->user()->user_type_id == '81') {
			$cmd = DB::raw("SELECT CONCAT(first_name, ' ', last_name) as name, 
					IFNULL(email_address, 'No Email') as email_address, id, image 
					FROM users 
					WHERE id != ".auth()->user()->id." AND 
						CONCAT(first_name,' ',last_name) LIKE '%".Input::get('contact')."%' AND 
						status = 'A' 
					ORDER BY CONCAT(first_name, ' ', last_name) DESC LIMIT 25");
		} else if (auth()->user()->user_type_id == '6' //AGENT
			|| auth()->user()->user_type_id == '13' //SUB-AGENT
			|| auth()->user()->user_type_id == '8') { //MERCHANT
			$cmd = DB::raw("SELECT CONCAT(first_name, ' ', last_name) as name, 
				IFNULL(email_address, 'No Email') as email_address, id, image 
				FROM users 
				WHERE username = 'admin' AND status = 'A' 
				ORDER BY CONCAT(first_name, ' ', last_name) DESC");
		} else {
			$cmd = DB::raw("SELECT CONCAT(first_name, ' ', last_name) as name, 
					IFNULL(email_address, 'No Email') as email_address, id, image 
					FROM users 
					WHERE CONCAT(first_name,' ',last_name) LIKE '%".Input::get('contact')."%' AND 
						(id IN ({$added_contacts}) OR 
						company_id IN ( " . auth()->user()->company_id . " ) OR 
						user_type_id IN (1,81)) AND id != " . auth()->user()->id . " AND 
						status = 'A' 
					ORDER BY CONCAT(first_name, ' ', last_name) DESC LIMIT 25");
		}

        $results = DB::select($cmd);

		if ($results ) {
	    	return response()->json(array(
	            'data'      => $results, 
	        ), 200);
		} else {
			return response()->json(array(
	            'data'      => 'No results found.', 
	        ), 200);
		}
	}

	public function addAsContact () {
		$added_contacts = Partner::get_added_contacts('contacts',auth()->user()->id);

        if($added_contacts == ''){$added_contacts = 0;}

		$results = '';

		if (auth()->user()->user_type_id != '1' && auth()->user()->user_type_id != '81') {
			$cmd = DB::raw("SELECT CONCAT(first_name, ' ', last_name) as name, 
					IFNULL(email_address, 'No Email') as email_address, id, image  
					FROM users 
					WHERE CONCAT(first_name,' ',last_name) 
					LIKE '%".Input::get('add_users')."%' AND 
						id NOT IN ({$added_contacts}) AND
						company_id NOT IN ( " . auth()->user()->company_id . " ) AND 
						user_type_id NOT IN (1,81) AND id != " . auth()->user()->id . " AND
						status = 'A' 
					ORDER BY CONCAT(first_name, ' ', last_name) DESC LIMIT 25");
					
			$results = DB::select($cmd);
		}

		if ($results) {
	    	return response()->json(array(
	            'data'      => $results, 
	        ), 200);
		} else {
			return response()->json(array(
	            'data'      => 'No results found.', 
	        ), 200);
		}
	}

	public function sendFriendRequest(){
		$if_exists = DB::select(DB::raw("SELECT count(id) as counter 
			FROM chat_friend_requests 
			WHERE sender_id = " . auth()->user()->id .
			" AND recipient = " . Input::get('id') . ""));

		$if_sent = DB::select(DB::raw("SELECT count(id) as counter 
			FROM chat_friend_requests 
			WHERE recipient = " . auth()->user()->id . 
			" AND sender_id = " . Input::get('id') . ""));

    	if ($if_exists[0]->counter != 0) {
    		return response()->json(array(
                'success'       => false, 
				'msg'           => "Friend Request already sent.", 
                'url'           => "", 				
            ), 200);
        } elseif ($if_sent[0]->counter != 0) {
        	return response()->json(array(
                'success'       => true, 
                'msg'			=> 'Friend Request pending.',
                'url'           => "/extras/friendRequest", 
            ), 200);
    	} else {
        	DB::transaction(function(){
	        	$friendRequest = new ChatFriendRequest;
	        	$friendRequest->sender_id = auth()->user()->id;
	        	$friendRequest->recipient = Input::get('id');
	        	$friendRequest->is_accepted_or_not = 0;

	        	if(!$friendRequest->save()){
					return response()->json(array(
			            'success'       => false, 
						'msg'           => "Friend Request error!", 
						'url'           => "",
			        ), 200);
	        	}
        	});
        	return response()->json(array(
	            'success'       => true, 
				'msg'           => "Friend Request sent!",
				'url'           => "", 
	        ), 200);
	    }
	}

	function acceptRequest(){
		DB::transaction(function(){
			$friendRequest = ChatFriendRequest::find(Input::get('id'));
	        $friendRequest->is_accepted_or_not = 1;

	        $sender = DB::table('chat_friend_requests')
	        	->select('sender_id')
	        	->where('id',Input::get('id'))
	        	->first();

	        $recipient = DB::table('users')
                ->select('username')
                ->where('id',$sender->sender_id)
                ->first(); 
            $recipient = $recipient == null ? -1 : $recipient->username;
            $original_parent_id = auth()->user()->reference_id == null ? -1 : auth()->user()->reference_id;
            $email_message = auth()->user()->first_name . ' ' . auth()->user()->last_name . ' accepted your contact request.';
            $email_subject = "Contact request for approval.";

            $notifications = new Notification;
            $notifications->partner_id 		= auth()->user()->reference_id == null ? -1 : auth()->user()->reference_id;
            $notifications->source_id 		= auth()->user()->reference_id == null ? -1 : auth()->user()->reference_id;
            $notifications->recipient 		= $recipient;
            $notifications->subject 		= $email_subject;
            $notifications->message 		= $email_message;
            $notifications->status 			= 'N';
            $notifications->create_by 		= auth()->user()->username;
            $notifications->redirect_url 	= '/';
            $notifications->save();

	        if(!$friendRequest->save()){
				return response()->json(array(
		            'success'       => false, 
		            'msg'           => "Friend Request error!", 
		        ), 200);
        	}
        });
        	return response()->json(array(
	            'success'       => true, 
	            'msg'           => "Friend Request accepted!", 
	        ), 200);
	}

	function declineRequest(){
		DB::transaction(function(){
			$friendRequest = ChatFriendRequest::find(Input::get('id'));
	        $friendRequest->is_accepted_or_not = -1;

	        if(!$friendRequest->save()){
					return response()->json(array(
			            'success'       => false, 
			            'msg'           => "Friend Request error!", 
			        ), 200);
	        	}
        });
        	return response()->json(array(
	            'success'       => true, 
	            'msg'           => "Friend Request declined!", 
	        ), 200);
	}

	function changePassword(){
		$user_id = auth()->user()->id;
		return view('extras.changepassword',compact('user_id'));	
	}

	function updatePassword(Request $request, $user_id){
		$this->validate($request,[
                'current_password' => 'required|string|max:255',
                'new_password' => 'required|string|min:6|required_with:confirm_new_password|same:confirm_new_password|different:current_password',
        ]);
        $user = User::find($user_id);
       	if(!Hash::check($request->current_password, $user->password)){
       		return redirect('/extras/changePassword')->with('failed','Invalid current password.');	
       	}
       	$user->password = bcrypt($request->new_password);
        $user->save();
       	return redirect('/')->with('success','Password successfully updated');
	}

	function generalSearch(){
		$results = array();
		$term = $_GET['term'];
		$searchType = $_GET['type'];
		$term = trim(str_replace("'", "", $term));	
		$access = session('all_user_access');
		$admin_access = isset($access['admin']) ? $access['admin'] : "";

		$filter = "";
		$isadmin = true;
        if (strpos($admin_access, 'super admin access') === false){
            $filter = "and p.company_id = ".auth()->user()->company_id; 
            $isadmin = false; 

	        if (auth()->user()->is_original_partner == 1){
	            $partner_access = Partner::get_partners_access(auth()->user()->reference_id); 
		        if ($partner_access==""){$partner_access=auth()->user()->reference_id;}
		        $filter .= " and p.id in ({$partner_access })";
	        }


        }

		if($searchType == "contact"){
	    	$cmd = "SELECT CONCAT(pcon.first_name,' ',pcon.last_name) as name,p.id,'CONTACT'as category,pt.name as type from partners p
					LEFT JOIN partner_types pt on pt.id = p.partner_type_id
					LEFT JOIN partner_contacts pcon on p.id=pcon.partner_id and pcon.is_original_contact=1
					where  CONCAT(pcon.first_name,' ',pcon.last_name) like '%{$term}%' {$filter} Limit 20";

			$records = DB::select(DB::raw($cmd));
			foreach ($records  as $rec)
			{
				$rec->label = $rec->name;
				if($rec->type == 'MERCHANT'){
					$rec->url = env('APP_URL')."/merchants/details/{$rec->id}/profile#ownrinf";
				}elseif($rec->type == 'LEAD'){
					$rec->url = env('APP_URL')."/leads/details/contact/{$rec->id}";
				}elseif($rec->type == 'PROSPECT'){
					$rec->url = env('APP_URL')."/prospects/details/contact/{$rec->id}";
				}else{
					$rec->url = env('APP_URL')."/partners/details/profile/{$rec->id}/profileContactList";
				}
				if (isset($access[strtolower($rec->type)]) && (strpos($access[strtolower($rec->type)], 'view') !== false)){
					$results[] = $rec;
				}
			}
		}

		if($searchType == "partner"){
			$partner_types = PartnerType::where('status','A')->where('name','<>','MERCHANT')->orderBy('sequence')->get();
			foreach ($partner_types as $pt) {
		    	$cmd = "SELECT pc.company_name as name,pc.partner_id,'{$pt->name}'as category from partner_companies pc
		    			INNER JOIN partners p on p.id = pc.partner_id
		    			INNER JOIN partner_types pt on p.partner_type_id = pt.id
						where  p.status= 'A' and pc.company_name like '%{$term}%' and pt.name = '{$pt->name}' {$filter} Limit 10";

				$records = DB::select(DB::raw($cmd));
				foreach ($records  as $rec)
				{
					$rec->label = $rec->name;
					if($pt->name == 'LEAD'){
						$rec->url = env('APP_URL')."/leads/details/profile/{$rec->partner_id}";
					}	
					elseif($pt->name == 'PROSPECT'){
						$rec->url = env('APP_URL')."/prospects/details/profile/{$rec->partner_id}";
					}
					else{
						$rec->url = env('APP_URL')."/partners/details/profile/{$rec->partner_id}/profileCompanyInfo";
					}
					if (isset($access[strtolower($pt->name)]) && (strpos($access[strtolower($pt->name)], 'view') !== false)){
						$results[] = $rec;
					}		
				}
			}
		}

		if($searchType == "merchant"){
			$partner_types = PartnerType::where('status','A')->where('name','MERCHANT')->orderBy('sequence')->get();
			foreach ($partner_types as $pt) {
		    	$cmd = "SELECT pc.company_name as name,pc.partner_id,'{$pt->name}'as category from partner_companies pc
		    			INNER JOIN partners p on p.id = pc.partner_id
		    			INNER JOIN partner_types pt on p.partner_type_id = pt.id
						where  p.status= 'A' and pc.company_name like '%{$term}%' and pt.name = '{$pt->name}' {$filter} Limit 10";

				$records = DB::select(DB::raw($cmd));
				foreach ($records  as $rec)
				{
					$rec->label = $rec->name;
					$rec->url = env('APP_URL')."/merchants/details/{$rec->partner_id}/profile";
					if (isset($access[strtolower($pt->name)]) && (strpos($access[strtolower($pt->name)], 'view') !== false)){
						$results[] = $rec;
					}		
				}
			}
		}


		if($searchType == "mid"){
			$partner_types = PartnerType::where('status','A')->where('name','MERCHANT')->orderBy('sequence')->get();
			foreach ($partner_types as $pt) {
		    	$cmd = "SELECT p.merchant_mid as name,pc.partner_id,'MID'as category from partner_companies pc
		    			INNER JOIN partners p on p.id = pc.partner_id
		    			INNER JOIN partner_types pt on p.partner_type_id = pt.id
						where  p.status= 'A' and p.merchant_mid like '%{$term}%' and pt.name = '{$pt->name}' {$filter} Limit 10";

				$records = DB::select(DB::raw($cmd));
				foreach ($records  as $rec)
				{
					$rec->label = $rec->name;
					$rec->url = env('APP_URL')."/merchants/details/{$rec->partner_id}/profile";
					if (isset($access[strtolower($pt->name)]) && (strpos($access[strtolower($pt->name)], 'view') !== false)){
						$results[] = $rec;
					}		
				}
			}
		}


		if($searchType == "domain"){
			if (isset($access['merchant']) && (strpos($access['merchant'], 'view') !== false)){
		    	$cmd = "SELECT merchant_url as name,id,'MERCHANT DOMAIN'as category from partners p
						where  p.status = 'A' and p.merchant_url like '%{$term}%' {$filter} Limit 10";
				$records = DB::select(DB::raw($cmd));
				foreach ($records  as $rec)
				{
					$rec->label = $rec->name;
					$rec->url = env('APP_URL')."/merchants/details/{$rec->id}/profile";
					$results[] = $rec;
				}
			}
		}

		if($searchType == "invoice"){
			if (isset($access['merchant']) && (strpos($access['merchant'], 'view invoice') !== false)){
				$find = trim(str_replace("INVOICENO."," ",strtoupper($term)));
				$find = trim(str_replace("INVOICE"," ",strtoupper($find)));
		    	$cmd = "SELECT ih.id,ih.partner_id,'INVOICE'as category FROM invoice_headers ih 
		    			INNER JOIN partners p on p.id = ih.partner_id
		    			where ih.id like  '%{$find}%' {$filter} Limit 10";
				$records = DB::select(DB::raw($cmd));
				foreach ($records  as $rec)
				{
					$rec->label = 'InvoiceNo. '.$rec->id;
					$rec->url = env('APP_URL')."/merchants/invoice/view/{$rec->id}";
					$results[] = $rec;
				}
			}
		}

		if($searchType == "order"){
			if (isset($access['merchant']) && (strpos($access['merchant'], 'create order') !== false)){
				$find = trim(str_replace("ORDERNO."," ",strtoupper($term)));
				$find = trim(str_replace("ORDER"," ",strtoupper($find)));
		    	$cmd = "SELECT ih.id,ih.partner_id,'ORDER'as category FROM product_orders ih 
		    			INNER JOIN partners p on p.id = ih.partner_id
		    			where ih.id like  '%{$find}%' {$filter} Limit 10";
				$records = DB::select(DB::raw($cmd));
				foreach ($records  as $rec)
				{
					$rec->label = 'OrderNo. '.$rec->id;
					$rec->url = env('APP_URL')."/merchants/{$rec->id}/order_preview";
					$results[] = $rec;
				}
			}
		}

		if($searchType == "billingid"){
			if (isset($access['merchant']) && (strpos($access['merchant'], 'create order') !== false)){
				$find = trim(str_replace("ORDERNO."," ",strtoupper($term)));
				$find = trim(str_replace("ORDER"," ",strtoupper($find)));
		    	$cmd = "SELECT ih.id,ih.billing_id,ih.partner_id,'BILLING ID'as category FROM product_orders ih 
		    			INNER JOIN partners p on p.id = ih.partner_id
		    			where ih.billing_id like  '%{$find}%' {$filter} Limit 10";
				$records = DB::select(DB::raw($cmd));
				foreach ($records  as $rec)
				{
					$rec->label = 'Billing ID. '.$rec->billing_id;
					$rec->url = env('APP_URL')."/merchants/details/{$rec->partner_id}/products";
					$results[] = $rec;
				}
			}
		}

		if($searchType == "task"){
			if (isset($access['merchant']) && (strpos($access['merchant'], 'create order') !== false)){

				$add = ($isadmin) ? "" : "and sd.assignee like '%".'"'.auth()->user()->id.'"'."%'";

		    	$cmd = "SELECT sd.name,sh.order_id,po.partner_id,'TASK'as category  from sub_task_headers sh
						inner join sub_task_details sd on sh.id = sd.sub_task_id
						inner join product_orders po on po.id = sh.order_id	
						INNER JOIN partners p on p.id = po.partner_id
						where sd.name like '%{$term}%' {$add} {$filter} Limit 10";
				$records = DB::select(DB::raw($cmd));
				foreach ($records  as $rec)
				{
					$rec->label = $rec->name . ' - OrderNo.'. $rec->order_id;
					$rec->url = env('APP_URL')."/merchants/workflow/{$rec->partner_id}/{$rec->order_id}";
					$results[] = $rec;
				}
			}
		}

		return $results;


	}

	public function editSettings()
	{
		$access = session('all_user_access');
		$dashboard = isset($access['dashboard']) ? $access['dashboard'] : "";
		return view('extras.settings',compact('dashboard'))->with([
			'user' => auth()->user()
		]);
	}

	public function updateSettings(Request $request)
	{
		$dashboard = "";
		$dashboard .= isset($request->leads_this_month) ? "leads_this_month," : "";
		$dashboard .= isset($request->merchant_by_agents) ? "merchant_by_agents," : "";
		$dashboard .= isset($request->owner_dashboard) ? "owner_dashboard," : "";
		$dashboard .= isset($request->sales_per_agent) ? "sales_per_agent," : "";
		$dashboard .= isset($request->task_completion_rate) ? "task_completion_rate," : "";
		$dashboard .= isset($request->task_list) ? "task_list," : "";
		$dashboard .= isset($request->top_5_products) ? "top_5_products," : "";
		$dashboard .= isset($request->yearly_revenue) ? "yearly_revenue," : "";
		$dashboard .= isset($request->transaction_activity) ? "transaction_activity," : "";
		$dashboard .= isset($request->recent_sales) ? "recent_sales," : "";
		$dashboard .= isset($request->active_vs_closed_merchants) ? "active_vs_closed_merchants," : "";
		$dashboard .= isset($request->merchants_enrollment) ? "merchants_enrollment," : "";
		$dashboard .= isset($request->sales_trends) ? "sales_trends," : "";
		$dashboard .= isset($request->sales_matrix) ? "sales_matrix," : "";
		$dashboard .= isset($request->sales_profit) ? "sales_profit," : "";

		$dashboard .= isset($request->incoming_leads_today) ? "incoming_leads_today," : "";
		$dashboard .= isset($request->total_leads) ? "total_leads," : "";
		$dashboard .= isset($request->leads_payment_processor) ? "leads_payment_processor," : "";
		$dashboard .= isset($request->converted_leads) ? "converted_leads," : "";
		$dashboard .= isset($request->converted_prospects) ? "converted_prospects," : "";
		$dashboard .= isset($request->appointments_per_day) ? "appointments_per_day," : "";

		$validator = Validator::make($request->all(), [
			'ticketing_email' => 'required',
			'workflow_email' => 'required'
		]);

		if ($validator->fails()) {
			return response()->json([
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray(),
            ], 400); 
		}

		$user = auth()->user();
		$user->ticketing_email = $request->ticketing_email == 1 ? true : false;
		$user->workflow_email = $request->workflow_email == 1 ? true : false;
		$user->dashboard_items = $dashboard;
		$user->save();

		return response()->json([
			'success' => true,
		], 200);
	}

	public function updateAsRead(Request $request) {
		foreach ($request->add_to_read as $key => $value) {
			$updateNotification = Notification::find($value);
			$updateNotification->update_by = auth()->user()->username;
			$updateNotification->status = 'R';

			if(!$updateNotification->save()){
				return response()->json(array(
					'success'       => false, 
					'msg'           => "Unable to update notification", 
				), 200);
			}
		}

		return response()->json(array(
			'success'       => true, 
			'msg'           => "Notification/s marked as read!", 
		), 200);
	}

	public function updateAsUnread(Request $request) {
		foreach ($request->add_to_unread as $key => $value) {
			$updateNotification = Notification::find($value);
			$updateNotification->update_by = auth()->user()->username;
			$updateNotification->status = 'N';

			if(!$updateNotification->save()){
				return response()->json(array(
					'success'       => false, 
					'msg'           => "Unable to update notification", 
				), 200);
			}
		}

		return response()->json(array(
			'success'       => true, 
			'msg'           => "Notification/s marked as unread!", 
		), 200);
	}


	public function createSuggestion(Request $request){
    	DB::transaction(function() use($request){
			$timestamp=date('Y-m-d H:i:s');
	        $suggestion = New Suggestion;
	        $suggestion->title = $request->suggestionTitle;
	        $suggestion->description = $request->suggestionDescription;
	        $suggestion->create_by = auth()->user()->username;
	        $suggestion->status = 'N';

	        if(!$suggestion->save()){
				return response()->json(array(
		            'success'       => false, 
		            'message'           => "Unable to submit suggestion", 
		        ), 200);
			} 

			$notificationData[] = [
				'partner_id' => auth()->user()->company_id,
				'source_id' => -1,
				'subject' => 'Suggestion - ' . $request->suggestionTitle,
				'message' => $request->suggestionDescription,
				'status' => 'N',
				'create_by' => auth()->user()->username,
				'update_by' => auth()->user()->username,
				'redirect_url' => "/admin/suggestions",
				'recipient' => 'admin',
				'created_at' => $timestamp,
				'updated_at' => $timestamp
			];
			Notification::insert($notificationData);
			

		});
		
		
		return response()->json(array(
			'success'       => true, 
			'message'           => "Suggestion Submitted! Thank you!", 
		), 200);
	}
	
	public function getCityAndState($zip) {
		$zipLen = strlen($zip);
		if ($zipLen == 5) {
			$cities = UsZipCode::select('city','state_id','is_primary_city')
				->where('zip_code', $zip)
				->distinct()
				->orderBy('is_primary_city', 'desc')
				->get();
		} elseif ($zipLen == 4) {
			$cities = PhZipCode::select('city','state_id','is_primary_city')
				->where('zip_code', $zip)
				->distinct()
				->orderBy('is_primary_city', 'desc')
				->get();
		} elseif ($zipLen == 6) {
			$cities = CnZipCode::select('city','state_id','is_primary_city')
				->where('zip_code', $zip)
				->distinct()
				->orderBy('is_primary_city', 'desc')
				->get();
		}

        if (count($cities) == 0) {
            return response()->json(array(
                "success" => false,
            ));
        }

        return response()->json(array(
            "success" => true,
            'cities' => $cities,
            'state' => $cities[0]->state->abbr,
        ));
    }

}
