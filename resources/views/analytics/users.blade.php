@extends('layouts.app')

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
      @component('analytics.components.analyticsNavigation', [
        'url' => route('analytics.users.index'),
        'filter' => request()->filter ?? 0
      ]) 
      @endcomponent

      <div class="row mx-4">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>Type</th>
              <th>Name</th>
              <th>Page Visits</th>
              <th>Time Spent</th>
            </tr>
          </thead>

          <tbody>
            @forelse ($users as $user)
              <tr>
                <td>
                    {{ $user->department_names }}
                </td>
                <td>
                  <a href="{{ route('analytics.users.show', $user->id) }}">{{ $user->full_name }}</a>
                </td>
                <td>{{ count($user->analytics) }}</td>
                <td>{{ gmdate('H:i:s', $user->analytics()->sum('time_spent')) }}</td>
              </tr>
            @empty
            @endforelse
          </tbody>
        </table>
      </div>
    </section>
  </div>
@endsection

@section('script')
  <script>
    $('table').dataTable();
  </script>
@endsection