<div class="content-wrapper callout-wrapper" style="position: relative; background: rgba(210, 214, 222, 0.65)">
  @php 
    $banners = auth()->user()->banners;
  @endphp

  @foreach ($banners as $banner)
    @php
      switch ($banner->type) {
        case App\Models\Banner::TYPE_ERROR:
          $color = 'danger';
          $icon = 'ban';
          break;
        
        case App\Models\Banner::TYPE_INFORMATION:
          $color = 'info';
          $icon = 'info';
          break;
        
        case App\Models\Banner::TYPE_WARNING:
          $color = 'warning';
          $icon = 'check';
          break;
      }
    @endphp

    <div class="alert alert-{{ $color }} alert-dismissible mt-0 mx-1" style="margin-bottom: 6px">
      <button type="button" class="close banner-close" data-dismiss="alert" data-banner_id="{{ $banner->id }}" aria-hidden="true">&times;</button>
      <h4 class="mb-2">
        <i class="icon fa fa-{{ $icon }}"></i>
        <span>{{ $banner->title }}</span>
      </h4>
      <span>{{ $banner->message }}</span>
    </div>
  @endforeach 
</div>