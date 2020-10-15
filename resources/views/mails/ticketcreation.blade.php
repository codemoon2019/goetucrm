<!DOCTYPE HTML>
<html>
<head>
<title>Goetu Ticket</title>
<style type="text/css">
  .body{padding: 20px;margin:0;font-size: 14px;color: #272727;}
  .color-disable{color:#a1a1a1;}
  hr{ border-top: dotted 1px; margin:25px 0 15px 0;  }
  .inside-content{ padding-left: 20px; }
  .dp{ border-radius: 5px;  background-color:#a1a1a1; min-height: 50px; min-width: 50px; }
  table{font-size: 15px; margin-top: 20px;}
  .highlight{font-weight: bold; color: #000; margin-right: 10px;}
  .space{padding-left: 10px;}
  .message{padding: 5px 0 15px 65px; font-size: 16px;}
</style>
</head>
<body class="body">
  <p class="color-disable">## - Please type your reply above this line - ##</p>
  <p>Your request has been received and is being reviewed by your support staff.</p>
  <p>To add additional comments, reply to this email.</p>
  <hr>
  <div class="inside-content">
    <table>
      <tr>
        <td>
          <div class="dp">
            <img width="50" height="50" style="border-radius: 5px;" />
          </div>
        </td>
        <td>
          <h3 class="space" style="display: initial;">{{$first_name}} {{$last_name}} 
            @if($care_of!="") 
              <span style="font-size: 12px;" class="color-disable space">via {{$care_of}}</span> 
            @endif
          </h3><br>
          <p class="color-disable" style="margin: 5px 5px 5px 10px; border-bottom: dotted 2px;line-height: 20px;">{{$datetime}}</p>
        </td>
      </tr>
    </table>
    <div class="message">
      <p>{!! html_entity_decode($message) !!}</p>
    </div>
  </div>
  <hr>
  <p class="color-disable" style="font-size: 12px;"></p>
  <table>
    
    <tr>
      <td class="highlight">Ticket #</td>
      <td class="color-disable space">{{$id}}</td>
    </tr>
    <tr>
      <td class="highlight">Status</td>
      <td class="color-disable space">{{$status}}</td>
    </tr>
    <tr>
      <td class="highlight">Requester</td>
      <td class="color-disable space">{{$requester}}</td>
    </tr>
    <tr>
      <td class="highlight">CCs</td>
      <td class="color-disable space">
        @if (count($ccs) > 0)
          @foreach($ccs as $cc)
            {{$cc}} <br>
          @endforeach
        @endif
      </td>
    </tr>
    <tr>
      <td class="highlight">Group</td>
      <td class="color-disable space">{{$group}}</td>
    </tr>
    <tr>
      <td class="highlight">Assignee</td>
      <td class="color-disable space">{{$assignee}}</td>
    </tr>
    <tr>
      <td class="highlight">Priority</td>
      <td class="color-disable space">{{$priority}}</td>
    </tr>
    <tr>
      <td class="highlight">Type</td>
      <td class="color-disable space">{{$type}}</td>
    </tr>
  </table>
  <table class="email-footer" align="left" width="570" cellpadding="0" cellspacing="0">
    <tr>
    <td class="content-cell" align="left">
      <p class="sub align-left"><b>Powered by </b><a href="{{ url('/') }}">GoETU Infotech Solutions</a></p>
      <p class="sub align-center">
      </p>
    </td>
    </tr>
  </table>
</body>
</html>