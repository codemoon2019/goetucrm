import swal from "sweetalert2";
let appUrl = document.querySelector("#ctx").getAttribute("content");

$(document).ready(function() {
    $('.datatables').dataTable();
    
    $("#startdate").datepicker({ autoclose: true }); 
    $("#enddate").datepicker({ autoclose: true }); 

    $(document).on('change','#txtBuyRate', function(e){
        $('#txtBuyRate').val(parseFloat($('#txtBuyRate').val()).toFixed(2));
        if($('#txtMarkUpType').val() == 3){
            $('#lblBuyRate').html($('#txtBuyRate').val());
        }else{
            $('#lblBuyRate').html($('#txtSecondBuyRate').val());
        }           
    });

    $(document).on('change','#txtSecondBuyRate', function(e){
        $('#txtSecondBuyRate').val(parseFloat($('#txtSecondBuyRate').val()).toFixed(2));
        if($('#txtMarkUpType').val() == 3){
            $('#lblBuyRate').html($('#txtBuyRate').val());
        }else{
            $('#lblBuyRate').html($('#txtSecondBuyRate').val());
        }           
    });

    $(document).on('change','#txtMarkUpType', function(e){
        if($('#txtMarkUpType').val() == 3){
            $('#lblBuyRate').html($('#txtBuyRate').val());
        }else{
            $('#lblBuyRate').html($('#txtSecondBuyRate').val());
        }           
    });

    $(document).on('click','#btnAddProduct', function(e){
        e.preventDefault();
        var table = document.getElementById("tblProductList");
        $('#btnUpdateProduct').removeClass('hide');

        swal({
            title: 'Loading Product...',
            text: 'Please wait while loading.',
            imageUrl: appUrl + "/images/user_img/goetu-profile.png",
            imageAlt: 'GOETU Image',
            imageHeight: 140,
            animation: false,
            showConfirmButton: false,
            allowOutsideClick: false,
            position: "center"
        });

       $('#tblProductList').find('input[type="checkbox"]:checked').each(function () {
            var $this = $(this);
            var product_id = $this.attr("id");
            $this.attr('checked',false);
            if(product_id != "allcb")
            {
                var catdiv = "";
                if($('#head-'+product_id).length == 0)
                {
                    var html = '<div id="form-'+product_id+'"> <div class="accordion-head" id="head-'+product_id+'">' +
                        '<h4>'+$this.val()+'</h4>'+
                        '<div class="pull-right" style="float:right;">'+
                        '<button type="button" class="btn btn-primary btn-sm fa fa-pencil" style="margin: 2px" onclick="editAllSubProduct('+product_id+',\''+$this.val()+'\')"></button>'+
                        '<button type="button" class="btn btn-danger btn-sm fa fa-trash" style="margin: 2px" onclick="deleteAllSubProduct('+product_id+',\''+$this.val()+'\')"></button>'+
                        '</div>'+
                    '</div>'+
                    '<div class="content">'+
                        '<div class="box-group" id="accordion-'+product_id+'">'+
                            '<div class="panel box box-primary" id="cat-container-'+product_id+'">'+ catdiv+
                            '</div>'+
                        '</div>'+
                    '</div></div>';
                    $('#template-body').append(html);
                }

                $( ".mainprodcat-"+product_id ).each(function() {
                    var cat_id = $(this).val();
                    var cat_name= $(this).attr("data-name");
                    $( ".mainprod-"+product_id ).each(function() {
                        if($(this).attr("data-cat") == cat_id)
                        {
                            if($('#category-table-'+cat_id).length == 0)
                            {
                                catdiv = '<div class="box-header with-border main-prod-div-'+product_id+'" id="category-div-'+cat_id+'">'+
                                    '<h4 class="box-title"> '+ cat_name +' </h4>'+
                                    '<div class="box-tools pull-right">'+
                                        '<a href="#collapseOne-'+cat_id+'" class="btn-circle btn-circle-collapse" data-toggle="collapse" data-parent="#accordion-'+cat_id+'">'+
                                            '<i class="fa fa-arrow-down"></i>'+
                                        '</a>'+
                                    '</div>'+
                                '</div>'+
                                '<div id="collapseOne-'+cat_id+'" class="panel-collapse collapse in show">'+
                                    '<div class="box-body">'+
                                        '<table class="table datatables table-condense table-striped" id="category-table-'+cat_id+'">'+
                                            '<thead>'+
                                            '<tr>' +
                                                '<td style="width:20%">Product Name</td>'+
                                                '<td style="width:10%">Cost ($)</td>'+
                                                '<td style="width:10%">Buy Rate ($)</td>'+
                                                '<td style="width:10%">Payment Frequency</td>'+
                                                '<td style="width:10%">Mark Up Split Percentage</td>'+
                                                '<td style="width:10%">SRP ($)</td>'+
                                                '<td style="width:10%">Actions</td>'+
                                                '</tr>'+
                                            '</thead>'+
                                            '<tbody>' + 
                                            '</tbody>'+
                                        '</table>'+
                                    '</div>'+
                                '</div>';
                                $('#cat-container-'+product_id).append(catdiv);
                            }


                            if($('#table-prod-'+$(this).val()).length == 0)
                            {
                                var table = document.getElementById('category-table-'+cat_id);
                                var row = table.getElementsByTagName('tbody')[0].insertRow(-1);
                                var i=0;
                                var field_product_name = row.insertCell(i++);
                                var field_buy_rate = row.insertCell(i++);
                                var field_cost = row.insertCell(i++);
                                var my_base_rate = row.insertCell(i++);
                                var second_base_rate = row.insertCell(i++);
                                var field_payment_frequency = row.insertCell(i++);
                                var split_type = row.insertCell(i++);
                                var split_percentage_text = row.insertCell(i++);
                                var srp = row.insertCell(i++);
                                var action = row.insertCell(i++);
                                var upline_percent = row.insertCell(i++);
                                var downline_percent = row.insertCell(i++);
                                var pricing_option = row.insertCell(i++);
                                var price = row.insertCell(i++);
                                var split_percentage = row.insertCell(i++);
                                var payment_frequency = row.insertCell(i++);
                                var commission_type = row.insertCell(i++);
                                var commission = row.insertCell(i++);
                                var cost_multiplier = row.insertCell(i++);
                                
                                var mrp = row.insertCell(i++);
                                var cm_value = row.insertCell(i++);
                                var cm_type = row.insertCell(i++);
                                var bonus = row.insertCell(i++);
                                var bonus_type = row.insertCell(i++);
                                var bonus_amount = row.insertCell(i++);
                                var sub_product_id = row.insertCell(i++);
                                row.className = "subproductrecord cat-table-"+cat_id;
                                row.id = "table-prod-"+$(this).val();
                                field_product_name.className = "table-val-name";
                                field_buy_rate.className = "table-val-buy_rate";
                                field_cost.className = "table-val-cost";
                                field_payment_frequency.className = "table-val-frequency";
                                my_base_rate.className = "table-val-downline_buy_rate";
                                second_base_rate.className = "table-val-second_buy_rate";
                                split_type.className = "table-val-split_type";
                                split_percentage_text.className = "table-val-split_percentage_text";
                                split_percentage.className = "table-val-split_percentage";
                                upline_percent.className ="table-val-upline_percent";
                                downline_percent.className = "table-val-downline_percent";
                                pricing_option.className = "table-val-pricing_option";
                                payment_frequency.className = "table-val-payment_frequency";
                                price.className = "table-val-price";
                                commission_type.className = "table-val-commission_type";
                                commission.className = "table-val-commission";
                                cost_multiplier.className = "table-val-cost_multiplier";
                                srp.className = "table-val-srp";
                                mrp.className = "table-val-mrp";
                                cm_value.className = "table-val-cm_value";
                                cm_type.className = "table-val-cm_type";
                                bonus.className = "table-val-bonus";
                                bonus_type.className = "table-val-bonus_type";
                                bonus_amount.className = "table-val-bonus_amount";
                                sub_product_id.className = "table-val-sub_product_id";
                                field_buy_rate.style.display ="none";
                                second_base_rate.style.display ="none";
                                split_type.style.display ="none";
                                upline_percent.style.display ="none";
                                downline_percent.style.display ="none";
                                pricing_option.style.display ="none";
                                price.style.display ="none";
                                split_percentage.style.display ="none";
                                payment_frequency.style.display ="none";
                                commission_type.style.display ="none";
                                commission.style.display ="none";
                                cost_multiplier.style.display ="none";
                                // srp.style.display ="none";
                                mrp.style.display ="none";
                                cm_value.style.display ="none";
                                cm_type.style.display ="none";
                                sub_product_id.style.display ="none";

                                cost_multiplier.innerHTML = "1";
                                cm_value.innerHTML = "30.00";
                                cm_type.innerHTML = "percentage";

                                bonus.style.display ="none";
                                bonus_type.style.display ="none";
                                bonus_amount.style.display ="none";

                                bonus.innerHTML = "0";
                                bonus_type.innerHTML = "percentage";
                                bonus_amount.innerHTML = "0.00";

                                sub_product_id.innerHTML =$(this).val();
                                action.innerHTML = '<button type="button" class="btn btn-primary btn-sm fa fa-pencil" style="margin: 2px" onclick="editRow('+$(this).val()+')" ></button>'+
                                '<button type="button" class="btn btn-danger btn-sm fa fa-trash" style="margin: 2px" onclick="deleteRow(this,\''+cat_id+'\',\''+product_id+'\',\''+$(this).val()+'\')"></button>';
                                field_product_name.innerHTML = $(this).attr("data-name");
                                field_buy_rate.innerHTML = $(this).attr("data-brate");
                                field_cost.innerHTML = $(this).attr("data-brate");     

                                var modProdID = $(this).val();
                                if($( ".subprod-"+modProdID ).length != 0){
                                    var row = table.getElementsByTagName('tbody')[0].insertRow(-1);
                                    row.id = 'table-prod-mod-'+modProdID;
                                    var filler = row.insertCell(0);
                                    var submodule = row.insertCell(1);
                                    submodule.colSpan = 8;
                                    html = "";
                                    $( ".subprod-"+modProdID).each(function() {
                                        html = html + '<table style="display: inline-table;width:390px" id="prod-mod-table-'+$(this).val()+'"><tbody></tbody></table>';
                                    });
                                    submodule.innerHTML = html;
                                    $( ".subprod-"+modProdID).each(function() {
                                        table = document.getElementById('prod-mod-table-'+$(this).val());
                                        var row = table.getElementsByTagName('tbody')[0].insertRow(0);
                                        var checkbox = row.insertCell(0);
                                        var name = row.insertCell(1);
                                        var value = row.insertCell(2);
                                        var id = row.insertCell(3);
                                        var type = '';
                                        checkbox.className = "td-checkbox";
                                        checkbox.width = "10%";
                                        name.className = "table-name";
                                        name.width = "60%";
                                        value.className = "table-value";
                                        id.className = "table-id";
                                        id.style.display ="none";
                                        id.innerHTML = $(this).val();
                                        if($(this).attr("data-type") == 'percentage'){
                                            type = '(%)';
                                        }else{
                                            type = '';
                                        }
                                        checkbox.innerHTML = '<input type="checkbox" id="use-module-'+$(this).val()+'" checked>';
                                        name.innerHTML = $(this).attr("data-name")+'<input type="hidden" id="module-name-'+$(this).val()+'" value="'+$(this).attr("data-name")+'"><input type="hidden" id="module-type-'+$(this).val()+'" value="'+$(this).attr("data-type")+'">';
                                        if($(this).attr("data-type") == 'checkbox'){
                                            if($(this).attr("data-val") == "yes"){
                                                value.innerHTML = '<select id="module-'+$(this).val()+'"><option value="yes" selected>Yes</option value="no"><option>No</option></select>' ;
                                            }else{
                                                value.innerHTML = '<select id="module-'+$(this).val()+'"><option value="yes">Yes</option value="no" selected><option>No</option></select>' ;
                                            }
                                           
                                        }else{
                                           value.innerHTML = '<input type="text" style="width:50%" name="module-'+$(this).val()+'" id="module-'+$(this).val()+'" value="'+ $(this).attr("data-val")+'" />' + type; 
                                        }
                                    });   
                                }

                            }
                        }
                    });
                });
            }
            
        });
        swal.close();
        $('#selectProduct').modal('hide');
    });


    $(document).on('click','#btnLoadProdTemplate', function(e){
        e.preventDefault();
        var selected = $("input[name='prod-fee-template']:checked");
        var template_id;
        if (selected.length > 0) {
            template_id = selected.val();
        }
        else
        {
            template_id = -1;
            alert("Please select a template");
            return false;
        }

        swal({
            title: 'Loading Product...',
            text: 'Please wait while loading.',
            imageUrl: appUrl + "/images/user_img/goetu-profile.png",
            imageAlt: 'GOETU Image',
            imageHeight: 140,
            animation: false,
            showConfirmButton: false,
            allowOutsideClick: false,
            position: "center"
        });

        $.getJSON('/partners/details/'+template_id+'/getTemplate', null, function(data) {
            // console.log(data);
            for (var i = 0; i < data.length; i++) {
                var product_id = data[i]['main_product'];
                var product_name = data[i]['main_product_name'];
                var catdiv = "";
                if($('#head-'+product_id).length == 0 && $('.mainprodcat-'+product_id).length > 0)
                {
                    var html = '<div id="form-'+product_id+'"> <div class="accordion-head" id="head-'+product_id+'">' +
                        '<h4>'+product_name+'</h4>'+
                        '<div class="pull-right" style="float:right;">'+
                        '<a href="#" class="btn btn-primary btn-sm fa fa-pencil" style="margin: 2px"  onclick="editAllSubProduct('+product_id+',\''+product_name+'\')"></a>'+
                        '<a href="#" class="btn btn-danger btn-sm fa fa-trash" style="margin: 2px" onclick="deleteAllSubProduct('+product_id+',\''+product_name+'\')"></a>'+
                        '</div>'+
                    '</div>'+
                    '<div class="content">'+
                        '<div class="box-group" id="accordion-'+product_id+'">'+
                            '<div class="panel box box-primary" id="cat-container-'+product_id+'">'+ catdiv+
                            '</div>'+
                        '</div>'+
                    '</div></div>';
                    $('#template-body').append(html);
                }

                $(".mainprodcat-"+product_id).each(function() {
                    var cat_id = $(this).val();
                    var cat_name= $(this).attr("data-name");
                    $( ".mainprod-"+product_id ).each(function() {
                        if($(this).attr("data-cat") == cat_id)
                        {
                            if($(this).val() == data[i]['product_id'])
                            {
                                if($('#category-table-'+cat_id).length == 0)
                                {
                                    var catdiv = '<div class="box-header with-border main-prod-div-'+product_id+'" id="category-div-'+cat_id+'">'+
                                        '<h4 class="box-title"> '+ cat_name +' </h4>'+
                                        '<div class="box-tools pull-right">'+
                                            '<a href="#collapseOne-'+cat_id+'" class="btn-circle btn-circle-collapse" data-toggle="collapse" data-parent="#accordion-'+cat_id+'">'+
                                                '<i class="fa fa-arrow-down"></i>'+
                                            '</a>'+
                                        '</div>'+
                                    '</div>'+
                                    '<div id="collapseOne-'+cat_id+'" class="panel-collapse collapse in show">'+
                                        '<div class="box-body">'+
                                            '<table class="table datatables table-condense table-striped" id="category-table-'+cat_id+'">'+
                                                '<thead>'+
                                                '<tr>' +
                                                '<td style="width:20%">Product Name</td>'+
                                                '<td style="width:10%">Cost ($)</td>'+
                                                '<td style="width:10%">Buy Rate ($)</td>'+
                                                '<td style="width:10%">Payment Frequency</td>'+
                                                '<td style="width:10%">Mark Up Split Percentage</td>'+
                                                '<td style="width:10%">SRP ($)</td>'+
                                                '<td style="width:10%">Actions</td>'+
                                                    '</tr>'+
                                                '</thead>'+
                                                '<tbody>' + 
                                                '</tbody>'+
                                            '</table>'+
                                        '</div>'+
                                    '</div>';
                                    $('#cat-container-'+product_id).append(catdiv);
                                }


                                if($('#table-prod-'+$(this).val()).length != 0)
                                {
                                  var row = document.getElementById('table-prod-'+$(this).val());
                                  row.parentNode.removeChild(row);
                                  if($('#table-prod-mod-'+$(this).val()).length != 0){
                                    var row = document.getElementById('table-prod-mod-'+$(this).val());
                                    row.parentNode.removeChild(row);
                                  }
                                }

                                var table = document.getElementById('category-table-'+cat_id);
                                var row = table.getElementsByTagName('tbody')[0].insertRow(-1);
                                var index=0;
                                var field_product_name = row.insertCell(index++);
                                var field_buy_rate = row.insertCell(index++);
                                var field_cost = row.insertCell(index++);
                                var my_base_rate = row.insertCell(index++);
                                var second_base_rate = row.insertCell(index++);
                                var field_payment_frequency = row.insertCell(index++);
                                var split_type = row.insertCell(index++);
                                var split_percentage_text = row.insertCell(index++);
                                var srp = row.insertCell(index++);
                                var action = row.insertCell(index++);
                                var upline_percent = row.insertCell(index++);
                                var downline_percent = row.insertCell(index++);
                                var pricing_option = row.insertCell(index++);
                                var price = row.insertCell(index++);
                                var split_percentage = row.insertCell(index++);
                                var payment_frequency = row.insertCell(index++);
                                var commission_type = row.insertCell(index++);
                                var commission = row.insertCell(index++);
                                var cost_multiplier = row.insertCell(index++);
                                
                                var mrp = row.insertCell(index++);
                                var cm_value = row.insertCell(index++);
                                var cm_type = row.insertCell(index++);
                                var bonus = row.insertCell(index++);
                                var bonus_type = row.insertCell(index++);
                                var bonus_amount = row.insertCell(index++);
                                var sub_product_id = row.insertCell(index++);
                                row.className = "subproductrecord cat-table-"+cat_id;
                                row.id = "table-prod-"+$(this).val();
                                field_product_name.className = "table-val-name";
                                field_product_name.innerHTML = data[i]['product_name'];

                                field_buy_rate.className = "table-val-buy_rate";
                                field_buy_rate.innerHTML = data[i]['buy_rate'];

                                field_cost.className = "table-val-cost";
                                field_cost.innerHTML = data[i]['cost'];

                                field_payment_frequency.className = "table-val-frequency";
                                field_payment_frequency.innerHTML = data[i]['payment_frequency'];

                                my_base_rate.className = "table-val-downline_buy_rate";
                                my_base_rate.innerHTML = data[i]['downline_buy_rate'];

                                second_base_rate.className = "table-val-second_buy_rate";
                                second_base_rate.innerHTML = data[i]['other_buy_rate'];

                                if(data[i]['split_type'] == 'First Buy Rate'){
                                    second_base_rate.style.display ="none";
                                }else{
                                    my_base_rate.style.display ="none";
                                }

                                split_type.className = "table-val-split_type";
                                split_type.innerHTML = data[i]['split_type'];

                                if(data[i]['is_split_percentage'] == 1){
                                    var split = 'Upline:'+data[i]['upline_percentage']+'% <br> Downline:'+data[i]['downline_percentage']+' %';
                                    var split2 = 'YES';
                                }else{
                                    var split = 'NO';
                                    var split2 = 'NO';
                                }
                                
                                split_percentage_text.className = "table-val-split_percentage_text";
                                split_percentage_text.innerHTML = split;

                                split_percentage.className = "table-val-split_percentage";
                                split_percentage.innerHTML = split2;

                                upline_percent.className ="table-val-upline_percent";
                                upline_percent.innerHTML = data[i]['upline_percentage'];

                                downline_percent.className = "table-val-downline_percent";
                                downline_percent.innerHTML = data[i]['downline_percentage'];

                                pricing_option.className = "table-val-pricing_option";
                                pricing_option.innerHTML = data[i]['pricing_option'];

                                payment_frequency.className = "table-val-payment_frequency";
                                payment_frequency.innerHTML = data[i]['frequency_id'];

                                price.className = "table-val-price";
                                price.innerHTML = data[i]['price'];

                                commission_type.className = "table-val-commission_type";
                                commission_type.innerHTML = data[i]['commission_type'];

                                if(data[i]['commission_type'] == 'fixed'){
                                    var commissions = data[i]['commission_fixed'];
                                }else{
                                    var commissions = data[i]['commission_based'];
                                }

                                commission.className = "table-val-commission";
                                commission.innerHTML = commissions;

                                cost_multiplier.className = "table-val-cost_multiplier";
                                cost_multiplier.innerHTML = data[i]['cost_multiplier'];

                                srp.className = "table-val-srp";
                                srp.innerHTML = data[i]['srp'];
                                mrp.className = "table-val-mrp";
                                mrp.innerHTML = data[i]['mrp'];

                                cm_value.className = "table-val-cm_value";
                                cm_value.innerHTML = data[i]['cost_multiplier_value'];
                                cm_type.className = "table-val-cm_type";
                                cm_type.innerHTML = data[i]['cost_multiplier_type'];

                                bonus.className = "table-val-bonus";
                                bonus_type.className = "table-val-bonus_type";
                                bonus_amount.className = "table-val-bonus_amount";
                                
                                bonus.style.display ="none";
                                bonus_type.style.display ="none";
                                bonus_amount.style.display ="none";

                                bonus.innerHTML = data[i]['bonus'];;
                                bonus_type.innerHTML = data[i]['bonus_type'];;
                                bonus_amount.innerHTML = data[i]['bonus_amount'];;

                                bonus.innerHTML = "0";
                                bonus_type.innerHTML = "percentage";
                                bonus_amount.innerHTML = "0.00";

                                sub_product_id.className = "table-val-sub_product_id";
                                field_buy_rate.style.display ="none";
                                split_type.style.display ="none";
                                upline_percent.style.display ="none";
                                downline_percent.style.display ="none";
                                pricing_option.style.display ="none";
                                price.style.display ="none";
                                split_percentage.style.display ="none";
                                payment_frequency.style.display ="none";
                                commission_type.style.display ="none";
                                commission.style.display ="none";
                                sub_product_id.style.display ="none";
                                cost_multiplier.style.display ="none";
                                // srp.style.display ="none";
                                mrp.style.display ="none";
                                cm_value.style.display ="none";
                                cm_type.style.display ="none";
                                sub_product_id.innerHTML =$(this).val();
                                action.innerHTML = '<button type="button" class="btn btn-primary btn-sm fa fa-pencil" style="margin: 2px"  onclick="editRow('+$(this).val()+')" ></button>'+
                                '<button type="button" class="btn btn-danger btn-sm fa fa-trash" style="margin: 2px" onclick="deleteRow(this,\''+cat_id+'\',\''+product_id+'\',\''+$(this).val()+'\')"></button>';
                                field_product_name.innerHTML = $(this).attr("data-name");
                                field_buy_rate.innerHTML = $(this).attr("data-brate");  


                                var modProdID = $(this).val();
                                if(data[i]['modules'] != "[]"){
                                    var row = table.getElementsByTagName('tbody')[0].insertRow(-1);
                                    row.id = 'table-prod-mod-'+modProdID;
                                    var filler = row.insertCell(0);
                                    var submodule = row.insertCell(1);
                                    submodule.colSpan = 8;
                                    html = "";
                                    $( ".subprod-"+modProdID).each(function() {
                                        html = html + '<table style="display: inline-table;width:390px" id="prod-mod-table-'+$(this).val()+'"><tbody></tbody></table>';
                                    });
                                    submodule.innerHTML = html;

                                    var modules = JSON.parse(data[i]['modules']);
                                    for (var k in modules) {
                                        var moduleID = modules[k].id;
                                        var table = document.getElementById('prod-mod-table-'+moduleID);
                                        var row = table.getElementsByTagName('tbody')[0].insertRow(0);
                                        var checkbox = row.insertCell(0);
                                        var name = row.insertCell(1);
                                        var value = row.insertCell(2);
                                        var id = row.insertCell(3);
                                        checkbox.className = "td-checkbox";
                                        checkbox.width = "10%";
                                        name.className = "table-name";
                                        name.width = "60%";
                                        value.className = "table-value";
                                        id.className = "table-id";
                                        id.style.display ="none";
                                        id.innerHTML = $(this).val();
                                        if(modules[k].type == 'percentage'){
                                            var type = '(%)';
                                        }else{
                                            var type = '';
                                        }
                                        if(modules[k].status == 'A'){
                                            checkbox.innerHTML = '<input type="checkbox" id="use-module-'+moduleID+'" checked>';
                                        }else{
                                            checkbox.innerHTML = '<input type="checkbox" id="use-module-'+moduleID+'">';
                                        }
                                        name.innerHTML = modules[k].name+'<input type="hidden" id="module-name-'+moduleID+'" value="'+modules[k].name+'"><input type="hidden" id="module-type-'+moduleID+'" value="'+modules[k].type+'">';
                                        if(modules[k].type == 'checkbox'){
                                            if($(this).attr("data-val") == "yes"){
                                                value.innerHTML = '<select id="module-'+moduleID+'"><option value="yes" selected>Yes</option value="no"><option>No</option></select>' ;
                                            }else{
                                                value.innerHTML = '<select id="module-'+moduleID+'"><option value="yes">Yes</option value="no" selected><option>No</option></select>' ;
                                            }
                                           
                                        }else{
                                           value.innerHTML = '<input type="text" style="width:50%" name="module-'+moduleID+'" id="module-'+moduleID+'" value="'+modules[k].value+'" />' + type; 
                                        }                                        
                                    }  
                                }

                            }
                        }
                    });
                });
            }
            swal.close();
        });

        $('#modalProductTemplateSelection').modal('hide');
    });


    $('#btnEditSubProduct').click(function(){
        var id = $('#txtSubProductId').val();
        if(isNaN($('#txtBuyRate').val())){
            alert('Incorrect Value for Buy Rate');
            return false;
        }
        if(isNaN($('#txtSecondBuyRate').val())){
            alert('Incorrect Value for Second Buy Rate');
            return false;
        }
        if(isNaN($('#txtPrice').val())){
            alert('Incorrect Value for Price');
            return false;
        }
        if(isNaN($('#fixedCommission').val())){
            alert('Incorrect Value for Commission');
            return false;
        }
        if(isNaN($('#txtDownPercentage').val())){
            alert('Incorrect Value for Downline %');
            return false;
        }
        if(isNaN($('#txtUpPercentage').val())){
            alert('Incorrect Value for Upline %');
            return false;
        }
        if(isNaN($('#txtSRP').val())){
            alert('Incorrect Value for SRP');
            return false;
        }
        if(isNaN($('#txtMRP').val())){
            alert('Incorrect Value for MRP');
            return false;
        }
        if(isNaN($('#fixedBonus').val())){
            alert('Incorrect Value for Bonus');
            return false;
        }
        if(isNaN($('#percentageBonus').val())){
            alert('Incorrect Value for Bonus');
            return false;
        }

        var srp = parseFloat($('#txtSRP').val());
        var mrp = parseFloat($('#txtMRP').val());
        var brate = parseFloat($('#txtBuyRate').val());

        if(brate > srp){
            alert('SRP should be greater than Buy Rate');
            return false;
        }

        if(mrp > 0 && brate > mrp){
            alert('MRP should be greater than Buy Rate');
            return false;
        }

        if($("#txtMarkUpType>option:selected").html() == "First Buy Rate")
        {
            $('#txtSecondBuyRate').val('0.00');
        }
        if($('#chkSplit').prop('checked')){
            var split_percentage_text ='Upline: '+$('#txtUpPercentage').val()+'% <br> Downline: '+$('#txtDownPercentage').val()+'%';
            var split_percentage ='YES';
        }
        else
        {
            $('#txtUpPercentage').val('0.00');
            $('#txtDownPercentage').val('0.00');
            var split_percentage_text ='NO';
            var split_percentage ='NO';
        }

        if($('#chkBonus').prop('checked')){
            var bonus =1;
            var bonusType = $("#BonusType>option:selected").val();
            if(bonusType == 'fixed'){
                var bonusAmount = $('#fixedBonus').val();
            }
            if(bonusType == 'percentage'){
                var bonusAmount = $('#percentageBonus').val();
            }
        }
        else
        {
            $('#fixedBonus').val('0.00');
            $('#percentageBonus').val('0.00');
            var bonus = 0;
            var bonusType = 'percentage';
            var bonusAmount = 0;
        }

        var selected_pricing = $("input:radio[name=pricing_option]:checked").val();
        var commission_type = $("#commissionType>option:selected").val()
        if(commission_type == 'based')
        {
            var commissionBased = [];
            $("#commission-case-table").find('td').each (function() {
                var cell = $(this);
                switch(cell.attr('class')) {
                    case "td-startCase":
                        var startCase = $(this).find('.startCase').val();
                        break;
                    case "td-endCase":
                        var endCase = $(this).find('.endCase').val();
                        break;
                    case "td-commission":
                        var commission = $(this).find('.commissionCase').val();
                        break;
                    case "td-action":
                        commissionBased.push({startCase: startCase, endCase: endCase, commission: commission});
                        break;   
                    default:
                        break;
                }
            });

            var commission = JSON.stringify(commissionBased);
        }
        else
        {
            var commission = $('#fixedCommission').val();
        }

        if($('#txtProductId').val() == -1)
        {
            $("#table-prod-"+id).find('td').each (function() {
                var cell = $(this);
                switch(cell.attr('class')) {
                    case "table-val-frequency":
                        cell[0].innerHTML = $("#txtPaymentFrequency>option:selected").html();
                        break;
                    case "table-val-payment_frequency":
                        cell[0].innerHTML = $('#txtPaymentFrequency').val();
                        break;
                    case "table-val-downline_buy_rate":
                        if($("#txtMarkUpType>option:selected").html() == 'First Buy Rate'){
                            cell[0].style.display ="block";
                        }else{
                            cell[0].style.display ="none";
                        }
                        cell[0].innerHTML = $('#txtBuyRate').val();
                        break;
                    case "table-val-second_buy_rate":
                        if($("#txtMarkUpType>option:selected").html() == 'Second Buy Rate'){
                            cell[0].style.display ="block";
                        }else{
                            cell[0].style.display ="none";
                        }
                        cell[0].innerHTML = $('#txtSecondBuyRate').val();
                        break;
                    case "table-val-split_type":
                        cell[0].innerHTML = $("#txtMarkUpType>option:selected").html();
                        break;
                    case "table-val-split_percentage_text":
                        cell[0].innerHTML = split_percentage_text;
                        break;
                    case "table-val-split_percentage":
                        cell[0].innerHTML = split_percentage;
                        break;
                    case "table-val-pricing_option":
                        cell[0].innerHTML = selected_pricing;
                        break;
                    case "table-val-price":
                        cell[0].innerHTML = $('#txtPrice').val();
                        break;                            
                    case "table-val-upline_percent":
                        cell[0].innerHTML = $('#txtUpPercentage').val();
                        break;   
                    case "table-val-downline_percent":
                        cell[0].innerHTML = $('#txtDownPercentage').val();
                        break;  
                    case "table-val-commission_type":
                        cell[0].innerHTML = commission_type;
                        break; 
                    case "table-val-commission":
                        cell[0].innerHTML = commission;
                        break; 
                    case "table-val-cost_multiplier":
                        cell[0].innerHTML = $('#chkCostMultiplier').prop('checked') ? 1 : 0;
                        break; 
                    case "table-val-cm_value":
                        cell[0].innerHTML = $('#CMValue').val();
                        break; 
                    case "table-val-cm_type":
                        cell[0].innerHTML = $('#CMType').val();
                        break; 
                    case "table-val-srp":
                        cell[0].innerHTML = $('#txtSRP').val();
                        break; 
                    case "table-val-mrp":
                        cell[0].innerHTML = $('#txtMRP').val();
                        break; 
                    case "table-val-bonus":
                        cell[0].innerHTML = bonus;
                        break; 
                    case "table-val-bonus_type":
                        cell[0].innerHTML = bonusType;
                        break; 
                    case "table-val-bonus_amount":
                        cell[0].innerHTML = bonusAmount;
                        break; 

                    // case "table-val-cost":
                    //     if($('#chkCostMultiplier').prop('checked')){
                    //         if($('#CMType').val() == 'percentage'){
                    //             cell[0].innerHTML = $('#txtSubProductCost').val() * ($('#CMValue').val()/100) ;
                    //         }else{
                    //             cell[0].innerHTML = $('#txtSubProductCost').val() * $('#CMValue').val();
                    //         }
                    //         cell[0].innerHTML = parseFloat(cell[0].innerHTML).toFixed(2);
                    //     }else{
                    //         cell[0].innerHTML = $('#txtSubProductCost').val();
                    //     }

                    default:
                        break;
                }
            });
        }
        else{
            var product_id = $('#txtProductId').val();
            $( ".mainprod-"+product_id ).each(function() {
                if($('#table-prod-'+$(this).val()).length > 0)
                {
                    $("#table-prod-"+$(this).val()).find('td').each (function() {
                        var cell = $(this);
                        switch(cell.attr('class')) {
                            case "table-val-frequency":
                                cell[0].innerHTML = $("#txtPaymentFrequency>option:selected").html();
                                break;
                            case "table-val-payment_frequency":
                                cell[0].innerHTML = $('#txtPaymentFrequency').val();
                                break;
                            case "table-val-downline_buy_rate":
                                if($("#txtMarkUpType>option:selected").html() == 'First Buy Rate'){
                                    cell[0].style.display ="block";
                                }else{
                                    cell[0].style.display ="none";
                                }
                                cell[0].innerHTML = $('#txtBuyRate').val();
                                break;
                            case "table-val-second_buy_rate":
                                if($("#txtMarkUpType>option:selected").html() == 'Second Buy Rate'){
                                    cell[0].style.display ="block";
                                }else{
                                    cell[0].style.display ="none";
                                }
                                cell[0].innerHTML = $('#txtSecondBuyRate').val();
                                break;
                            case "table-val-split_type":
                                cell[0].innerHTML = $("#txtMarkUpType>option:selected").html();
                                break;
                            case "table-val-split_percentage_text":
                                cell[0].innerHTML = split_percentage_text;
                                break;
                            case "table-val-split_percentage":
                                cell[0].innerHTML = split_percentage;
                                break;
                            case "table-val-pricing_option":
                                cell[0].innerHTML = selected_pricing;
                                break;
                            case "table-val-price":
                                cell[0].innerHTML = $('#txtPrice').val();
                                break;                            
                            case "table-val-upline_percent":
                                cell[0].innerHTML = $('#txtUpPercentage').val();
                                break;   
                            case "table-val-downline_percent":
                                cell[0].innerHTML = $('#txtDownPercentage').val();
                                break;  
                            case "table-val-commission_type":
                                cell[0].innerHTML = commission_type;
                                break; 
                            case "table-val-commission":
                                cell[0].innerHTML = commission;
                                break; 
                            case "table-val-cost_multiplier":
                                cell[0].innerHTML = $('#chkCostMultiplier').prop('checked') ? 1 : 0;
                                break; 
                            case "table-val-cm_value":
                                cell[0].innerHTML = $('#CMValue').val();
                                break; 
                            case "table-val-cm_type":
                                cell[0].innerHTML = $('#CMType').val();
                                break; 
                            case "table-val-buy_rate":
                                var cost = cell[0].innerHTML;
                                break;
                            case "table-val-srp":
                                cell[0].innerHTML = $('#txtSRP').val();
                                break; 
                            case "table-val-mrp":
                                cell[0].innerHTML = $('#txtMRP').val();
                                break; 
                            case "table-val-bonus":
                                cell[0].innerHTML = bonus;
                                break; 
                            case "table-val-bonus_type":
                                cell[0].innerHTML = bonusType;
                                break; 
                            case "table-val-bonus_amount":
                                cell[0].innerHTML = bonusAmount;
                                break; 

                            // case "table-val-cost":
                            //     if($('#chkCostMultiplier').prop('checked')){
                            //         if($('#CMType').val() == 'percentage'){
                            //             cell[0].innerHTML = cost * ($('#CMValue').val()/100) ;
                            //         }else{
                            //             cell[0].innerHTML = cost* $('#CMValue').val();
                            //         }
                            //         cell[0].innerHTML = parseFloat(cell[0].innerHTML).toFixed(2);
                            //     }else{
                            //         cell[0].innerHTML = cost;
                            //     }
                            default:
                                break;
                        }
                    });
                }
            });

        }
        

        $('#commissionAndRates').modal('hide');
    });

    $(document).on('change','#allcb', function(e){
        $('#tblProductList').find('input[type="checkbox"]').each(function () {
            var $this = $(this);
            if($('#allcb').prop('checked')){
                $this.prop('checked',true);
            }
            else{
                $this.prop('checked',false);
            }
        });            
    });

    $("#chkSplit").change(function () {
        var split = $("#chkSplit").is(":checked");
        if(split)
        {
            $("#divPercentageSplit").show();
        }else{
            $("#divPercentageSplit").hide();
        }
            
    });

    $("#chkBonus").change(function () {
        var split = $("#chkBonus").is(":checked");
        if(split)
        {
            $("#bonusDiv").show();
        }else{
            $("#bonusDiv").hide();
        }
            
    });

    $(document).on('change','#BonusType', function(e){
        var selected = $(this).val();
        if(selected == 'fixed')
        {
            $("#divFixedBonus").show();
            $("#divPercentBonus").hide();
        }

        if(selected == 'percentage')
        {
            $("#divFixedBonus").hide();
            $("#divPercentBonus").show();
        }

    });



    $(document).on('change','#commissionType', function(e){
        var selected = $(this).val();
        if(selected == 'fixed')
        {
            $("#divFixedPercentage").show();
            $("#divPercentBased").hide();
        }

        if(selected == 'based')
        {
            $("#divFixedPercentage").hide();
            $("#divPercentBased").show();
        }

    });

    $(document).on('change','#txtMarkUpType', function(e){
        var selected = $(this).val();
        if(selected == 4)
        {
            $("#div2ndBrate").show();
        }else{
            $("#div2ndBrate").hide();
        }
    });

    $("#txtUpPercentage").keyup(function () {
        var val = $("#txtUpPercentage").val();
        var down = 100-val; 
        $("#txtDownPercentage").val(down);
            
    });
    
    $("#txtDownPercentage").keyup(function () {
        var val = $("#txtDownPercentage").val();
        var down = 100-val; 
        $("#txtUpPercentage").val(down);
    });

    
    $(document).on('click','#btnUpdateProduct', function(e){
        $("#asTemplate").val(0);
        $('#frmProductFee').submit();
    });

    $(document).on('click','#btnSaveAsTemplate', function(e){
        if($("#newTemplateName").val() == "")
        {
            alert('Template Name Required!')
            return false;
        }
        $("#asTemplate").val(1);
        $("#txtTemplateName").val($("#newTemplateName").val());
        $("#txtTemplateDescription").val($("#newTemplateDescription").val());
        $('#frmProductFee').submit();
    });

    $('#frmProductFee').submit(function(){
        var details = [];
        var cost;
        var product_id;
        var frequency;
        var buyrate;
        var second_buyrate;
        var split_type;
        var split_percentage;
        var pricing_option;
        var price;
        var upline_percent;
        var downline_percent;
        var commission_type;
        var commission;
        var product_module;
        var cost_multiplier;
        var cost_multiplier_value;
        var cost_multiplier_type;
        var srp;
        var mrp;

        var bonus;
        var bonus_type;
        var bonus_amount;

        var count = 0;
        var hasError = false;

        swal({
            title: 'Saving Product...',
            text: 'Please wait while saving.',
            imageUrl: appUrl + "/images/user_img/goetu-profile.png",
            imageAlt: 'GOETU Image',
            imageHeight: 140,
            animation: false,
            showConfirmButton: false,
            allowOutsideClick: false,
            position: "center"
        });


        $( ".subproductrecord").find('td').each(function() {
            var cell = $(this);
            if(cell[0].innerHTML == "")
            {
                if(cell.attr('class') == "table-val-cm_type"){
                    cell[0].innerHTML = 'percentage';
                }
                if(cell.attr('class') == "table-val-cm_value"){
                    cell[0].innerHTML = 0;
                }
            }
            if(cell[0].innerHTML == "")
            {
                alert('Please fill up all product details to proceed.');
                hasError = true;
                return false;  
            }

            switch(cell.attr('class')) {
                case "table-val-sub_product_id":
                    product_id  = cell[0].innerHTML;
                    var product_module = [];
                    if($('#table-prod-mod-'+product_id).length != 0)
                    {
                        $( ".subprod-"+product_id).each(function() { 
                            var module_id = $(this).val();
                            if($("#use-module-"+module_id).length != 0){
                                if($("#use-module-"+module_id).prop('checked')){
                                    var status = 'A';
                                }else{
                                    var status = 'D';
                                }
                                var value = $("#module-"+module_id).val();
                                var name = $("#module-name-"+module_id).val();
                                var type = $("#module-type-"+module_id).val();
                                product_module.push({id: module_id,name: name, status: status,type: type, value: value});                                
                            }

                        });                          
                    }
                    product_module = JSON.stringify(product_module);

                    details.push({product_id: product_id, 
                                    frequency: frequency, 
                                    cost: cost,
                                    buyrate: buyrate, 
                                    second_buyrate: second_buyrate,
                                    split_type: split_type,
                                    split_percentage: split_percentage,
                                    pricing_option: pricing_option,
                                    price: price,
                                    upline_percent: upline_percent,
                                    downline_percent: downline_percent,
                                    commission_type: commission_type,
                                    commission: commission,
                                    product_module: product_module,
                                    cost_multiplier: cost_multiplier,
                                    cost_multiplier_value: cost_multiplier_value,
                                    cost_multiplier_type: cost_multiplier_type,
                                    srp: srp,
                                    mrp: mrp,
                                    bonus: bonus,
                                    bonus_type: bonus_type,
                                    bonus_amount: bonus_amount,
                                    });
                    break;
                case "table-val-cost_multiplier":
                    cost_multiplier = cell[0].innerHTML;
                    break;          
                case "table-val-cm_value":
                    cost_multiplier_value = cell[0].innerHTML;
                    break; 
                case "table-val-cm_type":
                    cost_multiplier_type = cell[0].innerHTML;
                    break; 
                case "table-val-buy_rate":
                    cost = cell[0].innerHTML;
                    break;                            
                case "table-val-frequency":
                    frequency = cell[0].innerHTML;
                    break;
                case "table-val-downline_buy_rate":
                    buyrate = cell[0].innerHTML; 
                    break;
                case "table-val-second_buy_rate":
                    second_buyrate = cell[0].innerHTML; 
                    break;
                case "table-val-split_type":
                    split_type = cell[0].innerHTML;
                    break;
                case "table-val-split_percentage":
                    split_percentage = cell[0].innerHTML;
                    break;
                case "table-val-pricing_option":
                    pricing_option = cell[0].innerHTML;
                    break;
                case "table-val-price":
                    price = cell[0].innerHTML; 
                    break;                            
                case "table-val-upline_percent":
                    upline_percent = cell[0].innerHTML; 
                    break;   
                case "table-val-downline_percent":
                    downline_percent = cell[0].innerHTML; 
                    break;  
                case "table-val-commission_type":
                    commission_type = cell[0].innerHTML; 
                    break; 
                case "table-val-commission":
                    commission = cell[0].innerHTML; 
                    break; 
                case "table-val-srp":
                    srp = cell[0].innerHTML; 
                    break;
                case "table-val-mrp":
                    mrp = cell[0].innerHTML; 
                    break;
                case "table-val-bonus":
                    bonus = cell[0].innerHTML; 
                    break;
                case "table-val-bonus_type":
                    bonus_type = cell[0].innerHTML; 
                    break;
                case "table-val-bonus_amount":
                    bonus_amount = cell[0].innerHTML; 
                    break;

                default:
                    break;
            }
            count++;
        });
        if(hasError){
            swal.close();
            return false;
        }
        if(count == 0){
            swal.close();
            alert('Cannot Save. No Product Defined!');
            return false;
        }
        $('#txtDetail').val(JSON.stringify(details));
    });

});


