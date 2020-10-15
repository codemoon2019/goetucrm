@extends('layouts.app')

@section('style')
  <style>
    .form-checkbox { transform: translateY(3px) }
    .table > tbody > tr > td { vertical-align: middle; } 
  </style>
@endsection

@section('content')
  <div class="content-wrapper">
    <section class="content-header">
      <h1>Announcements</h1>
      
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/">Admin</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.banners.index') }}">Announcement</a></li>
      </ol>

      <div class="dotted-hr"></div>
    </section>

    <section class="content container-fluid">
      <div class="row px-4">
        @component('admin.banners.components.smallBox', ['count' => $bannerTotalCount, 'label' => 'Total']) @endcomponent
        @component('admin.banners.components.smallBox', ['count' => $bannerShowingCount, 'label' => 'Showing']) @endcomponent
        @component('admin.banners.components.smallBox', ['count' => $bannerUpcomingCount, 'label' => 'Upcoming']) @endcomponent
        @component('admin.banners.components.smallBox', ['count' => $bannerEndedCount, 'label' => 'Ended']) @endcomponent

        <div class="col-lg-12 my-3">
          <div class="dotted-hr"></div>
        </div>
      </div>

      <form id="form-banners-delete" action="{{ route('admin.banners.destroyMany') }}" method="POST">
        @csrf
        @method('DELETE')

        <div class="row px-4 mb-3">
          <div class="col-lg-12 d-flex flex-row-reverse">
            @hasAccess('announcement', 'delete')
              <button id="btn-delete" type="submit" class="btn btn-sm btn-danger clickable">
                <i class="fa fa-trash"></i>&nbsp;
                <span>Delete Announcement/s</span>
              </button>&nbsp;&nbsp;
            @endhasAccess

            @hasAccess('announcement', 'create')
              <a href="{{ route('admin.banners.create') }}"  class="btn btn-sm btn-primary">
                <i class="fa fa-plus"></i>&nbsp;
                <span>Create Announcement</span>
              </a>
            @endhasAccess
          </div>
        </div>

        <div class="row px-2">
          <div class="col-lg-12">
            <table id="table-banner" class="table table-striped">
              <thead>
                <tr>
                  <th style="display:none"></th>
                  <th data-orderable="false" class="text-center"></th>
                  <th>Title</th>
                  <th class="text-center">Type</th>
                  <th class="text-center">State</th>
                  <th>Starts at</th>
                  <th class="pr-3">Ends at</th>
                </tr>
              </thead>
              
              <tbody>
                @foreach ($banners as $banner)
                  <tr>
                    <td style="display: none;"></td>
                    <td class="text-center">
                      <label>
                        <input class="form-checkbox flat-red"
                          type="checkbox" 
                          name="banners[]"
                          value="{{ $banner->id }}">
                      </label>
                    </td>
                    <td><a href="{{ route('admin.banners.edit', $banner->id) }}">{{ $banner->title }}</td>
                    <td class="text-center">
                      @switch ($banner->type)
                        @case (App\Models\Banner::TYPE_ERROR)
                          <span class="text-danger fa fa-times-circle"></span>
                          @break

                        @case (App\Models\Banner::TYPE_INFORMATION)
                          <span class="text-primary fa fa-info-circle"></span>
                          @break

                        @case (App\Models\Banner::TYPE_WARNING)
                          <span class="text-warning fa fa-exclamation-triangle"></span>
                          @break
                      @endswitch

                      <span>{{ $banner->type_readable }}</span>
                    </td>
                    <td class="text-center">{{ $banner->state }}</td>
                    <td>{{ $banner->starts_at->format('F d, Y h:i a') }}</td>
                    <td class="pr-3">{{ $banner->ends_at->format('F d, Y h:i a') }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </form>
    </section>
  </div>
@endsection

@section('script')
  <script src=@cdn('/js/admin/banners/index.js')></script>
@endsection