    <script>
        $(function(){
            var areaChart = new CanvasJS.Chart("areaChart", {
                animationEnabled: true,
                axisX: {
                    valueFormatString: "MMM",
                    minimum: new Date(2017, 0, 1),
                    maximum: new Date(2017, 11, 31),
                    interval: 1,
                    intervalType: "month",
                },
                axisY: {
                    prefix: "$"                
                },
                legend: {
                    verticalAlign: "top",
                    horizontalAlign: "right",
                    dockInsidePlotArea: true
                },
                toolTip: {
                    shared: true
                },
                data: [{
                    name: "{{\Carbon\Carbon::now()->format('Y')}}",
                    showInLegend: true,
                    legendMarkerType: "square",
                    type: "area",
                    color: "rgba(40,175,101,0.6)",
                    markerSize: 0,
                    dataPoints: [
                        {!! $revenue['yearlyRevenue'] !!}
                    ]
                },
                    {
                        name: "{{\Carbon\Carbon::now()->format('Y') - 1}}",
                        showInLegend: true,
                        legendMarkerType: "square",
                        type: "area",
                        color: "rgba(0,75,141,0.7)",
                        markerSize: 0,
                        dataPoints: [
                        {!! $revenue['yearlyRevenuePrev'] !!}
                        ]
                    }]
            });
            areaChart.render();
        });
    </script>