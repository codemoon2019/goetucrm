@extends('mails.tickets.layout')

@section('mailContent')
  <p class="color-disable">## - Please type your reply above this line - ##</p>
  <p>Your request has been received and is being reviewed by your support staff.</p>
  <p>To add additional comments, reply to this email.</p>

  <hr>

  <table>
    <tr>
      <td>
        <h3 style="display: flex">
          <img src="{{ url('/') . $ticketHeader->createdBy->image }}" height="35px" width="35px" style="border: 1px solid black; border-radius: 50%" />
          @if ($ticketHeader->createdBy == $ticketHeader->requester || !isset($ticketHeader->requester))
            <span style="margin-left: 10px; align-self: center">{{ $ticketHeader->createdBy->full_name }} created a ticket</span>
          @else
            <span style="margin-left: 10px; align-self: center">{{ $ticketHeader->createdBy->full_name }} created a ticket for {{ $ticketHeader->requester->full_name }}</span>
          @endif
        </h3>
      </td>
    </tr>

    <tr><td>&nbsp;</td></tr>

    <tr>
      <td>
        <strong>Message:</strong>
      </td>
    </tr>

    <tr>
      <td>
        {!! html_entity_decode($ticketHeader->description) !!}
      </td>
    </tr>

    <tr><td>&nbsp;</td></tr>

    <tr>
      <td>
        <strong>Created at:</strong>
      </td>
    </tr>

    <tr>
      <td>
        {{ $ticketHeader->created_at }}
      </td>
    </tr>
  </table>
@endsection