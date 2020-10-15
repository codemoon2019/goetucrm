@extends('layouts.app')

@section('content')
  <div class="content-wrapper">
    {{------- Header -------}}
    <section class="content-header">
      <h1 class="d-flex align-items-center">
        <i class="fa fa-ticket mr-2"></i>
        <span class="mr-3">Ticketing Frequently Asked Questions</span>

        @hasAccess ('ticketing', 'edit ticket faq')
          <a class="btn btn-sm btn-primary" href="{{ route('tickets.faq.edit') }}" role="button">
            Edit FAQ
          </a>
        @endhasAccess
      </h1>

      <div class="dotted-hr"></div>
    </section>

   {{------- Content -------}}
   <section class="content container-fluid">
      <div class="row px-4">
        <div class="col-md-12">
          {!! html_entity_decode($ticketFaq) !!}
        </div>
      </div>
    </section>
  </div>
@endsection