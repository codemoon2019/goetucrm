@extends('layouts.app')

@section('style')
  <style>
    .upline,
    .uplines {
      color: #3c8dbc;
    }

    .flip-and-rotate {
      transform: rotate(-90deg) scaleX(-1);
    }
  </style>
@endsection

@section('content')
  <div class="content-wrapper">
    <section class="content-header">
      <h1>Supplier Leads</h1>
      
      <ol class="breadcrumb">
        <li><a href="/">Dashboard</a></li>
        <li class="active">List of Supplier Leads</li>
      </ol>
      
      <div class="dotted-hr"></div>
    </section>

    <section class="content container-fluid">
      <div class="col-md-12 secondary-header">
        <div class="row">
          <div class="col-md-6">
            <h5>Select a supplier lead to view their information ...</h5>
          </div>


          <div class="col-md-6">
                <button class="btn btn-primary pull-right" onclick="upload();" style="margin-right:5px">Upload Supplier Leads</button>
                <button class="btn btn-primary pull-right" style="margin-right:5px" onclick="showLPFileFormatDialog();">Get Upload File Format</button>
          </div>
        </div>
      </div>



      
      <div class="clearfix"></div>

      <table class="table">
        <thead>
          <th>I.D.</th>
          <th>Assignee / Upline</th>
          <th>Business Name</th>
          <th>Business Phone</th>
          <th>Contact Person</th>
          <th>No. of Products</th>
        </thead>
  
        <tbody>
          @foreach ($supplierLeads as $supplierLead)
            <tr>
              <td>
                <a href="{{ route('supplierLeads.show', $supplierLead->id) }}">
                  {{ $supplierLead->formatted_id }}
                </a>
              </td>

              <td style="white-space: nowrap;">
                @php $uplines = $supplierLead->partner->uplines ?? collect([]) @endphp

                <span class="{{ $uplines->count() == 0 ? '' : 'upline clickable' }}">
                  <span class="mr-2">
                    {{ $supplierLead->partner->partnerCompany->company_name ?? 'N/A' }}
                  </span>
                  
                  @if ($uplines->count() != 0) 
                    <i class="fa fa-ellipsis-h"></i>
                  @endif
                </span>
                
                @if ($uplines->count() != 0)
                  <div class="uplines clickable hidden">
                    <span>{{ $supplierLead->partner->partnerCompany->company_name }}</span><br>

                    @foreach ($uplines as $i => $upline)
                      @php $whitespace = str_repeat('&nbsp;', $i * 2); @endphp

                      <span>{!! $whitespace !!}</span><i class="fa fa-level-down flip-and-rotate"></i>
                      <span>&nbsp;{{ $upline->partnerCompany->company_name }}</span><br>
                    @endforeach
                  </div>
                @endif
              </td>

              <td>{{ $supplierLead->doing_business_as }}</td>
              <td>{{ $supplierLead->country->country_calling_code }}-{{ $supplierLead->business_phone }}</td>
              <td>{{ $supplierLead->contacts()->first()->full_name }}</td>
              <td>{{ $supplierLead->products()->count() }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </section>


        <div id="modalUploadCSV" class="modal" role="dialog">
            <form role="form" name="frmUploadCSV" id="frmUploadCSV" method="post" enctype="multipart/form-data" files="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">GoETU Supplier Leads Upload</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Select CSV file:</label>
                                        <input type="file" id="fileUploadCSV" name="fileUploadCSV" accept=".csv"/>
                                    </div>

                                    <button class="btn btn-sm btn-danger clear-input" data-file_id="fileUploadCSV">Clear Input</butto>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" id="btnUploadCSV" name="btnUploadCSV" class="btn btn-primary">Upload</button>
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
            </form>
        </div>



        <div id="modalFileFormat" class="modal fade" role="dialog">
                <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="modal-title-message">Upload File Format</h4>
                    </div>
                    <div class="modal-body">
                    <div class="row">
                        <div class="col-md-2">
                            <a href="{{ "/uploadfiles/supplierleadfilespecs.pdf" . "?v=" . config("app.version") }}" target="_blank" style="text-align:center" class="fa fa-file-pdf-o fa-5x" title="Download File Specification"></a>
                        </div>
                        <div class="col-md-4"><label>Upload File Specifications and Guidelines</label> </div>
                        <div class="col-md-2">
                            <a href="{{ "/uploadfiles/supplierleadfiletemplate.csv" . "?v=" . config("app.version") }}"  style="text-align:center" class="fa fa-file-excel-o fa-5x" title="Download Upload File Template"></a> 
                        </div>
                        <div class="col-md-4"><label>Upload File Template</label></div>
                    </div>
                    </div>
                    <div class="modal-footer">
                    
                    </div>
                </div>
                </div>
            </div>

    
  </div>
@endsection

@section('script')
  <script>
    $(document).ready(function() {

      $('.table').DataTable();
      
      $('.upline').on('click', function() {
        console.log( $(this) )
        $(this).addClass('hidden')
        $(this).siblings('.uplines').removeClass('hidden')
      })

      $('.uplines').on('click', function() {
        $(this).addClass('hidden')
        $(this).siblings('.upline').removeClass('hidden')
      })

    })


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
            url: "/supplier-leads/uploadfile", // Url to which the request is send
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

    function upload() {
      $('#modalUploadCSV').modal('show');
      return false;
    }
  </script>
@endsection