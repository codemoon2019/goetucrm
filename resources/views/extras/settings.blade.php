@extends('layouts.app')

@section('content')
  <style>
    .overlay {
      position: fixed;
      width: 100%;
      height: 100%;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: rgba(0,0,0,0.1);
      z-index: 2000;

      display: none;
    }

    .overlay > div {
      margin: 0;
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
    }
  </style>
  <div class="content-wrapper">
    <section class="content-header">
      <h1><strong>Settings</strong></h1>
      <div class="dotted-hr"></div>
    </section>

    <section class="content container-fluid">
      <div class="col-sm-12 col-lg-6">
        <form>
          @csrf

          <div class="form-group">
            <p>Receive emails involving tickets</p>
  
            <label>
              <input type="radio" name="ticketing_email" value="1" {{ $user->ticketing_email === 1 ? 'checked' : '' }}>
              <span>On</span>
            </label>
  
            &nbsp;
  
            <label>
              <input type="radio" name="ticketing_email" value="0" {{ $user->ticketing_email === 0 ? 'checked' : '' }}>
              <span>Off</span>
            </label>
          </div>
  
          <div class="form-group">
            <p>Receive emails involving workflow</p>
  
            <label>
              <input type="radio" name="workflow_email" value="1" {{ $user->workflow_email === 1 ? 'checked' : '' }}>
              <span>On</span>
            </label>
  
            &nbsp;
  
            <label>
              <input type="radio" name="workflow_email" value="0" {{ $user->workflow_email === 0 ? 'checked' : '' }}>
              <span>Off</span>
            </label>
          </div>

          <h5><strong>Dashboard</strong></h5>

          <table>
            @if(strpos($dashboard, 'leads this month') !== false)
             <tr>
                <td style="vertical-align:bottom">Leads this Month</td>
                <td>            
                    <label class="switch switch-auto">
                      <input type="checkbox" id="leads_this_month" name="leads_this_month" 
                      @if(strpos($user->dashboard_items, 'leads_this_month') !== false || !isset($user->dashboard_items)) checked @endif >
                      <div class="slider round">
                          <span class="on">Show</span><span class="off">Hide</span>
                      </div>
                    </label>
                </td>
             </tr> 
            @endif

            @if(strpos($dashboard, 'merchant by agents') !== false)
             <tr>
                <td  style="vertical-align:bottom">Merchant by Agent</td>
                <td>            
                    <label class="switch switch-auto">
                      <input type="checkbox" id="merchant_by_agents" name="merchant_by_agents" 
                       @if(strpos($user->dashboard_items, 'merchant_by_agents') !== false || !isset($user->dashboard_items)) checked @endif >
                      <div class="slider round">
                          <span class="on">Show</span><span class="off">Hide</span>
                      </div>
                    </label>
                </td>
             </tr> 
            @endif

            @if(strpos($dashboard, 'owner dashboard') !== false)
             <tr>
                <td  style="vertical-align:bottom">Owner Dashboard</td>
                <td>            
                    <label class="switch switch-auto">
                      <input type="checkbox" id="owner_dashboard" name="owner_dashboard" 
                       @if(strpos($user->dashboard_items, 'owner_dashboard') !== false || !isset($user->dashboard_items)) checked @endif>
                      <div class="slider round">
                          <span class="on">Show</span><span class="off">Hide</span>
                      </div>
                    </label>
                </td>
             </tr> 
            @endif

            @if(strpos($dashboard, 'sales per agent') !== false)
             <tr>
                <td  style="vertical-align:bottom">Sales Per Agent</td>
                <td>            
                    <label class="switch switch-auto">
                      <input type="checkbox" id="sales_per_agent" name="sales_per_agent" 
                       @if(strpos($user->dashboard_items, 'sales_per_agent') !== false || !isset($user->dashboard_items)) checked @endif>
                      <div class="slider round">
                          <span class="on">Show</span><span class="off">Hide</span>
                      </div>
                    </label>
                </td>
             </tr> 
            @endif

            @if(strpos($dashboard, 'task completion rate') !== false)
             <tr>
                <td  style="vertical-align:bottom">Task Completion Rate</td>
                <td>            
                    <label class="switch switch-auto">
                      <input type="checkbox" id="task_completion_rate" name="task_completion_rate" 
                       @if(strpos($user->dashboard_items, 'task_completion_rate') !== false || !isset($user->dashboard_items)) checked @endif>
                      <div class="slider round">
                          <span class="on">Show</span><span class="off">Hide</span>
                      </div>
                    </label>
                </td>
             </tr> 
            @endif

            @if(strpos($dashboard, 'task list') !== false)
             <tr>
                <td  style="vertical-align:bottom">Task List</td>
                <td>            
                    <label class="switch switch-auto">
                      <input type="checkbox" id="task_list" name="task_list" 
                       @if(strpos($user->dashboard_items, 'task_list') !== false || !isset($user->dashboard_items)) checked @endif>
                      <div class="slider round">
                          <span class="on">Show</span><span class="off">Hide</span>
                      </div>
                    </label>
                </td>
             </tr> 
            @endif

            @if(strpos($dashboard, 'top 5 products') !== false)
             <tr>
                <td  style="vertical-align:bottom">Top 5 Products</td>
                <td>            
                    <label class="switch switch-auto">
                      <input type="checkbox" id="top_5_products" name="top_5_products" 
                       @if(strpos($user->dashboard_items, 'top_5_products') !== false || !isset($user->dashboard_items)) checked @endif>
                      <div class="slider round">
                          <span class="on">Show</span><span class="off">Hide</span>
                      </div>
                    </label>
                </td>
             </tr> 
            @endif

            @if(strpos($dashboard, 'yearly revenue') !== false)
             <tr>
                <td  style="vertical-align:bottom">Yearly Revenue</td>
                <td>            
                    <label class="switch switch-auto">
                      <input type="checkbox" id="yearly_revenue" name="yearly_revenue" 
                       @if(strpos($user->dashboard_items, 'yearly_revenue') !== false || !isset($user->dashboard_items)) checked @endif>
                      <div class="slider round">
                          <span class="on">Show</span><span class="off">Hide</span>
                      </div>
                    </label>
                </td>
             </tr> 
            @endif

            @if(strpos($dashboard, 'transaction activity') !== false)
             <tr>
                <td  style="vertical-align:bottom">Transaction Activity</td>
                <td>            
                    <label class="switch switch-auto">
                      <input type="checkbox" id="transaction_activity" name="transaction_activity" 
                       @if(strpos($user->dashboard_items, 'transaction_activity') !== false || !isset($user->dashboard_items)) checked @endif>
                      <div class="slider round">
                          <span class="on">Show</span><span class="off">Hide</span>
                      </div>
                    </label>
                </td>
             </tr> 
            @endif

            @if(strpos($dashboard, 'recent sales') !== false)
             <tr>
                <td  style="vertical-align:bottom">Recent Sales</td>
                <td>            
                    <label class="switch switch-auto">
                      <input type="checkbox" id="recent_sales" name="recent_sales" 
                       @if(strpos($user->dashboard_items, 'recent_sales') !== false || !isset($user->dashboard_items)) checked @endif>
                      <div class="slider round">
                          <span class="on">Show</span><span class="off">Hide</span>
                      </div>
                    </label>
                </td>
             </tr> 
            @endif

            @if(strpos($dashboard, 'active vs closed merchants') !== false)
             <tr>
                <td  style="vertical-align:bottom">Active vs Closed Merchants</td>
                <td>            
                    <label class="switch switch-auto">
                      <input type="checkbox" id="active_vs_closed_merchants" name="active_vs_closed_merchants" 
                       @if(strpos($user->dashboard_items, 'active_vs_closed_merchants') !== false || !isset($user->dashboard_items)) checked @endif>
                      <div class="slider round">
                          <span class="on">Show</span><span class="off">Hide</span>
                      </div>
                    </label>
                </td>
             </tr> 
            @endif

            @if(strpos($dashboard, 'merchants enrollment') !== false)
             <tr>
                <td  style="vertical-align:bottom">Merchants Enrollment</td>
                <td>            
                    <label class="switch switch-auto">
                      <input type="checkbox" id="merchants_enrollment" name="merchants_enrollment" 
                       @if(strpos($user->dashboard_items, 'merchants_enrollment') !== false || !isset($user->dashboard_items)) checked @endif>
                      <div class="slider round">
                          <span class="on">Show</span><span class="off">Hide</span>
                      </div>
                    </label>
                </td>
             </tr> 
            @endif

            @if(strpos($dashboard, 'sales trends') !== false)
             <tr>
                <td  style="vertical-align:bottom">Sales Trend</td>
                <td>            
                    <label class="switch switch-auto">
                      <input type="checkbox" id="sales_trends" name="sales_trends" 
                       @if(strpos($user->dashboard_items, 'sales_trends') !== false || !isset($user->dashboard_items)) checked @endif>
                      <div class="slider round">
                          <span class="on">Show</span><span class="off">Hide</span>
                      </div>
                    </label>
                </td>
             </tr> 
            @endif

            @if(strpos($dashboard, 'sales matrix') !== false)
             <tr>
                <td  style="vertical-align:bottom">Sales Matrix</td>
                <td>            
                    <label class="switch switch-auto">
                      <input type="checkbox" id="sales_matrix" name="sales_matrix" 
                       @if(strpos($user->dashboard_items, 'sales_matrix') !== false || !isset($user->dashboard_items)) checked @endif>
                      <div class="slider round">
                          <span class="on">Show</span><span class="off">Hide</span>
                      </div>
                    </label>
                </td>
             </tr> 
            @endif

            @if(strpos($dashboard, 'sales profit') !== false)
             <tr>
                <td  style="vertical-align:bottom">Sales Profit</td>
                <td>            
                    <label class="switch switch-auto">
                      <input type="checkbox" id="sales_profit" name="sales_profit" 
                       @if(strpos($user->dashboard_items, 'sales_profit') !== false || !isset($user->dashboard_items)) checked @endif>
                      <div class="slider round">
                          <span class="on">Show</span><span class="off">Hide</span>
                      </div>
                    </label>
                </td>
             </tr> 
            @endif


            @if(strpos($dashboard, 'incoming leads today') !== false)
             <tr>
                <td  style="vertical-align:bottom">Incoming Leads Today</td>
                <td>            
                    <label class="switch switch-auto">
                      <input type="checkbox" id="incoming_leads_today" name="incoming_leads_today" 
                       @if(strpos($user->dashboard_items, 'incoming_leads_today') !== false || !isset($user->dashboard_items)) checked @endif>
                      <div class="slider round">
                          <span class="on">Show</span><span class="off">Hide</span>
                      </div>
                    </label>
                </td>
             </tr> 
            @endif

            @if(strpos($dashboard, 'total leads') !== false)
             <tr>
                <td  style="vertical-align:bottom">Total Leads</td>
                <td>            
                    <label class="switch switch-auto">
                      <input type="checkbox" id="total_leads" name="total_leads" 
                       @if(strpos($user->dashboard_items, 'total_leads') !== false || !isset($user->dashboard_items)) checked @endif>
                      <div class="slider round">
                          <span class="on">Show</span><span class="off">Hide</span>
                      </div>
                    </label>
                </td>
             </tr> 
            @endif

            @if(strpos($dashboard, 'leads payment processor') !== false)
             <tr>
                <td  style="vertical-align:bottom">Leads Payment Processor</td>
                <td>            
                    <label class="switch switch-auto">
                      <input type="checkbox" id="leads_payment_processor" name="leads_payment_processor" 
                       @if(strpos($user->dashboard_items, 'leads_payment_processor') !== false || !isset($user->dashboard_items)) checked @endif>
                      <div class="slider round">
                          <span class="on">Show</span><span class="off">Hide</span>
                      </div>
                    </label>
                </td>
             </tr> 
            @endif

            @if(strpos($dashboard, 'converted leads') !== false)
             <tr>
                <td  style="vertical-align:bottom">Converted Leads</td>
                <td>            
                    <label class="switch switch-auto">
                      <input type="checkbox" id="converted_leads" name="converted_leads" 
                       @if(strpos($user->dashboard_items, 'converted_leads') !== false || !isset($user->dashboard_items)) checked @endif>
                      <div class="slider round">
                          <span class="on">Show</span><span class="off">Hide</span>
                      </div>
                    </label>
                </td>
             </tr> 
            @endif

            @if(strpos($dashboard, 'converted prospects') !== false)
             <tr>
                <td  style="vertical-align:bottom">Converted Prospects</td>
                <td>            
                    <label class="switch switch-auto">
                      <input type="checkbox" id="converted_prospects" name="converted_prospects" 
                       @if(strpos($user->dashboard_items, 'converted_prospects') !== false || !isset($user->dashboard_items)) checked @endif>
                      <div class="slider round">
                          <span class="on">Show</span><span class="off">Hide</span>
                      </div>
                    </label>
                </td>
             </tr> 
            @endif

            @if(strpos($dashboard, 'appointments per day') !== false)
             <tr>
                <td  style="vertical-align:bottom">Appointments per Day</td>
                <td>            
                    <label class="switch switch-auto">
                      <input type="checkbox" id="appointments_per_day" name="appointments_per_day" 
                       @if(strpos($user->dashboard_items, 'appointments_per_day') !== false || !isset($user->dashboard_items)) checked @endif>
                      <div class="slider round">
                          <span class="on">Show</span><span class="off">Hide</span>
                      </div>
                    </label>
                </td>
             </tr> 
            @endif



          </table>


          <br />
          <br />

          <button type="submit" class="btn btn-primary btn-submit">Update Settings</button>
        </form>
      </div>
    </section>
  </div>

  <div class="overlay">
    <div id="text">
      <img width="75px"   
        height="75px"
        src="https://ubisafe.org/images/transparent-gif-loading-5.gif"/>
    </div>
  </div>
@endsection

@section('script')
  <script src=@cdn('/js/extras/settings.js')></script>
@endsection