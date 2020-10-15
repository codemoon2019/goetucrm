@php
    use Illuminate\Http\Request;
    use App\Models\TicketHeader;
    use App\Models\TicketStatus;
    use App\Models\Suggestion;

    $newTicketsCount = TicketHeader::whereStatus(TicketStatus::TICKET_STATUS_NEW)
        ->where(function($query) {
            $query->where('create_by', auth()->user()->username)
                ->orWhere('assignee', auth()->user()->id)
                ->orWhere('requester_id', auth()->user()->id)
                ->orWhereHas('ccs', function($query) {
                    $query->where('user_id', auth()->user()->id);
                });
        })
        ->count();

    $span = "";
    if ($newTicketsCount > 0)
    {

        $span = '<span class="pull-right-container">
                     <small class="label pull-right bg-red">'.$newTicketsCount.'</small>
                  </span>';
               
    }

    $span_suggestion="";
    $newSuggestionCount = Suggestion::where('status','N')->count();
    if ($newSuggestionCount > 0)
    {

        $span_suggestion = '<span class="pull-right-container">
                     <small class="label pull-right bg-red">'.$newSuggestionCount.'</small>
                  </span>';
               
    }

    $access = session('permissions');
    $has_access=false;
	$menus = array(           
            'partners' => array(
                'create' => 'Create New Partner',
                'management' => 'List of Partners',
                'agent-applicants' => 'Agent Applicants',
            ), 
                        
            'merchants' => array(
                'create' => 'Create New Merchant',
                '' => 'List of Merchants',
                'branch' => 'List of Branches',
            ),
                
            'leads' => array(
            	'createLeadProspect' => 'Create New Lead',
                '' => 'List of Leads',
            	'incoming' => 'Incoming Leads',
            ),
            
            'supplier-leads' => array(
            	'create' => 'Create New Supplier Lead',
                '' => 'List of Supplier Leads',
			),

            'prospects' => array(
                'createLeadProspect' => 'Create New Prospect',
                '' => 'List of Prospects',
                'incoming' => 'Incoming Prospects'
            ),

            // 'drafts' =>  'drafts',

			'vendors' => array(
            	'create' 	=> 'Create Vendors',
                '' 			=> 'List of Vendors',
            	'incoming' 	=> 'Incoming Leads',
			),
            
           'products' => array(
                '' => 'Products',
                'listTemplate' => 'Templates',
            ),

           //'inventory' => array(
           //     'purchase_order' => 'Purchase Order',
           //     'receiving_po' => 'Receiving Purchase Order',
           // ),

            'tickets' => array(
                'create' => 'Create New Ticket',
                '' => 'Tickets',
                'reply-template' => 'Reply Templates',
                'faq' => 'View Ticket FAQ'
            ),
            'billing' =>  'billing/report',
            'calendar' =>  'calendar',
            'training' => array(
                'training_list' => 'Modules',
                'setup'               => 'Setup',
               // 'acl'               => 'Access Control',
                ),
            
            'admin'   => array(
                'analytics' => 'Analytics',
                'banners'             =>  'Announcements',
                'suggestions'             =>  'Suggestions',
                'users'             =>  'System Users',
                'departments'               => 'Access Control List',
                'company_settings'      => 'Company Settings',
            //    'errorlogs'               => 'Error Logs',
            ),

            'developers' => [
                'api-keys' => 'API Keys',
                'api-documentations' => 'API Documentation',
            ],
        );
        
        $menu_label = array(
        	'sign_application'  => 'Sign Application',
            'partners'  		=> 'Partners',
            'merchants'  		=> 'Merchants',
            'leads'             => 'Leads',
            'supplier-leads'    => 'Supplier Leads',
            'prospects'  		=> 'Prospects',
            // 'drafts'            => 'Draft Applicants',
            'vendors'  			=> 'Vendors',
            'products' 			=> 'Products',
            'inventory' 		=> 'Inventory',
            'billing' 			=> 'Reports',
            'tickets'  			=> 'Tickets',          
            'training'  		=> 'Training',     
            'setting'     		=> 'Setting',
            'reports'       	=> 'Reports',
            'calendar'       	=> 'My Calendar',
            'admin'         	=> 'Admin',
            'developers'        => 'Developers'
        );

        $url = \Request::url();
        session(['current_page' => '']);
        session(['main_menu' => 'Home']);

        if(strpos($url,'processFlow') !== false ){
            session(['current_page' => '']);
            session(['main_menu' => 'Process Flow']);
        }
        
        if(strpos($url,'products') !== false ){
            session(['current_page' => 'Products']);
            session(['main_menu' => 'Products']);
            if(strpos($url,'emplate') !== false ){
                session(['current_page' => 'Templates']);
            }
        }


        if(strpos($url,'partners') !== false ){
            session(['current_page' => 'List of Partners']);
            session(['main_menu' => 'Partners']);

            if(strpos($url,'partners/create') !== false ){
                session(['current_page' => 'Create New Partner']);
            }

            if(strpos($url,'partners/agent-applicants') !== false ){
                session(['current_page' => 'Agent Applicants']);
            }

        }

        if(strpos($url,'/merchants') !== false ){
            session(['current_page' => 'List of Merchants']);
            session(['main_menu' => 'Merchants']);

            if(strpos($url,'merchants/create') !== false ){
                session(['current_page' => 'Create New Merchant']);
            }
            if(strpos($url,'branch') !== false ){
                session(['current_page' => 'List of Branches']);
            }

        }

        if(strpos($url,'leads') !== false ){
            session(['current_page' => 'List of Leads']);
            session(['main_menu' => 'Leads']);
            if(strpos($url,'leads/create') !== false ){
                session(['current_page' => 'Create New Lead']);
            }
            if(strpos($url,'incoming') !== false ){
                session(['current_page' => 'Incoming Leads']);
            }
        }

        if(strpos($url,'prospects') !== false ){
            session(['current_page' => 'List of Prospects']);
            session(['main_menu' => 'Prospects']);
            if(strpos($url,'prospects/createLeadProspect') !== false ){
                session(['current_page' => 'Create New Prospect']);
            }
            if(strpos($url,'prospects/incoming') !== false ){
                session(['current_page' => 'Incoming Prospects']);
            }
        }

        if (strpos($url, 'supplier-leads') !== false) {
            session(['current_page' => 'List of Supplier Leads']);
            session(['main_menu' => 'Supplier Leads']);

            if (strpos($url,'supplier-leads/create') !== false) {
                session(['current_page' => 'Create New Supplier Lead']);
            }
        }

        if(strpos($url,'tickets') !== false ){
            session(['current_page' => 'Tickets']);
            session(['main_menu' => 'Tickets']);
            if(strpos($url,'create') !== false ){
                session(['current_page' => 'Create New Ticket']);
            }
            if(strpos($url,'reply-template') !== false ){
                session(['current_page' => 'Reply Templates']);
            }
            if(strpos($url,'faq') !== false ){
                session(['current_page' => 'View Ticket FAQ']);
            }
        }

        if(strpos($url,'report') !== false ){
            session(['current_page' => '']);
            session(['main_menu' => 'Reports']);
        }

        if(strpos($url,'calendar') !== false ){
            session(['current_page' => '']);
            session(['main_menu' => 'My Calendar']);
        }

        if(strpos($url,'training') !== false ){
            session(['current_page' => 'Modules']);
            session(['main_menu' => 'Training']);
            if(strpos($url,'/setup') !== false ){
                session(['current_page' => 'Setup']);
            }
        }

        if(strpos($url,'admin/') !== false ){
            session(['current_page' => 'System Users']);
            session(['main_menu' => 'Admin']);
            if(strpos($url,'/departments') !== false ){
                session(['current_page' => 'Access Control List']);
            }
            if(strpos($url,'/company_settings') !== false ){
                session(['current_page' => 'Company Settings']);
            }

        }

        if (strpos($url, 'developers/') !== false) {
            session(['current_page' => 'API Keys']);
            session(['main_menu' => 'Developers']);
        }

        if(strpos($url,'banners') !== false ){
            session(['current_page' => 'A']);
            session(['main_menu' => 'Announcements']);
        }

        if(strpos($url,'banners') !== false ){
            session(['current_page' => 'Announcements']);
            session(['main_menu' => 'Admin']);
        }

        if(strpos($url,'suggestions') !== false ){
            session(['current_page' => 'Suggestions']);
            session(['main_menu' => 'Admin']);
        }

        if(strpos($url,'analytics') !== false ){
            session(['current_page' => 'Analytics']);
            session(['main_menu' => 'Admin']);
        }

        /* if(strpos($url,'drafts') !== false ){
            session(['current_page' => '']);
            session(['main_menu' => 'Draft Applicants']);
        } */

        /** 
         ICONS
         **/
        $font_awesome = array(
            'partners'         => 'group',
            'merchants'         => 'users',
            'leads'             => 'users',
            'supplier-leads'    => 'users',
            'prospects'         => 'users',
            'drafts'             => 'users',
            'products'          => 'tags',
            'billing'           => 'file-text',
            'tickets'           => 'ticket',          
            'training'          => 'list',     
            'setting'           => 'Setting',
            'calendar'          => 'calendar',
            'admin'             => 'cogs',
            'home'              => 'dashboard',
            'logout'            => 'power-off',
            'process'           => 'refresh',
            'developers'        => 'code'
        );