function validateField(element,msg)
{
    if($('#'+element).val().trim() == ""){
        document.getElementById(element).style.borderColor = "red";
        alert(msg);
        return false;
    }else{
        document.getElementById(element).style.removeProperty('border');
        return true;
    }            
} 


function deleteRow(btn,catID,prodID,subProdID) {
  var row = btn.parentNode.parentNode;
  row.parentNode.removeChild(row);
  if($('#table-prod-mod-'+subProdID).length > 0){
      var element =  document.getElementById('table-prod-mod-'+subProdID);
      element.parentNode.removeChild(element);    
  }

  if($('.cat-table-'+catID).length == 0)
  {
    var element =  document.getElementById('category-div-'+catID);
    element.parentNode.removeChild(element);
    element =  document.getElementById('collapseOne-'+catID);
    element.parentNode.removeChild(element);   
    if($('.main-prod-div-'+prodID).length == 0)   
    {
        element =  document.getElementById('form-'+prodID);
        element.parentNode.removeChild(element);                 
    }
  }
}

function validate_numeric_input(evt) {
  var theEvent = evt || window.event;
  var key = theEvent.keyCode || theEvent.which;
  key = String.fromCharCode( key );
  var regex = /[0-9\b]|\./;
  if( !regex.test(key) ) {
    theEvent.returnValue = false;
    if(theEvent.preventDefault) theEvent.preventDefault();
  }
}



