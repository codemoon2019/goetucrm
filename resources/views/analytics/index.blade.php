@extends('layouts.app')

@section('style')
  <style>
    .section-page-views-summary {
      display: flex;
      justify-content: space-between;
    }

    .section-page-views-summary > div {
      border: 1px solid black;
      box-sizing: border-box;
      height: 135px;
      width: 32%;

      display: flex;
      justify-content: space-around;
    }

    .section-page-views-summary > div > div {
      height: 135px;
      width: 100%;
      position: relative;

      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    .section-page-views-summary > div > div:nth-child(1) {
      border-right: 1px solid black;
    }

    .section-page-views-summary > div > div > span:nth-child(1) {
      font-size: 2.5em;
      line-height: 1.2
    }

    .section-page-views-summary > div > div > span:nth-child(3) {
      position: absolute;
      right: 5px;
      top: 5px;
    }
  </style>
@endsection

@section('content')
  <div class="content-wrapper">
    <section class="content-header">
      <h1>Analytics</h1>
      
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('analytics.index') }}">Analytics</a></li>
      </ol>

      <div class="dotted-hr"></div>
    </section>

    <section class="content container-fluid">
      @component('analytics.components.analyticsNavigation', [
        'url' => route('analytics.index'),
        'filter' => request()->filter ?? 0
      ]) 
      @endcomponent

      <!--Page Views Summary-->
      <div class="row mx-4">
        <div class="col-sm-12">
          <h4 class="mb-1"><strong>Page Visits Summary</strong></h4>
          <p class="mb-2"><strong>{{ $analyticsSummary->getTotalPageViews() }}</strong> total page visits</p>
        </div>

        <div class="section-page-views-summary col-sm-12">
          @php $pageViews = $analyticsSummary->getPageVisitsSummary('day') @endphp
          @component('analytics.components.summaryItem', [
            'pageViewsCurrent' => $pageViews['currentPeriod'],
            'pageViewsPrevious' => $pageViews['previousPeriod'], 
            'labelCurrent' => 'Today',
            'labelPrevious' => 'Yesterday',
          ]) 
          @endcomponent

          @php $pageViews = $analyticsSummary->getPageVisitsSummary('week') @endphp
          @component('analytics.components.summaryItem', [
            'pageViewsCurrent' => $pageViews['currentPeriod'],
            'pageViewsPrevious' => $pageViews['previousPeriod'], 
            'labelCurrent' => 'Last 7 Days',
            'labelPrevious' => 'Previous Period',
          ]) 
          @endcomponent

          @php $pageViews = $analyticsSummary->getPageVisitsSummary('month') @endphp
          @component('analytics.components.summaryItem', [
            'pageViewsCurrent' => $pageViews['currentPeriod'],
            'pageViewsPrevious' => $pageViews['previousPeriod'], 
            'labelCurrent' => 'Last 30 Days',
            'labelPrevious' => 'Previous Period',
          ]) 
          @endcomponent
        </div>

        <div class="col-sm-12 mt-4">
          <div class="p-4" style="border: 1px solid black;">
            <div id="section-page-views-chart" style="min-height: 250px"></div> <!--@todo To be changed-->
          </div>
        </div>
      </div><!--/Page Views Summary-->

      <div class="row mx-4">
        <div class="col-sm-12">
          <hr />
        </div>
      </div>

      <div class="row mx-4">
        <!--Most Active Users-->
        <div class="col-sm-6">
          <h4 class="mb-2"><strong>Most Active Users</strong></h4>
          <table class="table table-striped mb-1">
            <thead>
              <tr>
                <th>Type</th>
                <th>Name</th>
                <th>Page Visits</th>
                <th>Time Spent</th>
              </tr>
            </thead>

            <tbody>
              @forelse ($analyticsSummary->getMostActiveUsers() as $user)
                <tr>
                  <td>
                      {{ $user->department_names }}
                  </td>
                  <td>
                    <a href="{{ route('analytics.users.show', $user->id) }}">{{ $user->full_name }}</a>
                  </td>
                  <td>{{ $user->total_page_visits }}</td>
                  <td>{{ gmdate("H:i:s", $user->total_time_spent) }}</td>
                </tr>
              @empty
                <tr><td colspan="4" class="text-center">No Data Available</td></tr>
              @endforelse
            </tbody>
          </table>

          <a href="{{ route('analytics.users.index') }}"> 
            <p><i><small>For a more comprehensive and complete list of users, please click here</small></i></p>
          </a>
        </div><!--/Most Active Users-->

        <div class="col-sm-6">
          <h4 class="mb-2"><strong>Most Visited Pages</strong></h4>
          <table class="table table-striped mb-1">
            <thead>
              <tr>
                <th>URL</th>
                <th>Visits</th>
                <th>Avg Time Spent</th>
              </tr>
            </thead>

            <tbody>
              @forelse ($analyticsSummary->getMostVisitedPages() as $page)
                <tr>
                  <td>{{ $page->url }}</td>
                  <td>{{ $page->total_page_visits }}</td>
                  <td>{{ gmdate("H:i:s", $page->total_time_spent / $page->total_page_visits) }}</td>
                </tr>
              @empty
                <tr><td colspan="4" class="text-center">No Data Available</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div><!--/Page Views Chart-->
    </section>
  </div>
@endsection

@section('script')
  <script>
    $(document).ready(function() {
      var chart = new CanvasJS.Chart("section-page-views-chart", {
        animationEnabled: true,
        theme: "light2",
        title:{
          text: "Page Visits ({{ $analyticsLineChart->startDate->format('F Y') }} - {{ $analyticsLineChart->endDate->format('F Y') }})"
        },
        axisX: {
          title: 'Months'
        },
        axisY:{
          includeZero: false
        },
        data: [{        
          type: "line",       
          dataPoints: @json($analyticsLineChart->getChartData())
        }]
      });

      chart.render();
    })
  </script>
@endsection