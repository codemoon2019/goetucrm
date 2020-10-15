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
          <img src="{{ url('/') . $ticketHeader->updatedBy->image }}" height="35px" width="35px" style="border: 1px solid black; border-radius: 50%" />
          <span style="margin-left: 10px; align-self: center">{{ $ticketHeader->updatedBy->full_name }} replied to Ticket # {{ $ticketHeader->id }}</span>
        </h3>
      </td>
    </tr>
  </table>

  <hr style="margin-top: 5px;">

  <table>
    <tr><td>&nbsp;</td></tr>

    <tr>
      <td>
        <strong>Latest Replies:</strong>
      </td>
    </tr>

    <tr><td>&nbsp;</td></tr>

    @foreach($ticketHeader->ticketDetails->sortByDesc('id')->take(5) as $ticketDetail)
      <tr>
        <td>
          <h3 style="display: flex">
            <img src="{{ url('/') . $ticketDetail->createdBy->image }}" height="35px" width="35px" style="border: 1px solid black; border-radius: 50%" />
            <span style="margin-left: 10px; align-self: center">
              <span>{{ $ticketDetail->createdBy->full_name }}</span>
              <span style="color: gray"><i>replied at {{ $ticketDetail->created_at }}</i></span>
            </span>
          </h3>
        </td>
      </tr>

      <tr style="padding-bottom: 10px;">
        <td>
          {!! html_entity_decode($ticketDetail->message) !!}
        </td>
      </tr>
    @endforeach
  </table>
@endsection