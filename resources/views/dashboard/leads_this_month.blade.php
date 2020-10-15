<div class="col-lg-6 col-xs-6">
    <div class="info-box">
        <span class="info-box-icon bg-aqua"><i class="fa fa-users"></i></span>
        <div class="info-box-content">
            <span class="info-box-text">Leads This Month</span>
            <span class="info-box-number">{{$leadInfo['currLead']}}</span>
            <span class="info-box-number"><small>Leads Last Month</small> {{$leadInfo['prevLead']}} <small>|</small> <small>Avg leads/month</small> {{$leadInfo['avgLead']}} <small>|</small> <small>Leads Today</small> {{$leadInfo['todayLead']}} </span>
        </div>
    </div>
</div>