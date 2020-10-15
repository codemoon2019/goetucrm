<!doctype html>

<html lang="{{ app()->getLocale() }}">
<head>
    @yield("title")
    <title>{{ config("app.name") }}</title>
    @include("incs.head")
    @yield("style")
    <script>
        window.Laravel = {!! json_encode(['csrfToken' => csrf_token()]) !!};
    </script>
    @yield("headerScript")
</head>
<body class="hold-transition skin-red sidebar-mini">

<style type="text/css">
  
/*#loading {
    display: block;
    position: absolute;
    top: 0;
    left: 0;
    z-index: 100;
    width: 100vw;
    height: 100vh;
    background-color: rgba(192, 192, 192, 0.5);
    background-image: url("http://i.stack.imgur.com/MnyxU.gif");
    background-repeat: no-repeat;
    background-position: center;
}*/

#loading {
    display: block;
    position: absolute;
    top: 0;
    left: 0;
    /*z-index: 100;*/
    z-index: 2000;
    width: 100vw;
    height: 100vh;
    /*background-color: rgba(192, 192, 192, 0.5);*/
    background-color: rgb(31, 108, 199);
    background-image: url("/images/loading_book.gif");
    background-repeat: no-repeat;
    background-position: center;
}

</style>
<div id="loading"></div>
  
<div class="wrapper">
    @include("incs.header")
    @include("incs.navigator")
    @include("incs.chat")
    @include('incs.messages')
    @include('incs.notifBanner')
    @yield("content")
    @include("incs.footer")
</div>


<div id="modalLoading" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
            <h4 class="modal-title" id="modal-title-message">GoETU Billing</h4>
      </div>
      <div class="modal-body">
        <p id="modalLoadingMessage">Modal Message</p><i class="fa fa-spin fa-refresh"></i>
      </div>
      <div class="modal-footer">
        
      </div>
    </div>
  </div>
</div>

<div id="modalSuggestion" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-body">
          <form role="form" id="frmSuggestionBox" enctype="multipart/form-data" method="POST">
            {{ csrf_field() }}
            <div class="row">
                        <div class="row-header content-header">
                            <h3 class="title">Suggestion Box <i class="fa fa-lightbulb-o"></i></h3>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Have a great idea? We want to hear it!</label>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="suggestionTitle">Title:<span class="required">*</span></label>
                                <input type="text" class="form-control" name="suggestionTitle" id="suggestionTitle" value="" placeholder="Enter Suggestion Title"/>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="suggestionDescription">Suggestion:<span class="required">*</span></label>
                                <textarea class="form-control" rows="3" name="suggestionDescription" id="suggestionDescription"></textarea>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group  pull-right">
                                <input class="btn btn-primary" id="btnSubmitSuggestion" type="button" value="Submit" />
                            </div>
                        </div>
                </div>  
            </form>
      </div>
      <div class="modal-footer">
        
      </div>
    </div>
  </div>
</div>

@php
    use Illuminate\Http\Request;
    use App\Models\User;

    $userCompanies = User::find(auth()->user()->id);
    $userCompanies  = $userCompanies->companies;

@endphp
<div id="modalChangeCompany" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-body">
          <form role="form" id="frmChangeCompany" enctype="multipart/form-data" method="POST">
            {{ csrf_field() }}
            <div class="row">
                        <div class="row-header content-header">
                            <h3 class="title"> Company List </h3>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Select an available company</label>
                            </div>
                        </div>
                        <div class="col-sm-12">
                          <div class="form-group">
                            <select class="form-control" style="width: 100%;" id="txtChangeCompany" name="txtChangeCompany"
                                                tabindex="-1" aria-hidden="true">
                              @foreach($userCompanies as $company)
                                @if(isset($company->company_detail->company_name))
                                <option value="{{$company->company_id}}" @if($company->company_id == auth()->user()->company_id) selected @endif>{{$company->company_detail->company_name}}</option>
                                @endif
                              @endforeach
                          </select>
                        </div>
                      </div>

                        <div class="col-sm-12">
                            <div class="form-group  pull-right">
                                <input class="btn btn-primary" id="btnSubmitChangeCompany" type="button" value="Change" />
                            </div>
                        </div>
              </div>

                   
            </form>
      </div>
      <div class="modal-footer">
        
      </div>
    </div>
  </div>
</div>

<script>
    function showLoadingModal(msg, title = null)
    {
        if (title != null) {
          document.getElementById('modal-title-message').innerHTML = title
        }

        document.getElementById('modalLoadingMessage').innerHTML = msg;
        $('#modalLoading').modal({backdrop: 'static', keyboard: false}); 
        // $('#modalLoading').modal('show'); 
    }

    function closeLoadingModal()
    {
        $('#modalLoading').modal('hide');
    }

    function showSuggestionBox()
    {
        $('#modalSuggestion').modal('show'); 
    }

    function showLPFileFormatDialog()
    {
        $('#modalFileFormat').modal('show'); 
    }

    function showChangeCompany()
    {
        $('#modalChangeCompany').modal('show'); 
    }

</script>
<script type="text/javascript">
var APP_URL = {!! json_encode(url('/')) !!}
</script>
@include("incs.foot")

    <style type="text/css">

      .ui-autocomplete-loading {
          background:url("/images/loading.gif") no-repeat right center;
          background-size: 30px 30px;; 
        }
      .ui-autocomplete {
          position: absolute;
          z-index: 9999 !important;
          cursor: default;
          padding: 0;
          margin-top: -10px;
          list-style: none;
          background-color: #ffffff;
          border: 1px solid #ccc;
          -webkit-border-radius: 5px;
             -moz-border-radius: 5px;
                  border-radius: 5px;
          -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
             -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
                  box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
      }
      .ui-autocomplete > li {
        padding: 3px 20px;
      }
      .ui-autocomplete > li.ui-state-focus {
        background-color: #DDD;
      }
      .ui-helper-hidden-accessible {
        display: none;
      }

      .ui-autocomplete-category {
        font-weight: bold;
        padding: .2em .4em;
        margin: .8em 0 .2em;
        line-height: 1.5;
      }

    </style>

<script type="text/javascript">
  $(document).ready(function() {
      $.widget( "custom.catcomplete", $.ui.autocomplete, {
      _create: function() {
        this._super();
        this.widget().menu( "option", "items", "> :not(.ui-autocomplete-category)" );
      },
      _renderMenu: function( ul, items ) {
        var that = this,
          currentCategory = "";
        $.each( items, function( index, item ) {
          var li;
          if ( item.category != currentCategory ) {
            ul.append( "<li class='ui-autocomplete-category'><b>" + item.category + "</b></li>" );
            currentCategory = item.category;
          }
          li = that._renderItemData( ul, item );
          if ( item.category ) {
            li.attr( "aria-label", item.category + " : " + item.label );
          }
        });
      }
    });
    
    $("#generalSearch").catcomplete({
        delay: 0,
        source: function(request, response) {
        $.getJSON(APP_URL+"/extras/search", { term: request.term, type: $('#generalSearchType').val() }, response);
        },
        minLength: 1,
        select: function (e, ui) {
            window.location.href = ui.item.url;
        },
      });

  });


  /** Banner */
  $('.banner-close').on('click', function() { 
    let bannerId = $(this).data('banner_id')
    let request = $.ajax({
      url: `/admin/banners/${bannerId}/close`,
      method: "POST",
    })
  })
</script>

@yield("script")
</body>
</html>