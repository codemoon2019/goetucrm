
import axios from "axios";
import swal from "sweetalert2";

$( document ).ready(function() {
    $("#txtMonthlyDate").datepicker({
      autoclose: true,
      format: "yyyy-mm",
      minViewMode: "months",
      startView: "months",  
    });

    $("#txtYearlyDate").datepicker({
      autoclose: true,
      format: "yyyy",
      minViewMode: "years",
      startView: "years",  
    });

    $('.datepick').datepicker({ 
      autoclose: true,
      format: 'yyyy-mm-dd',
      orientation: "bottom left" 
    });        


    $('#txtWeeklyDateNewPartner').datetimepicker({ 'format': 'YYYY-MM-DD' });
    $('#txtWeeklyDateNewPartner').on('dp.change', function (e) {
        value = $("#txtWeeklyDateNewPartner").val();
        firstDate = moment(value, "YYYY-MM-DD'").day(0).format("YYYY-MM-DD");
        lastDate =  moment(value, "YYYY-MM-DD'").day(6).format("YYYY-MM-DD");
        $("#txtWeeklyDateNewPartner").val(firstDate + "   to   " + lastDate);
        if($('#init').val() == 1){
            generateReport('/billing/report_new_partner/'+$('#partner').val()+'/'+$('#txtDateType').val()+'/{$from}/{$to}/false'); 
        }
        $('#init').val(1);
        
    });
    var value = $("#txtWeeklyDateNewPartner").val();
    var firstDate = moment(value, "YYYY-MM-DD'").day(0).format("YYYY-MM-DD");
    var lastDate =  moment(value, "YYYY-MM-DD'").day(6).format("YYYY-MM-DD");
    $("#txtWeeklyDateNewPartner").val(firstDate + "   to   " + lastDate);



    $('#txtWeeklyDateNewBusiness').datetimepicker({ 'format': 'YYYY-MM-DD' });
    $('#txtWeeklyDateNewBusiness').on('dp.change', function (e) {
        value = $("#txtWeeklyDateNewBusiness").val();
        firstDate = moment(value, "YYYY-MM-DD'").day(0).format("YYYY-MM-DD");
        lastDate =  moment(value, "YYYY-MM-DD'").day(6).format("YYYY-MM-DD");
        $("#txtWeeklyDateNewBusiness").val(firstDate + "   to   " + lastDate);
        if($('#init').val() == 1){
            generateReport('/billing/report_new_business/'+$('#txtDateType').val()+'/{$from}/{$to}/false'); 
        }
        $('#init').val(1);
        
    });
    var value = $("#txtWeeklyDateNewBusiness").val();
    var firstDate = moment(value, "YYYY-MM-DD'").day(0).format("YYYY-MM-DD");
    var lastDate =  moment(value, "YYYY-MM-DD'").day(6).format("YYYY-MM-DD");
    $("#txtWeeklyDateNewBusiness").val(firstDate + "   to   " + lastDate);


    $('#txtWeeklyDateNewProduct').datetimepicker({ 'format': 'YYYY-MM-DD' });
    $('#txtWeeklyDateNewProduct').on('dp.change', function (e) {
        value = $("#txtWeeklyDateNewProduct").val();
        firstDate = moment(value, "YYYY-MM-DD'").day(0).format("YYYY-MM-DD");
        lastDate =  moment(value, "YYYY-MM-DD'").day(6).format("YYYY-MM-DD");
        $("#txtWeeklyDateNewProduct").val(firstDate + "   to   " + lastDate);
        if($('#init').val() == 1){
            generateReport('/billing/report_product/'+$('#merchant').val()+'/'+$('#txtDateType').val()+'/{$from}/{$to}/false'); 
        }
        $('#init').val(1);
        
    });
    var value = $("#txtWeeklyDateNewProduct").val();
    var firstDate = moment(value, "YYYY-MM-DD'").day(0).format("YYYY-MM-DD");
    var lastDate =  moment(value, "YYYY-MM-DD'").day(6).format("YYYY-MM-DD");
    $("#txtWeeklyDateNewProduct").val(firstDate + "   to   " + lastDate);


    $('#txtWeeklyDateBranch').datetimepicker({ 'format': 'YYYY-MM-DD' });
    $('#txtWeeklyDateBranch').on('dp.change', function (e) {
        value = $("#txtWeeklyDateBranch").val();
        firstDate = moment(value, "YYYY-MM-DD'").day(0).format("YYYY-MM-DD");
        lastDate =  moment(value, "YYYY-MM-DD'").day(6).format("YYYY-MM-DD");
        $("#txtWeeklyDateBranch").val(firstDate + "   to   " + lastDate);
        if($('#init').val() == 1){
            generateReport('/billing/report_branches/'+$('#merchant').val()+'/'+$('#txtDateType').val()+'/{$from}/{$to}/false'); 
        }
        $('#init').val(1);
        
    });
    var value = $("#txtWeeklyDateBranch").val();
    var firstDate = moment(value, "YYYY-MM-DD'").day(0).format("YYYY-MM-DD");
    var lastDate =  moment(value, "YYYY-MM-DD'").day(6).format("YYYY-MM-DD");
    $("#txtWeeklyDateBranch").val(firstDate + "   to   " + lastDate);


    $('#txtDateType').change(function () {
         $('.dateDiv').hide();
         if($(this).val() == 'Daily'){
            $('.dailyDiv').show();
         }

         if($(this).val() == 'Weekly'){
            $('.weeklyDiv').show();
         }

         if($(this).val() == 'Monthly'){
            $('.monthlyDiv').show();
         }


         if($(this).val() == 'Yearly'){
            $('.yearlyDiv').show();
         }

         if($(this).val() == 'Custom'){
            $('.customDiv').show();
         }

    });
    $('#txtDateType').trigger('change');

}); 

