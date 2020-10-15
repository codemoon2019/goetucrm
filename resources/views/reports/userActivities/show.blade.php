@extends('layouts.app')

@section('content')
  <div class="content-wrapper">
    <section class="content-header">
      <h1>User Activity Reports</h1>
      
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('billing/reports') }}">Reports</a></li>
        <li class="breadcrumb-item"><a href="{{ route('reports.userActivities.index') }}">User Activity</a></li>
        <li class="breadcrumb-item">Report</li>
      </ol>

      <div class="dotted-hr"></div>
    </section>

    <section class="content container-fluid py-4">
      @if ($report['group_description'] !== null)
        <h1 class="text-center mb-4"> {{ $report['group_description'] }}'s Activity Report</h1>
      @endif

      @foreach ($report['users'] as $userReport)
        <div class="col-sm-12 px-4">
          <h3 class="mb-0 text-center"><strong>{{ $userReport['user'] }}</strong>'s Activity Report</h3>
        </div>

        @if (isset($userReport['table']['rows']))
          <div class="col-sm-12 mt-4 px-4">
            <h4 class="mb-2">{{ $userReport['table']['label'] }}</h4>
            <table class="table table-striped mb-4" style="width: 100%">
              <thead>
                <tr>
                  @foreach ($userReport['columns'] as $column)
                    <th class="text-center">{{ $column }}</th>
                  @endforeach
                </tr>
              </thead>

              <tbody>
                @foreach ($userReport['table']['rows'] as $row)
                  <tr>
                    @foreach ($row as $data)
                      <td class="text-center">{{ $data }}</td>
                    @endforeach
                  </tr>
                @endforeach

                <tr><td colspan="4">&nbsp;</td></tr>
                <tr>
                  <td class="text-right"><strong>Total</strong></td>
                  <td class="text-center">{{ $userReport['table']['total']['number_of_login'] }}</td>
                  <td class="text-center">{{ $userReport['table']['total']['page_visits'] }}</td>
                  <td class="text-center">{{ $userReport['table']['total']['time_spent'] }}</td>
                </tr>
              </tbody>
            </table>

            <div class="row mb-4">
              <div class="col-lg-12">
                <h5>Grand Total</h5>
              </div>

              <div class="col-lg-4">
                <div class="small-box bg-navy" data-filter='A' data-status='A'>
                  <div class="inner">
                    <h3>{{ $userReport['grandTotal']['number_of_login'] }}</h3>
                    <p>Number of Logins</p>
                  </div>
                </div>
              </div>

              <div class="col-lg-4">
                <div class="small-box bg-navy" data-filter='A' data-status='A'>
                  <div class="inner">
                    <h3>{{ $userReport['grandTotal']['page_visits'] }}</h3>
                    <p>Page Visits</p>
                  </div>
                </div>
              </div>

              <div class="col-lg-4">
                <div class="small-box bg-navy" data-filter='A' data-status='A'>
                  <div class="inner">
                    <h3>{{ $userReport['grandTotal']['time_spent'] }}</h3>
                    <p>Time Spent</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        @else
            <p class="text-center my-4">No Data Available from {{ $userReport['table']['label'] }}</p>
        @endif
      @endforeach
    </section>
  </div>
@endsection