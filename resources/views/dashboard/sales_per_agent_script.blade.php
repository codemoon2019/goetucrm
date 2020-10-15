    <script>
        $(function(){
            var lineChart = new CanvasJS.Chart("lineChart", {
                theme:"light2",
                animationEnabled: true,
                axisY :{
                    includeZero: false,
                    prefix: "$"
                },
                axisX :{
                    title: "Past 60 Days",
                },
                toolTip: {
                    shared: "true"
                },
                legend:{
                    cursor:"pointer",
                    itemclick : toggleDataSeries
                },
                data: [{!! $salesPerAgent !!}]
            });

            lineChart.render();

            function toggleDataSeries(e) {
                if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible ){
                    e.dataSeries.visible = false;
                } else {
                    e.dataSeries.visible = true;
                }
                lineChart.render();
            }
        });
    </script>