
$(document).ready(function () {
    
    $('input[name="daterange"]').daterangepicker();
    $(function() {

        var start = moment().subtract(29, 'days');
        var end = moment();

        function cb(start, end) {
            $('input[name="daterange"] span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        }

        $('input[name="daterange"]').daterangepicker({
            startDate: start,
            endDate: end,
            ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        }, cb);
        cb(start, end);
    });

    $('#merchantList').dataTable({
        "bRetrieve": true,
        "iDisplayLength": 5,
        "fnDrawCallback": function (oSettings) {
            $(".view-more").click(function (e) {
                id = this.id
                e.preventDefault();
                $("box_" + id).focus();
                // if (this.innerHTML == 'View More') {
                //     $(this).text('View Less');
                //     $("#box_" + id).attr('class', 'box box-default');
                //     var url = '/company/sales/' + id;
                //     $.getJSON({
                //         url: url,
                //     }).done(function (data) {
                //         $("#overlay_" + id).css('display', 'none');
                //         try {
                //             JSON.parse(data);
                //             var lineChart = new CanvasJS.Chart("lineChartCompanySales-" + id, {
                //                 theme: "light2",
                //                 animationEnabled: true,
                //                 axisY: {
                //                     includeZero: false,
                //                     prefix: "$"
                //                 },
                //                 axisX: {
                //                     title: "Past 60 Days",
                //                 },
                //                 toolTip: {
                //                     shared: "true"
                //                 },
                //                 legend: {
                //                     cursor: "pointer",

                //                 },
                //                 data: [JSON.parse(data)]
                //             });

                //             lineChart.render();

                //         } catch (e) {
                //             var lineChart = new CanvasJS.Chart("lineChartCompanySales-" + id, {
                //                 theme: "light2",
                //                 animationEnabled: true,
                //                 axisY: {
                //                     includeZero: false,
                //                     prefix: "$"
                //                 },
                //                 axisX: {
                //                     title: "Past 60 Days",
                //                 },
                //                 toolTip: {
                //                     shared: "true"
                //                 },
                //                 legend: {
                //                     cursor: "pointer",

                //                 },
                //                 data: [
                //                     {
                //                         type: "column",

                //                         dataPoints: null
                //                     },
                //                 ]
                //             });

                //             showDefaultText(lineChart, "No Data available")
                //             lineChart.render();
                //             return false;
                //         }
                //         return true;
                //     });

                // }
                // else {
                //     $("#box_" + id).attr('class', 'box box-default collapsed-box');
                //     $(this).text('View More');
                // }
            });
        }
    });
    $('.productList').dataTable({
        "bRetrieve": true,
        "dom": '<f<t>p>',
        "order": [[1, "desc"]],
        "iDisplayLength": 5
    });
    $('#taskList').dataTable({"bRetrieve": true});
    $('#productSales').dataTable({"bRetrieve": true});

    function showDefaultText(chart, text) {

        var isEmpty = !(chart.options.data[0].dataPoints && chart.options.data[0].dataPoints.length > 0);

        if (!chart.options.subtitles)
            (chart.options.subtitles = []);

        if (isEmpty)
            chart.options.subtitles.push({
                text: text,
                verticalAlign: 'center',
            });
        else
            (chart.options.subtitles = []);
    }

    function debounce(obj) {
        var fewSeconds = 0.50;
        $(obj).css('pointer-events', 'none');
        setTimeout(function () {
            $(obj).css('pointer-events', 'auto');
        }, fewSeconds * 1000);
    }

    // $(window).on('load', function () {
    //     setTimeout(removeLoader, 500); //wait for page load PLUS two seconds.
    // });

    function removeLoader() {
        $(".overlay").fadeOut(500, function () {
            // fadeOut complete. Remove the loading div
            $(".overlay").remove(); //makes page more lightweight
        });
    }
})