function editRow(prodID) {
    $("#table-prod-"+prodID).find('td').each (function() {
        var cell = $(this);
        if(cell.attr('class') == "table-val-name")
        {
            $('#txtSubProductName').val(cell[0].innerHTML);
        }
        if(cell.attr('class') == "table-val-buy_rate")
        {
            $('#txtSubProductCost').val(cell[0].innerHTML);
        }  
        var val;

        switch(cell.attr('class')) {
            case "table-val-name":
                $('#txtSubProductName').val(cell[0].innerHTML);
                break;
            case "table-val-buy_rate":
                $('#txtSubProductCost').val(cell[0].innerHTML);
                break;
            case "table-val-payment_frequency":
                if(cell[0].innerHTML == "")
                {
                    $('#txtPaymentFrequency').val(7);
                }else{
                    $('#txtPaymentFrequency').val(cell[0].innerHTML); 
                }
                break;
            case "table-val-downline_buy_rate":
                val =  (cell[0].innerHTML == "") ? "0.00" : cell[0].innerHTML; 
                $('#txtBuyRate').val(val);
                break;
            case "table-val-second_buy_rate":
                val =  (cell[0].innerHTML == "") ? "0.00" : cell[0].innerHTML; 
                $('#txtSecondBuyRate').val(cell[0].innerHTML);
                break;
            case "table-val-split_type":
                if(cell[0].innerHTML == "Second Buy Rate")
                {
                    $('#txtMarkUpType').val(4);
                    $("#div2ndBrate").show();
                }else{
                    $('#txtMarkUpType').val(3);
                    $("#div2ndBrate").hide();
                    $('#txtSecondBuyRate').val('0.00');
                }
                break;
            case "table-val-split_percentage":
                if(cell[0].innerHTML.trim() == "YES")
                {
                    $('#chkSplit').prop('checked',true);
                    $("#divPercentageSplit").show();
                }else{
                    $('#chkSplit').prop('checked',false);
                    $("#divPercentageSplit").hide();
                    $('#txtUpPercentage').val("0.00");
                    $('#txtDownPercentage').val("0.00");
                }
                break;
            case "table-val-bonus":
                if(cell[0].innerHTML.trim() == 1)
                {
                    $('#chkBonus').prop('checked',true);
                    $("#bonusDiv").show();
                }else{
                    $('#chkBonus').prop('checked',false);
                    $("#bonusDiv").hide();
                    $('#fixedBonus').val("0.00");
                    $('#percentageBonus').val("0.00");
                }
                break;
            case "table-val-bonus_type":
                if(cell[0].innerHTML.trim() == 'fixed')
                {
                    $("#divFixedBonus").show();
                    $("#divPercentBonus").hide();
                }else{
                    $("#divFixedBonus").hide();
                    $("#divPercentBonus").show();
                }
                break;
            case "table-val-bonus_amount":
                $("#fixedBonus").val(cell[0].innerHTML.trim());
                $("#percentageBonus").val(cell[0].innerHTML.trim());
                break;
            case "table-val-pricing_option":
                if(cell[0].innerHTML == "MRP")
                {
                    $('#pricing_optionMRP').prop('checked',true);
                }else{
                    $('#pricing_optionSRP').prop('checked',true);
                }
                break;
            case "table-val-price":
                val =  (cell[0].innerHTML == "") ? "0.00" : cell[0].innerHTML;
                $('#txtPrice').val(val);
                break;                            
            case "table-val-upline_percent":
                val =  (cell[0].innerHTML == "") ? "0.00" : cell[0].innerHTML;
                $('#txtUpPercentage').val(val);
                break;   
            case "table-val-downline_percent":
                val =  (cell[0].innerHTML == "") ? "0.00" : cell[0].innerHTML;
                $('#txtDownPercentage').val(val);
                break;  
            case "table-val-commission_type":
                if(cell[0].innerHTML == "based")
                {
                    $('#commissionType').val("based");
                    $("#divFixedPercentage").hide();
                    $("#divPercentBased").show();
                }else{
                    $('#commissionType').val("fixed");
                    $("#divFixedPercentage").show();
                    $("#divPercentBased").hide();                            
                }
                break; 
            case "table-val-commission":
                $("#commission-case-table").find("tr:gt(0)").remove();
                if($("#commissionType>option:selected").val() == "based")
                {
                    if(cell[0].innerHTML != "")
                    {
                        var commissions = JSON.parse(cell[0].innerHTML);
                        for (var k in commissions) {
                            var table = document.getElementById('commission-case-table');
                            var row = table.insertRow(-1);
                            var startCase = row.insertCell(0);
                            startCase.className = "td-startCase";
                            var to = row.insertCell(1);
                            var endCase = row.insertCell(2);
                            endCase.className = "td-endCase";
                            var commission = row.insertCell(3);
                            commission.className = "td-commission";
                            var action = row.insertCell(4);
                            action.className = "td-action";
                            startCase.innerHTML = '<input type="text" class="form-control startCase" value="'+commissions[k].startCase+'" width="20" onkeypress="validate_numeric_input(event);">';
                            to.innerHTML = 'to';
                            endCase.innerHTML = '<input type="text" class="form-control endCase" value="'+commissions[k].endCase+'" width="20" onkeypress="validate_numeric_input(event);">';
                            commission.innerHTML = '<input type="text" class="form-control commissionCase" value="'+commissions[k].commission+'" width="20" onkeypress="validate_numeric_input(event);">';
                            action.innerHTML = '<a href="#" onclick="deleteCommissionRow(this);"><i class="fa fa-minus-circle fa-2x"></i></a>';

                        }                                 
                    }
                }else{
                    val =  (cell[0].innerHTML == "") ? "0.00" : cell[0].innerHTML;
                    $('#fixedCommission').val(val);                          
                }
                break;
            case "table-val-cost_multiplier":
                if(cell[0].innerHTML == 1){
                    $('#chkCostMultiplier').prop('checked',true);
                }else{
                    $('#chkCostMultiplier').prop('checked',false);
                }
                break;
            case "table-val-cm_value":
                val =  (cell[0].innerHTML == "") ? "0.00" : cell[0].innerHTML;
                $('#CMValue').val(val); 
                break;
            case "table-val-cm_type":
                val =  (cell[0].innerHTML == "") ? "percentage" : cell[0].innerHTML;
                $('#CMType').val(val); 
                break;
            case "table-val-srp":
                val =  (cell[0].innerHTML == "") ? "0.00" : cell[0].innerHTML;
                $('#txtSRP').val(val);
                break;
            case "table-val-mrp":
                val =  (cell[0].innerHTML == "") ? "0.00" : cell[0].innerHTML;
                $('#txtMRP').val(val);
                break;
            default:
                break;
        }

    });
    $('#txtProductId').val(-1);
    $('#txtSubProductId').val(prodID);
    if($('#txtMarkUpType').val() == 3){
        $('#lblBuyRate').html($('#txtBuyRate').val());
    }else{
        $('#lblBuyRate').html($('#txtSecondBuyRate').val());
    }
    $('#commissionAndRates').modal('show');
}

