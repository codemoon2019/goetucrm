@extends('layouts.app')

@section('content')

                @php 
                    $access = session('all_user_access'); 
                    $canEdit = false;
                    if(array_key_exists(strtolower($partner_info->partner_type_description),$access)){
                        if(strpos($access[strtolower($partner_info->partner_type_description)], 'edit') !== false){ 
                            $canEdit = true;
                        } 
                    } 
                @endphp

                @include("partners.details.profile.partnertabs")
                <!-- Tabs within a box -->
                @include("partners.details.profile.profiletabs")
                <div class="tab-content no-padding">
                    <div class="tab-pane active">
                        <div class="row">
                            <div class="row-header">
                                <h3 class="title">Attachments</h3>
                            </div>
                        </div>
                        <div class="content">
                            <div class="box-group" id="accordion">
                                <!-- we are adding the .panel class so bootstrap.js collapse plugin detects it -->
                                @if(count($attachments)>0)
                                    @foreach($attachments as $attachment)
                                    <div class="panel box box-primary">
                                        <div class="box-header with-border">
                                            <h4 class="box-title"> {{$attachment->name}} </h4>
                                            <div class="box-tools pull-right">
                                                <a href="#collapse-{{$attachment->id}}" class="btn-circle btn-circle-collapse" data-toggle="collapse" data-parent="#accordion">
                                                    <i class="fa fa-arrow-down"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div id="collapse-{{$attachment->id}}" class="panel-collapse collapse in show">
                                            <div class="box-body">
                                                <table class="table datatables table-condense table-striped">
                                                    <thead>
                                                    <th>Document Name</th>
                                                    <th>Image</th>
                                                    <th>Created By</th>
                                                    <th>Created Date</th>
                                                    </thead>
                                                    <tbody>
                                                    @if(count($attachment->details)>0)
                                                        @foreach($attachment->details as $detail)
                                                            <tr>
                                                                <td>{{$detail->name}}</td>
                                                                <td><a  target="_blank" href="/storage/attachments/{{$detail->document_image}}"><i class="fa fa-file"></i></a></td>
                                                                <td>{{$detail->create_by}}</td>
                                                                <td>{{$detail->create_date}}</td>
                                                            </tr>
                                                        @endforeach
                                                    @endif
                                                    </tbody>
                                                </table>
                                            </div>
                                            @if($canEdit)
                                            <div class="box-footer">
                                                <a href="#" id="add-file-upload" onclick="window.UploadAttachment = UploadAttachment(-1,{{$attachment->id}},'{{$attachment->name}}');"><i class="fa fa-plus-circle"></i> Add File</a>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    @endforeach
                                @endif
                            </div>
                            </div>
                            @if($canEdit)
                            <a href="#"  onclick="window.UploadAttachment = UploadAttachment(-1,-2,'');"><i class="fa fa-plus-circle"></i> Upload New File</a>
                            @endif
                        </div>
                        </div>
                    </div>
                </div>
        </section>
        <div id="modalUploadAttachment" class="modal" role="dialog">
           <form role="form" name="frmRegisterAttachment" id="frmRegisterAttachment" method="post" enctype="multipart/form-data" action="/partners/upload_attachment">
             {{ csrf_field() }}
           <input type="hidden" id="txtAttachmentId" name="txtAttachmentId"> 
           <input type="hidden" id="txtDocumentId" name="txtDocumentId"> 
           <input type="hidden" id="txtDocumentName" name="txtDocumentName"> 
           <input type="hidden" id="txtDocumentPartnerId" name="txtDocumentPartnerId" value="{{$id}}"> 
          <div class="modal-dialog modal-lg">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Attachment</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
               
              </div>
              <div class="modal-body">
                
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group" id="divUploadAttachment" style="display:none">
                                    <label>Document Name:</label>
                                    <input type="text" id="txtUploadAttachment" name="txtUploadAttachment" class="form-control"/>
                                </div>
                                <div class="form-group">
                                    <label>Select file:</label>
                                    <input type="file" id="fileUploadAttachment" name="fileUploadAttachment" accept="application/pdf,image/x-png,image/jpeg" />
                                </div>

                                <button class="btn btn-sm btn-danger clear-input" data-file_id="fileUploadAttachment">Clear Input</butto>
                            </div>
                        </div>
                    </div>
              </div>
              <div class="modal-footer">
                <button type="submit" id="btnSaveAttachment" name="btnSaveAttachment" class="btn btn-primary">Upload</button>
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
              </div>
            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
          </form>
      </div>
        <!-- /.content -->
    </div>
@endsection
@section('script')
    <script src="{{ config(' app.cdn ') . '/js/clearInput.js' . '?v=' . config(' app.version ') }}"></script>
    <script src="{{ config("app.cdn") . "/js/partners/partner.js" . "?v=" . config("app.version") }}"></script>
    <script>
        function UploadAttachment(id, document_id, document_name) {
            $('#txtAttachmentId').val(id);
            $('#txtDocumentId').val(document_id);
            $('#txtDocumentName').val(document_name);
            $('#txtUploadAttachment').val(document_name);

            if (document_id > 0 || document_name != '') {
                $('#divUploadAttachment').hide();
            } else {
                $('#divUploadAttachment').show();
            }
            $('#modalUploadAttachment').modal('show');
            return false;
        }
    </script>
@endsection
