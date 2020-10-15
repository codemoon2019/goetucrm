
<div class="col-lg-6 col-xs-6">
    <!-- LINE CHART -->
    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title title-header">Top 5 Products in Sales Revenue</h3>
        </div>
        <div class="box-body">
            <div class="chart">
                <table class="table datatable responsive table-condense table-striped table-bordered">
                    <thead>
                    <tr>
                        <th>Product </th>
                        <th> {{\Carbon\Carbon::now()->format('Y') - 1}} Sales</th>
                        <th>Sales YTD</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($salesYTD as $sales)
                    <tr>
                        <td>{!! $sales->name !!}</td>
                        <td>$ {{number_format($sales->prev_total,2,".",",")}}</td>
                        <td>$ {{number_format($sales->total,2,".",",")}}</td>
                    </tr>     
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <!-- /.box-body -->
    </div>
</div>