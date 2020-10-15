<div>
  <div class="text-center">
    @php
      if ($pageViewsPrevious == 0) {
        $diffInPercentage = $pageViewsCurrent * 100;
      } else {
        $diffInPercentage = ($pageViewsCurrent - $pageViewsPrevious) / $pageViewsPrevious * 100;
      }
    @endphp

    <span><strong>{{ $pageViewsCurrent }}</strong></span>
    <span>{{ $labelCurrent }}</span>
    <span class="text-{{ $diffInPercentage > 0 ? 'green' : 'red' }}">
      @if ($diffInPercentage > 0)
        <i class="fa fa-arrow-up"></i>
      @else 
        <i class="fa fa-arrow-down"></i>
      @endif

      <span>{{ number_format($diffInPercentage, 2, '.', '') }}%</span>
    </span>
  </div>

  <div class="text-center">
    <span><strong>{{ $pageViewsPrevious }}</strong></span>
    <span>{{ $labelPrevious }}</span>
  </div>
</div>