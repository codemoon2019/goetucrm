<div class="col-lg-12 col-xs-12">
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title title-header">Recent Sales</h3>
        </div>

        <div class="box-body">
            @foreach($recentInvoice as $inv)
           <div class="card">
               <h4 class="recent-amount">$ {{number_format((float)$inv->totalSale, 2, '.', ',')}}</h4>
               <p class="recent-date">{{$inv->salesDate}}</p>
           </div>
            @endforeach

        </div>
    </div>
</div>