function editAllSubProduct(prodID,prodName) {

    $('#txtSubProductName').val(prodName);
    $('#txtSubProductCost').val('N/A');
    $('#txtPaymentFrequency').val(7);
    $('#txtBuyRate').val('0.00');
    $('#txtSecondBuyRate').val('0.00');
    $('#txtMarkUpType').val(3);
    $("#div2ndBrate").hide();
    $('#txtSecondBuyRate').val('0.00');
    $('#chkSplit').prop('checked',false);
    $("#divPercentageSplit").hide();
    $('#txtUpPercentage').val("0.00");
    $('#txtDownPercentage').val("0.00");
    $('#pricing_optionSRP').prop('checked',true);
    $('#txtPrice').val('0.00');
    $('#txtSRP').val('0.00');
    $('#txtMRP').val('0.00');
    $('#commissionType').val("fixed");
    $("#divFixedPercentage").show();
    $("#divPercentBased").hide();
    $("#commission-case-table").find("tr:gt(0)").remove();
    $('#fixedCommission').val("0.00");
    $('#chkCostMultiplier').prop('checked',true);
    $('#CMValue').val('30.00');
    $('#CMType').val('percentage');
    $('#lblBuyRate').html("0.00");

    $('#chkBonus').prop('checked',false);
    $("#bonusDiv").hide();
    $('#percentageBonus').val("0.00");
    $('#fixedBonus').val("0.00");

    $('#txtProductId').val(prodID);
    $('#txtSubProductId').val(-1);
    $('#commissionAndRates').modal('show');
}

