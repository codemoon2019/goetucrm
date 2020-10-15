    
 <script>
    $(function(){

        var taskChart = new CanvasJS.Chart("taskChart", {
            animationEnabled: true,
            data: [{
                type: "pie",
                startAngle: 240,
                yValueFormatString: "##0.00\"%\"",
                indexLabel: "{label} {y}",
                dataPoints: [
                    {y: {!! $taskSummary['completed'] !!}, label: "Completed"},
                    {y: {!! $taskSummary['delayed'] !!}, label: "Delayed"},
                    {y: {!! $taskSummary['ontrack'] !!}, label: "On Track"},
                ]
            }]
        });
        taskChart.render();
    });
</script>