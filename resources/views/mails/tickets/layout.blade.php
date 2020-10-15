<!doctype html>
<html>

<head>
    <title>GoETU Ticket</title>
    <style type="text/css">
        .body {
            padding: 20px;
            margin: 0;
            font-size: 14px;
            color: #272727;
        }

        .color-disable {
            color: #a1a1a1;
        }

        hr {
            border-top: dotted 1px;
            margin: 25px 0 15px 0;
        }

        .dp {
            border-radius: 5px;
            background-color: #a1a1a1;
            min-height: 50px;
            min-width: 50px;
        }

        table {
            font-size: 15px;
            margin-top: 20px;
        }

        .highlight {
            font-weight: bold;
            color: #000;
            margin-right: 10px;
        }

        .space {
            padding-left: 10px;
        }

        .message {
            padding: 5px 0 15px 65px;
            font-size: 16px;
        }
    </style>
</head>

<body class="body">
    <div class="inside-content">
        @yield('mailContent')
    </div>

    <hr>

    <p class="color-disable" style="font-size: 12px;"></p>
    
    <table>
        <tr>
            <td class="highlight">Ticket #</td>
            <td class="color-disable space">{{ $ticketHeader->id }} </td>
        </tr>
        <tr>
            <td class="highlight">Status</td>
            <td class="color-disable space">{{ $ticketHeader->ticketStatus->description }}</td>
        </tr>
        <tr>
            <td class="highlight">Requester</td>
            <td class="color-disable space">{{ optional($ticketHeader->requester)->full_name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="highlight">CCs</td>
            <td class="color-disable space">
                @foreach ($ticketHeader->ccs as $cc) 
                    {{ $cc->full_name }} <br>
                @endforeach
            </td>
        </tr>
        <tr>
            <td class="highlight">Group</td>
            <td class="color-disable space">{{ optional($ticketHeader->userType)->description ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="highlight">Assignee</td>
            <td class="color-disable space">{{ optional($ticketHeader->assignedTo)->full_name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="highlight">Priority</td>
            <td class="color-disable space">{{ $ticketHeader->ticketPriority->description }}</td>
        </tr>
        <tr>
            <td class="highlight">Type</td>
            <td class="color-disable space">{{ $ticketHeader->ticketType->description }}</td>
        </tr>
    </table>

    <table class="email-footer" align="left" width="570" cellpadding="0" cellspacing="0">
        <tr>
            <td class="content-cell" align="left">
                <p class="sub align-left">
                    <b>Powered by</b>
                    <a href="{{ url('/') }}">GoETU Infotech Solutions</a>
                </p>
            </td>
        </tr>
    </table>
</body>

</html>