function deleteAllSubProduct(prodID,prodName) {
    var element =  document.getElementById('form-'+prodID);
    element.parentNode.removeChild(element);  
}

function add_commision_case() {
    var table = document.getElementById('commission-case-table');
    var row = table.insertRow(-1);
    var startCase = row.insertCell(0);
    startCase.className = "td-startCase";
    var to = row.insertCell(1);
    var endCase = row.insertCell(2);
    endCase.className = "td-endCase";
    var commission = row.insertCell(3);
    commission.className = "td-commission";
    var action = row.insertCell(4);
    action.className = "td-action";
    startCase.innerHTML = '<input type="text" class="form-control startCase" value="0" width="20" onkeypress="validate_numeric_input(event);">';
    to.innerHTML = 'to';
    endCase.innerHTML = '<input type="text" class="form-control endCase" value="0" width="20" onkeypress="validate_numeric_input(event);">';
    commission.innerHTML = '<input type="text" class="form-control commissionCase" value="0" width="20" onkeypress="validate_numeric_input(event);">';
    action.innerHTML = '<a href="#" onclick="deleteCommissionRow(this);"><i class="fa fa-minus-circle fa-2x"></i></a>';
}

function deleteCommissionRow(btn) {
  var row = btn.parentNode.parentNode;
  row.parentNode.removeChild(row);
}