function generateReport($url) {
    var $from;
    var $to;
    var $dt;

     if($('#txtDateType').val() == 'Daily'){
        $from = $('#txtDate').val().trim();
        $to = 'none';
     }

     if($('#txtDateType').val() == 'Weekly'){
        $dt = $('.weeklyDate').val().split("to");
        $from = $dt[0].trim();
        $to = $dt[1].trim();               
     }

     if($('#txtDateType').val() == 'Monthly'){
        $from = $('#txtMonthlyDate').val().trim();
        $to = 'none';                
     }

     if($('#txtDateType').val() == 'Yearly'){
        $from = $('#txtYearlyDate').val().trim();
        $to = 'none';                
     }

     if($('#txtDateType').val() == 'Custom'){
        $from = $('#txtFromDate').val().trim();
        $to = $('#txtToDate').val().trim();   

        var startDate = document.getElementById("txtFromDate").value;
        var endDate = document.getElementById("txtToDate").value;

        if ((Date.parse(endDate) < Date.parse(startDate))) {
          alert("End date should be greater than Start date");
          $('#txtToDate').val($from);
          $('#txtFromDate').val($to);
          return false;
        }

     }
     $url = $url.replace("{$from}",$from);
     $url = $url.replace("{$to}",$to);
     window.location = $url;
}


function getReportData($url,$table,$all) {

    var $from;
    var $to;
    var $dt;

     if($('#txtDateType').val() == 'Daily'){
        $from = $('#txtDate').val().trim();
        $to = 'none';
     }

     if($('#txtDateType').val() == 'Weekly'){
        $dt = $('.weeklyDate').val().split("to");
        $from = $dt[0].trim();
        $to = $dt[1].trim();               
     }

     if($('#txtDateType').val() == 'Monthly'){
        $from = $('#txtMonthlyDate').val().trim();
        $to = 'none';                
     }

     if($('#txtDateType').val() == 'Yearly'){
        $from = $('#txtYearlyDate').val().trim();
        $to = 'none';                
     }

     if($('#txtDateType').val() == 'Custom'){
        $from = $('#txtFromDate').val().trim();
        $to = $('#txtToDate').val().trim();  

        var startDate = document.getElementById("txtFromDate").value;
        var endDate = document.getElementById("txtToDate").value;

        if ((Date.parse(endDate) < Date.parse(startDate))) {
          alert("End date should be greater than Start date");
          $('#txtToDate').val($from);
          $('#txtFromDate').val($to);
          return false;
        }
                      
     }


     if($all == 1){
        $url = $url.replace("{$from}",'all');
        $url = $url.replace("{$to}",'all');        
     }else{
        $url = $url.replace("{$from}",$from);
        $url = $url.replace("{$to}",$to);
     }

     $('#exportUrl').val($url);

    swal({
        title: 'Loading Data...',
        text: 'Please wait.....',
        imageHeight: 140,
        animation: false,
        showConfirmButton: false,
        allowOutsideClick: false,
        position: "center"
    });

    $.getJSON($url , null, function(data) {  
        $('#'+$table).dataTable().fnDestroy();
        var oTable = $('#'+$table).dataTable( {"bRetrieve": true , "lengthMenu": [25, 50, 75, 100 ]} );
        oTable.fnClearTable();
        if (data.length >0){
            oTable.fnAddData(data);    
        }
        $('#'+$table).DataTable().columns.adjust().responsive.recalc();
        swal.close();
    });

}

function exportReportData() {
  var url = $('#exportUrl').val();
  url = url.replace("/report_new_partner_data/","/report_new_partner_data_export/");
  window.open(url);
}

window.generateReport = generateReport;
window.getReportData = getReportData;
window.exportReportData = exportReportData;