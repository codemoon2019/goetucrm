
import { APP_URL } from "../../react/config";
import axios from "axios";

$(document).ready(function () {
    $('#state_ph').hide();
    $('#state_cn').hide();

    // $('#merchant-list').DataTable({
    //     serverSide: true,
    //     processing: true,
    //     searching: false,
    //     ajax: '/merchants/merchant_data',
    //     columns: [
    //         {data: 'type'},
    //         {data: 'partners'},
    //         {data: 'merchant'},
    //         {data: 'mid'},
    //         {data: 'cid'},
    //         {data: 'contact'},
    //         {data: 'mobile'},
    //         {data: 'email'},
    //         {data: 'state'},
    //         {data: 'url'},
    //         {data: 'action', name: 'action', orderable: false, searchable: false}
    //     ]
    // });

    $('#filterType').change(function () {
        switch ($(this).val()) {
            case 'all':
                $('#divSearchText').hide();
                $('#divUplinePartner').hide();
                break;
            case 'upline':
                $('#divSearchText').hide();
                $('#divUplinePartner').show();
                break;
            default:
                $('#divUplinePartner').hide();
                $('#divSearchText').show();
        }
    });
    $('#filterType').trigger('change');

    $('#country').change(function () {
        var country = document.getElementById("country");
        var country_selectedText = country.options[country.selectedIndex].text;
        var country_selectedValue = country.options[country.selectedIndex].value;

        $('#state_us').hide();
        $('#state_ph').hide();
        $('#state_cn').hide();

        if (country_selectedValue == "US") {
            $('#state_us').show();
        }
        if (country_selectedValue == "PH") {
            $('#state_ph').show();
        }
        if (country_selectedValue == "CN") {
            $('#state_cn').show();
        }
    });


    /**
     * Search merchant
     */
    $(document).on('click', '#searchMerchants', function (e) {
        e.preventDefault();

        var filterType = $('#filterType').val();
        var txtSearchValue = "";

        if(filterType == "upline") {
            txtSearchValue = $('#uplinePartner').val();
        } else {
            if($('#txtSearchValue').val() == undefined || $('#txtSearchValue').val() == null || $('#txtSearchValue').val() == "") {
                txtSearchValue = null;
            } else {
                txtSearchValue = $('#txtSearchValue').val();
            }

        }

        $.getJSON('/merchants/search/'+filterType+'/'+txtSearchValue + '/A', null, function(data) {  

            $('#merchant-list').dataTable().fnDestroy();
            var oTable = $('#merchant-list').dataTable({
                "lengthMenu": [25, 50, 75, 100 ],
                "bRetrieve": true
            });

            oTable.fnClearTable();
            if (data.length >0){
                oTable.fnAddData(data);    
            }   

            redrawTable('#merchant-list');
        });

    });

    $('#frmUploadCSV').submit(function () {
        var filename = document.getElementById("fileUploadCSV").value;
        if (document.getElementById("fileUploadCSV").value == "") {
            alert('Please select a file');
            return false;
        }
        var ext = filename.split('.').pop();
        if (ext != "csv") {
            alert('Please select csv file format.');
            return false;
        }

        $('#modalUploadCSV').modal('hide');
        showLoadingModal('Processing...');
        $.ajax({
            url: "/merchants/uploadfile", // Url to which the request is send
            type: "POST", // Type of request to be send, called as method
            data: new FormData(this), // Data sent to server, a set of key/value pairs (i.e. form fields and values)
            dataType: 'json',
            contentType: false, // The content type used when sending data to the server.
            cache: false, // To unable request pages to be cached
            processData: false, // To send DOMDocument or non processed data file it is set to false
            success: function success(data) // A function to be called if request succeeds
            {
                closeLoadingModal();
                if (!data.logs) {
                    alert(data.message);
                    var delay = 3000; //3 second
                    setTimeout(function () {
                        var str = window.location.href;
                        str = str.replace("#", '');
                        window.location.href = str;
                    }, delay);
                } else {
                    var logs = "";
                    for (var i = 0; i < data.logs.length; i++) {
                        logs = logs + data.logs[i] + " \n";
                    }
                    alert('Successfully processed file but with exceptions \n\n' + logs);
                    var delay = 3000; //3 second
                    setTimeout(function () {
                        var str = window.location.href;
                        str = str.replace("#", '');
                        window.location.href = str;
                    }, delay);
                }
            }
        });
        return false;
    });


    $(document).on('click', '#searchBranches', function (e) {
        e.preventDefault();

        var filterType = $('#filterType').val();
        var txtSearchValue = "";

        if(filterType == "upline") {
            txtSearchValue = $('#uplinePartner').val();
        } else {
            if($('#txtSearchValue').val() == undefined || $('#txtSearchValue').val() == null || $('#txtSearchValue').val() == "") {
                txtSearchValue = null;
            } else {
                txtSearchValue = $('#txtSearchValue').val();
            }

        }

        $.getJSON('/merchants/branchSearch/'+filterType+'/'+txtSearchValue + '/A', null, function(data) {  
            $('#branch-list').dataTable().fnDestroy();
            var oTable = $('#branch-list').dataTable({
                "lengthMenu": [25, 50, 75, 100 ],
                "bRetrieve": true
            });

            oTable.fnClearTable();
            if (data.length >0){
                oTable.fnAddData(data);    
            }   

            redrawTable('#branch-list');
        });

    });

});

