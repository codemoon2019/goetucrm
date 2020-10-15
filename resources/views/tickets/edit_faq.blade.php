@extends('layouts.app')

@section('style')
  <style>
    .form-error {
      color: red;
      font-size: 0.85em;
      padding-top: 3px;
    }

    .overlay {
      position: fixed;
      width: 100%;
      height: 100%;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: rgba(0,0,0,0.1);
      z-index: 2000;

      display: none;
    }

    .overlay > div {
      margin: 0;
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
    }
  </style>
@endsection

@section('content')
  <div class="content-wrapper">
    {{------- Header -------}}
    <section class="content-header">
      <h1>
        <i class="fa fa-ticket mr-1"></i>
        <span>Ticketing Frequently Asked Questions</span>
      </h1>

      <div class="dotted-hr"></div>
    </section>

    {{------- Content -------}}
    <section class="content container-fluid">
      <div class="row px-4">
        <div class="col-md-12">
          <form id="form-edit-faq">
            <div class="form-group mb-4">
              <textarea class="form-control" rows="10" name="ticket_faq">{!! html_entity_decode($ticketFaq) !!}</textarea>
              <p id="form-error-ticket_faq" class="form-error hidden"></p>
            </div>
    
            <div class="form-group pull-right">
              <button class="btn btn-primary pull-right clickable" type="submit">Save FAQ</button>
            </div>
          </form>
        </div>
      </div>
    </section>
  </div>

  <div class="overlay">
    <div id="text">
      <img width="75px"   
        height="75px"
        src="https://ubisafe.org/images/transparent-gif-loading-5.gif"/>
    </div>
  </div>
@endsection

@section('script')
  <script>
    $(document).ready(function() {
      let ckEditor = CKEDITOR
      ckEditor.replace('ticket_faq', {
        toolbar : 'Basic',
        height: 400,
      })

      $('#form-edit-faq').on('submit', function(e) {
        e.preventDefault()
        $('.overlay').show()

        for (instance in CKEDITOR.instances)
          CKEDITOR.instances[instance].updateElement();

        let formData = new FormData(this);

        axiosCustom.post("{{ route('tickets.faq.save') }}", formData, {
          headers: {
            'Accepts': 'application/json'
          }
        }).then(() => {
            $('.form-error').addClass('hidden')

            swal.mixin({
              toast: true,
              position: 'top-end',
              showConfirmButton: false,
              timer: 3000
            }).fire({
              type: 'success',
              title: 'Ticket FAQ Saved'
            });
          })
          .finally(() => {
            $('.overlay').hide()
          })
      })
    })
  </script>
@endsection