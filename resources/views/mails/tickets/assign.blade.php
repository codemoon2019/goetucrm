@extends('mails.tickets.layout')

@section('mailContent')
  <p class="color-disable">## - Please type your reply above this line - ##</p>
  <p>Your request has been received and is being reviewed by your support staff.</p>
  <p>To add additional comments, reply to this email.</p>

  <hr>

  <table>
    <tr>
      <td>
        <strong>Ticket #{{ $ticketHeader->id }} is assigned to:</strong>
      </td>
    </tr>

    <tr>
      <td style="padding-top: 10px;">
        @if ($ticketHeader->assignee == -1)
          <span>{{ $ticketHeader->userType->description }} Department</span>
        @else
          <h3 style="display: flex">
            <img src="{{ url('/') . $ticketHeader->assignedTo->image }}" height="35px" width="35px" style="border: 1px solid black; border-radius: 50%" />
            <span style="margin-left: 10px; align-self: center">{{ $ticketHeader->assignedTo->full_name }} of {{ $ticketHeader->userType->description }} Department</span>
          </h3>
        @endif 
      </td>
    </tr>

    <tr><td>&nbsp;</td></tr>

    <tr>
      <td>
        <strong>Assigned By:</strong>
      </td>
    </tr>

    <tr>
      <td style="padding-top: 10px;">
        <h3 style="display: flex">
          <img src="{{ url('/') . $ticketHeader->updatedBy->image }}" height="35px" width="35px" style="border: 1px solid black; border-radius: 50%" />
          <span style="margin-left: 10px; align-self: center">{{ $ticketHeader->updatedBy->full_name }}</span>
        </h3>
      </td>
    </tr>

    <tr><td>&nbsp;</td></tr>

    <tr>
      <td>
        <strong>Assigned at:</strong>
      </td>
    </tr>

    <tr>
      <td>
        {{ $ticketHeader->updated_at }}
      </td>
    </tr>
  </table>
@endsection