function advanceSearchMerchants() {
    var country = document.getElementById("country");
    var country_selectedText = country.options[country.selectedIndex].text;
    var country_selectedValue = country.options[country.selectedIndex].value;

    if (country_selectedValue == "US") {
        $('#state_us').show();
        var checkboxes = document.getElementsByName("states[]");
        var country = 'United States';
    }
    if (country_selectedValue == "PH") {
        $('#state_ph').show();
        var checkboxes = document.getElementsByName("statesPH[]");
        var country = 'Philippines';
    }
    if (country_selectedValue == "CN") {
        $('#state_cn').show();
        var checkboxes = document.getElementsByName("statesCN[]");
        var country = 'China';
    }

    var states = "";
    for (var i = 0; i < checkboxes.length; i++) {
        if (checkboxes[i].checked) {
            states = states + checkboxes[i].value + ",";
        }
    }

    states = states.substr(0, states.length - 1);

    if (states == '') {
        for (var i = 0; i < checkboxes.length; i++) {
            states = states + checkboxes[i].value + ",";
        }
    }

    var status = "'A','P','C','I','V','T'";
    // $('#merchant-list').dataTable().fnDestroy();
    // $('#merchant-list').DataTable({
    //     processing: true,
    //     serverSide: true,
    //     ajax: '/merchants/advance_merchants_search/' + country + '/' + states,
    //     columns: [
    //         {data: 'type'},
    //         {data: 'partners'},
    //         {data: 'merchant'},
    //         {data: 'mid'},
    //         {data: 'cid'},
    //         {data: 'contact'},
    //         {data: 'mobile'},
    //         {data: 'email'},
    //         {data: 'state'},
    //         {data: 'url'},
    //         {data: 'partner_type'},
    //         {data: 'upline_partners'},
    //         {data: 'dba'},
    //         {data: 'merchant_mid'},
    //         {data: 'credit_card_reference_id'},
    //         {data: 'contact'},
    //         {data: 'phone1'},
    //         {data: 'email'},
    //         {data: 'state'},
    //         {data: 'merchant_url'},
    //         {data: 'action', name: 'action', orderable: false, searchable: false}
    //     ]
    // });
    $.getJSON('/merchants/advance_merchants_search/' + country + '/' + states + '/A', null, function(data) {  
        $('#merchant-list').dataTable().fnDestroy();
        var oTable = $('#merchant-list').dataTable({
            "lengthMenu": [25, 50, 75, 100 ],
            "bRetrieve": true
        });

        oTable.fnClearTable();
        if (data.length >0){
            oTable.fnAddData(data);    
        }   

        redrawTable('#merchant-list');
    });
    $('.adv-close').click();
}

function load_merchants(){ 
    $.getJSON('/merchants/merchant_data', null, function(data) {  
        $('#merchant-list').dataTable().fnDestroy();
        var oTable = $('#merchant-list').dataTable({
            "lengthMenu": [25, 50, 75, 100 ],
            "bRetrieve": true
        });

        oTable.fnClearTable();
        if (data.length >0){
            oTable.fnAddData(data);    
        }
        $('#merchant-list').DataTable().columns.adjust().responsive.recalc();

        toggleCols('#merchant-list');
    });
    
}

function upload() {
    $('#modalUploadCSV').modal('show');
    return false;
}


function load_branches(){ 
    $.getJSON('/merchants/branch_data', null, function(data) {  
        $('#branch-list').dataTable().fnDestroy();
        var oTable = $('#branch-list').dataTable({
            "lengthMenu": [25, 50, 75, 100 ],
            "bRetrieve": true
        });

        oTable.fnClearTable();
        if (data.length >0){
            oTable.fnAddData(data);    
        }
        $('#branch-list').DataTable().columns.adjust().responsive.recalc();

        toggleCols('#branch-list');
    });
    
}

function advanceSearchBranches() {
    var country = document.getElementById("country");
    var country_selectedText = country.options[country.selectedIndex].text;
    var country_selectedValue = country.options[country.selectedIndex].value;

    if (country_selectedValue == "US") {
        $('#state_us').show();
        var checkboxes = document.getElementsByName("states[]");
        var country = 'United States';
    }
    if (country_selectedValue == "PH") {
        $('#state_ph').show();
        var checkboxes = document.getElementsByName("statesPH[]");
        var country = 'Philippines';
    }
    if (country_selectedValue == "CN") {
        $('#state_cn').show();
        var checkboxes = document.getElementsByName("statesCN[]");
        var country = 'China';
    }

    var states = "";
    for (var i = 0; i < checkboxes.length; i++) {
        if (checkboxes[i].checked) {
            states = states + checkboxes[i].value + ",";
        }
    }

    states = states.substr(0, states.length - 1);

    if (states == '') {
        alert('Select a state!');
        return false;
    }

    $.getJSON('/merchants/advance_branch_search/' + country + '/' + states + '/A', null, function(data) {  
        $('#branch-list').dataTable().fnDestroy();
        var oTable = $('#branch-list').dataTable({
            "lengthMenu": [25, 50, 75, 100 ],
            "bRetrieve": true
        });

        oTable.fnClearTable();
        if (data.length >0){
            oTable.fnAddData(data);    
        }   

        redrawTable('#branch-list');
    });
    $('.adv-close').click();
}


window.load_merchants = load_merchants;
window.advanceSearchMerchants = advanceSearchMerchants;
window.upload = upload;
window.load_branches = load_branches;
window.advanceSearchBranches = advanceSearchBranches;