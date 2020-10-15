<div class="recent-activities p-0">
  <div class="container p-4">
    <h4>
      <i class="fa fa-angle-right btn-hide-recent-activities clickable mr-2"></i>
      <strong>Recent Activities</strong>
    </h4>
    
    <hr>

    @foreach ($recentActivities as $activity)
      <div class="activity mb-2">
        <img src="{{ $activity->createdBy->image }}"
          width="25px"
          height="25px"
          class="mr-2">

        <span>
          <span>{{ $activity->createdBy->full_name }}</span>
          @php $raTicketHeader = $activity->ticketHeader; @endphp
          @php $raSubtask = $raTicketHeader->subtask; @endphp

          @switch($activity->main_action)
            @case('commented')
              <span class="badge badge-secondary">commented</span> 
              <span>on </span>
              <a href='{{ url("/tickets/{$raTicketHeader->id}/edit") }}' target="_blank">
                subtask #{{ $raSubtask->task_no }}.
              </a>
            @break
            
            @case('started progress')
              <span class="badge status-in-progress">started progress</span> 
              <span>on </span>
              <a href='{{ url("/tickets/{$raTicketHeader->id}/edit") }}' target="_blank">
                subtask #{{ $raSubtask->task_no }}.
              </a>
            @break

            @case('pending')
              <span>mark</span>
              <a href='{{ url("/tickets/{$raTicketHeader->id}/edit") }}' target="_blank">
                subtask #{{ $raSubtask->task_no }}
              </a>
              <span>as</span>
              <span class="badge status-pending">pending.</span> 
            @break

            @case('solved')
              <span class="badge status-closed">solved</span> 
              <a href='{{ url("/tickets/{$raTicketHeader->id}/edit") }}' target="_blank">
                subtask #{{ $raSubtask->task_no }}.
              </a>
            @break
          @endswitch

          <span class="text-muted ml-2">{{ $raSubtask->created_at->diffForHumans(null, false, true) }}</span>
        </span>
      </div>
    @endforeach
  </div>
</div>