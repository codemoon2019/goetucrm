<script>
    $('.td-toggle-merchants').on('click', function () {
        let agentId = $(this).data('id')

        $('.tr-agent-merchant-' + agentId).toggleClass('hidden')
        if ($('.tr-agent-merchant-' + agentId).first().hasClass('hidden')) {
            $(this).html('&#9654;')
        } else {
            $(this).html('&#9660;')
        }
    });
    function viewMerchants($id)
    {
        $("#merchants tbody tr").remove(); 
        $.getJSON('/merchant-list/'+$id, null, function(data) {
            for (var i = 0; i < data.length; i++) {
                table = document.getElementById('merchants');
                var row = table.getElementsByTagName('tbody')[0].insertRow(-1);
                var name = row.insertCell(0);
                var email = row.insertCell(1);
                var action = row.insertCell(2);

                name.innerHTML = data[i]['company_name'];
                email.innerHTML = data[i]['email'];
                action.innerHTML = '<a href="/merchants/details/'+data[i]['id']+'/profile"><button type="button" class="btn btn-info btn-sm">View</button></a>';
            }
            $('#modalViewMerchants').modal('show');
        }); 
    }
</script>