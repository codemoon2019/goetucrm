
import { APP_URL } from "../../react/config";
import axios from "axios";
import swal from 'sweetalert2'

$(document).ready(function () {
    $('#state_ph').hide();
    $('#state_cn').hide();

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

        $.getJSON('/merchants/search/'+filterType+'/'+txtSearchValue+ '/P', null, function(data) {  
            var oTable = $('#merchant-list').dataTable( {"bRetrieve": true} );
            oTable.fnClearTable();
            if (data.length >0){
                oTable.fnAddData(data);    
            }   
        });

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

        $.getJSON('/merchants/branchSearch/'+filterType+'/'+txtSearchValue+ '/P', null, function(data) {  
            var oTable = $('#merchant-list').dataTable( {"bRetrieve": true} );
            oTable.fnClearTable();
            if (data.length >0){
                oTable.fnAddData(data);    
            }   
        });

    });


    $("input.toggle-vis").on( 'click', function(){
        var table = $('#merchant-list').DataTable();
        var column = table.column( $(this).attr('data-column') );
        column.visible( ! column.visible() );
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
        alert('Select a state!');
        return false;
    }

    $.getJSON('/merchants/advance_merchants_search/' + country + '/' + states+ '/P', null, function(data) {  
        var oTable = $('#merchant-list').dataTable( {"bRetrieve": true} );
        oTable.fnClearTable();
        if (data.length >0){
            oTable.fnAddData(data);    
        }   
    });
    $('.adv-close').click();
}

function load_merchants(){ 
    $.getJSON('/merchants/merchant_board_data', null, function(data) {  
        var oTable = $('#merchant-list').dataTable( {"bRetrieve": true} );
        oTable.fnClearTable();
        if (data.length >0){
            oTable.fnAddData(data);    
        }
        $('#merchant-list').DataTable().columns.adjust().responsive.recalc();
    });
    
}

function declineMerchant(merchantId) {
    swal({
        title: 'Reason of Action',
        input: 'text',
        inputValidator: (value) => {
            return new Promise((resolve) => {
                resolve()
            })
        },
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#808080',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Decline',
        cancelButtonText: 'Close'

    }).then((result) => {
        if (result.value) {
            axios.post(`/merchants/${merchantId}/decline`, {
                reason_of_action: result.value
            })
                .then(response => {
                    alert(response.data.message);
                    location.reload(true)
                })
                .catch(error => {
                    console.log(error)
                })
        }
    })
}

function declineBranch(merchantId) {
    swal({
        title: 'Reason of Action',
        input: 'text',
        inputValidator: (value) => {
            return new Promise((resolve) => {
                resolve()
            })
        },
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#808080',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Decline',
        cancelButtonText: 'Close'

    }).then((result) => {
        if (result.value) {
            axios.post(`/merchants/${merchantId}/declineBranch`, {
                reason_of_action: result.value
            })
                .then(response => {
                    alert(response.data.message);
                    location.reload(true)
                })
                .catch(error => {
                    console.log(error)
                })
        }
    })
}
function load_branches(){ 
    $.getJSON('/merchants/branch_board_data', null, function(data) {  
        var oTable = $('#merchant-list').dataTable( {"bRetrieve": true} );
        oTable.fnClearTable();
        if (data.length >0){
            oTable.fnAddData(data);    
        }
        $('#merchant-list').DataTable().columns.adjust().responsive.recalc();
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

    $.getJSON('/merchants/advance_branch_search/' + country + '/' + states + '/P', null, function(data) {  
        var oTable = $('#merchant-list').dataTable( {"bRetrieve": true} );
        oTable.fnClearTable();
        if (data.length >0){
            oTable.fnAddData(data);    
        }   
    });
    $('.adv-close').click();
}


window.load_merchants = load_merchants;
window.load_branches = load_branches;
window.advanceSearchMerchants = advanceSearchMerchants;
window.declineMerchant = declineMerchant;
window.declineBranch = declineBranch;
window.advanceSearchBranches = advanceSearchBranches;