<div class="modal fade" id="ach-configuration" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">GoETU Billing</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            <form id="frmACH" name="frmACH"  method="post" enctype="multipart/form-data" action="/admin/company_settings/{{$id}}/ach_update">
            {{ csrf_field() }}
            <input type="hidden" class="form-control" id="achID" name="achID" value="-1"> 
                <div id="divACH">                        
                    <div class="form-group">
                        <label>SFTP Address:<span class="required">*</span></label>
                        <input type="text" class="form-control" name="SFTPAddress" id="SFTPAddress" value="{{old('SFTPAddress')}}" />
                    </div>
                    <div class="custom-contact-wrap-sm row">
                        <div class="col-md-6 sm-col">
                            <div class="form-group">
                                <label>SFTP Username:<span class="required">*</span></label>
                                <input type="text" class="form-control" name="SFTPUsername" id="SFTPUsername" value="{{old('SFTPUsername')}}" />
                            </div>
                        </div>
                        <div class="col-md-6 sm-col">
                            <div class="form-group">
                                <label>SFTP Password:<span class="required">*</span></label>
                                <input type="password" class="form-control" name="SFTPPassword" id="SFTPPassword" value="{{old('SFTPPassword')}}" />
                            </div>
                        </div>
                    </div>
                    <div class="custom-contact-wrap-sm row">
                        <div class="col-md-6 sm-col">
                            <div class="form-group">
                                <label>Pay To:<span class="required">*</span></label>
                                <input type="text" class="form-control" name="PayTo" id="PayTo" value="{{old('PayTo')}}"  />
                            </div>
                        </div>
                        <div class="col-md-6 sm-col">
                            <div class="form-group">
                                <label>Pay Token:<span class="required">*</span></label>
                                <input type="text" class="form-control" name="PayToken" id="PayToken" value="{{old('PayToken')}}" />
                            </div>
                        </div>
                    </div>  
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-close" data-dismiss="modal">Close</button>
                <input type="submit" class="btn btn-primary btn-save" id="btnSaveACH" name="btnSaveACH" value="Save">
            </div>
            </form>
        </div>
    </div>
</div>