@endphp

<ul class="sidebar-menu" data-widget="tree">
    <!-- Header Nav title  na color light black
    <li class="header">HEADER</li> -->
    <!-- You can view the routes/web.php for declaration of the Routes per page -->
    <li class="{{ session('main_menu') == "Home" ? "active" : "" }}"><a href="{{ url('/') }}"><i class="fa fa-{{$font_awesome['home']}}"></i> <span>Home</span></a></li>

    <li class="{{ session('main_menu') == "Process Flow" ? "active" : "" }}"><a href="{{ url('/processFlow') }}"><i class="fa fa-{{$font_awesome['process']}}"></i> <span>Process Flow</span></a></li>
    @foreach($menus as $key => $menu)
        @php 
            $has_access=false;
        @endphp 

        @if(is_array($menu))
            @foreach($menu as $menu_key => $sub_menu)
                @if(array_key_exists($key.'/'.$menu_key,$access))
                    @php 
                        $has_access=true;

                    @endphp
                @endif    
            @endforeach
            @if($has_access)
    	    <li class="treeview">
    	       <a href="#">
               <span>
                   <i class="fa fa-{{$font_awesome[$key]}}"></i>
                   {{$menu_label[$key]}} 
        	       <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                    @if($menu_label[$key]=="Tickets") {!! $span !!} @endif
                    @if($menu_label[$key]=="Admin") {!! $span_suggestion !!} @endif 
    	       </a>
    	        <ul class="treeview-menu" style="{{ session('main_menu') == $menu_label[$key] ? "display: block;" : "" }}">
                    @foreach($menu as $menu_key => $sub_menu)
                        @if($sub_menu=='System Users') 
                            @if (App\Models\Access::hasPageAccess('users', 'view', true))
                                <li class="{{ session('current_page') == $sub_menu ? "active" : "" }}"><a href="{{ url("$key/$menu_key/") }}">{{$sub_menu}}</a></li>
                            @endif
                        @elseif ($sub_menu=='API Documentation')
                            <li class="{{ session('current_page') == $sub_menu ? "active" : "" }}"><a href="{{ url("$key/$menu_key/") }}" target="_blank">{{$sub_menu}}</a></li>
                        @elseif ($sub_menu=="Suggestions") 
                            <li class="{{ session('current_page') == $sub_menu ? "active" : "" }}"><a href="{{ url("$key/$menu_key/") }}">{{$sub_menu}}  {!! $span_suggestion !!}</a> </li>
                        @else
                            @if(array_key_exists($key.'/'.$menu_key,$access))
                                <li class="{{ session('current_page') == $sub_menu ? "active" : "" }}"><a href="{{ url("$key/$menu_key/") }}">{{$sub_menu}}</a></li>
                            @endif
                        @endif
    	           	@endforeach
    	        </ul>
    	    </li>
            @endif
	    @else
            @if(array_key_exists($menu,$access))
            
	    	<li class="{{ session('main_menu') == $menu_label[$key] ? "active" : "" }}">
            <a href="{{ url("$menu") }}"><span><i class="fa fa-{{$font_awesome[$key]}}"></i> {{$menu_label[$key]}}</span></a>
            </li>
	    	@endif
	    @endif
    @endforeach
    <li><a href="{{ url('/logout') }}" onclick="chatOffline();"><i class="fa fa-{{$font_awesome['logout']}}"></i><span>Sign Out</span></a></li>
               
</ul>