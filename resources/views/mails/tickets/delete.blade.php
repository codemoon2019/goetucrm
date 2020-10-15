@extends('mails.tickets.layout')

@section('mailContent')
  <table>
    <tr>
      <td>
        <h3>Ticket #{{ $ticketHeader->id }} has been deleted</h3>
      </td>
    </tr>

    <tr><td>&nbsp;</td></tr>

    <tr>
      <td>
        <strong>Deleted By:</strong>
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
        <strong>Deleted at:</strong>
      </td>
    </tr>

    <tr>
      <td>
        {{ $ticketHeader->updated_at }}
      </td>
    </tr>
  </table>
@endsection