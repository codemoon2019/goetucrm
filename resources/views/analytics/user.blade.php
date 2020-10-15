@extends('layouts.app')

@section('style')
  <style>
    .dataTables_wrapper > div:nth-child(1) {
      display: none;
    }

    .user-image > img {
      border: 1px solid black;
      border-radius: 50%;
    }

    .user-details {
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    .user-analytics-summary {
      display: flex;
      justify-content: space-around;
    }

    .user-analytics-summary > div {
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    .user-analytics-summary-2 {
      display: flex;
      flex-direction: column;
      align-items: center;
    }
  </style>
@endsection

@section('content')
  <div class="content-wrapper">
    <section class="content-header">
      <h1>Analytics</h1>
      
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('analytics.index') }}">Analytics</a></li>
        <li class="breadcrumb-item"><a href="{{ route('analytics.users.index') }}">Users</a></li>
      </ol>

      <div class="dotted-hr"></div>
    </section>

    <section class="content container-fluid">
      <div class="row">
        <div class="col-sm-8 pl-4 pr-2">
          <div class="box box-info">
              <div class="box-header with-border">
                <h3 class="box-title">{{ $user->full_name }} Activity</h3>
              </div><!-- /.box-header -->
      
              <div class="box-body">
                <div class="table-responsive p-2">
                  <table class="table table-striped">
                    <thead>
                      <tr>
                        <th>URL</th>
                        <th>Time Visited</th>
                        <th>Time Spent</th>
                        <th class="none">IP Address</th>
                        <th class="none">Device</th>
                        <th class="none">Platform</th>
                        <th class="none">Browser</th>
                      </tr>
                    </thead>
      
                    <tbody>
                      @forelse ($user->analytics as $analytics)
                        <tr>
                          <td>{{ $analytics->url }}</td>
                          <td>{{ $analytics->created_at->format('F d, Y h:i a') }}</td>
                          <td>{{ gmdate('H:i:s', $analytics->time_spent) }}</td>
                          <td>{{ $analytics->ip_address }}</td>
                          <td>{{ $analytics->device }}</td>
                          <td>{{ $analytics->platform }}</td>
                          <td>{{ $analytics->browser }}</td>
                        </tr>
                      @empty
                        <tr><td colspan="6">No Data Available</td></tr>
                      @endforelse
                    </tbody>
                  </table>
                </div><!-- /.table-responsive -->
              </div><!-- /.box-body -->
            </div>
        </div>

        <div class="col-sm-4 pr-4 pl-2">
          <div class="box box-info">
            <div class="box-body with-border">
              <div class="user-image text-center mt-4">
                <img height="100px" width="100px" src="{{ $user->image }}">
              </div>
  
              <div class="user-details my-4">
                <span class="h4"><strong>{{ $user->full_name }}</strong></span>
                <span class="h5">{{ $user->partnerCompany->company_name ?? '' }}</span>
                <span>{{ $user->department_names }}</span>
              </div>
  
              <div class="user-analytics-summary mb-4">
                <div>
                  <span class="h4"><strong>{{ count($user->analytics) }}</strong></span>
                  <span>Page Visits</span>
                </div>
  
                <div>
                  <span class="h4"><strong>{{ gmdate('H:i:s', $userAnalytics->getTotalTimeSpent()) }}</strong></span>
                  <span>Time Spent</span>
                </div>
  
                <div>
                  <span class="h4"><strong>{{ $userAnalytics->getTotalDaysVisited() }}</strong></span>
                  <span>Days Visited</span>
                </div>
              </div>
  
              @if ($user->enhanced_is_online)
                <div class="d-flex flex-column text-center mb-4 text-green">
                  <span><strong>Now Visiting:</strong></span>
                  <span>{{ $user->analytics()->orderByDesc('created_at')->first()->url }}<span>
                </div>
              @else
                <div class="text-center mb-4">
                  <span>Last Visited</span>&nbsp;
                  <strong>{{ $user->last_activity->format('F d, Y') }}</strong>
                </div>
              @endif
  
              <div class="user-analytics-summary-2 mb-4">
                <span class="mb-2">Commonly used technologies</span>
                <span><strong>Device:</strong> <!--icon-->{{ $userAnalytics->getMostUsedDevice() }}</span>
                <span><strong>Platform:</strong> <!--icon--> {{ $userAnalytics->getMostUsedPlatform() }}</span>
                <span><strong>Browser:</strong> <!--icon--> {{ $userAnalytics->getMostUsedBrowser() }}</span>
              </div>
            </div><!-- /.box-body -->
          </div>
        </div>
      </div>
    </section>
  </div>
@endsection

@section('script')
  <script>
    $('table').dataTable({
      responsive: true,
      pageLength: 25,
      "bLengthChange": false,
      "bFilter": false,
      aaSorting: [],
    })
  </script>
@endsection