function generateOrderList(id){
    var startdate = $('#startdate').val().replace(/\//g,' ');
    var enddate = $('#enddate').val().replace(/\//g,' ');

    if(Date.parse($('#startdate').val()) > Date.parse($('#enddate').val())){
        alert('End date should not be greater than Start date.');
        return false;
    }
    //$('#startdate_export').val($('#startdate').val());
    //$('#enddate_export').val($('#enddate').val()); 

    $('#tblSearchApplicationList').dataTable().fnDestroy();
    $('#tblSearchApplicationList').DataTable({
        processing: true,
        serverSide: true,
        ajax: '/partners/generate_order_list/'+id+'/'+startdate+'/'+enddate,
        columns: [
            { data: ''},
            { data: 'merchant_mid'},
            { data: 'company_name'},
            { data: 'date'},
            { data: 'order_id'},
            { data: 'product'},
            { data: 'application_status'},
            { data: 'product_status'},
            { data: 'agent'},
            { data: 'view' },
        ]
    });
}


window.validateField = validateField;
window.deleteRow = deleteRow;
window.validate_numeric_input = validate_numeric_input;
window.editRow = editRow;
window.editAllSubProduct = editAllSubProduct;
window.deleteAllSubProduct = deleteAllSubProduct;
window.add_commision_case = add_commision_case;
window.deleteCommissionRow = deleteCommissionRow;
window.generateOrderList = generateOrderList;