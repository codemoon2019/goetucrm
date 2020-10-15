@extends('layouts.app')

@section('style')
  <link rel="stylesheet" 
    type="text/css" 
    href="https://adminlte.io/themes/AdminLTE/bower_components/select2/dist/css/select2.min.css" />
@endsection

@section('content')
  <div class="content-wrapper">
    <section class="content-header">
      <h1>API Keys</h1>
      
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('developers.apiKeys.index') }}">API Keys</a></li>
      </ol>

      <div class="dotted-hr"></div>
    </section>

    <section class="content container-fluid">
      <div class="row px-2">
        @php $hasSuperAdminAccess = false; @endphp
        @hasAccess('admin', 'super admin access')
          @php $hasSuperAdminAccess = true; @endphp
        @endhasAccess

        @hasAccess('developers', 'view api keys')
          <div class="{{ $hasSuperAdminAccess ? 'col-lg-8' : 'col-lg-6' }}">
            <div class="box box-info pb-2">
              <div class="box-header with-border">
                <h3 class="box-title">{{ $hasSuperAdminAccess ? 'API Keys' : 'My API Keys'}}</h3>
              </div>

              <div class="box-body">
                <div class="table-responsive p-2">
                  <table class="table table-striped">
                    <thead>
                      <tr>
                        @if ($hasSuperAdminAccess) <th>Owner</th> @endif
                        <th>Project Name</th>
                        <th>API Key</th>
                        <th class="none">Note</th>
                      </tr>
                    </thead>
      
                    <tbody>
                      @forelse ($apiKeys as $apiKey)
                        <tr>
                          @if ($hasSuperAdminAccess) <td>{{ $apiKey->user->full_name}}</td> @endif
                          <td>{{ $apiKey->project_name }}</td>
                          <td>{{ $apiKey->key }}</td>
                          <td>{{ $apiKey->note ?? 'N/A' }}</td>
                        </tr>
                      @empty
                      @endforelse
                    </tbody>
                  </table>
                </div><!-- /.table-responsive -->
              </div>
            </div>
          </div>
        @endhasAccess

        @hasAccess('developers', 'create api keys')
          <div class="{{ $hasSuperAdminAccess ? 'col-lg-4' : 'col-lg-6' }}">
            <div class="box box-info">
              <div class="box-header with-border">
                <h3 class="box-title">Create new API Key</h3>
              </div>
              <div class="box-body px-4">
                <form action="{{ route('developers.apiKeys.store') }}" method="POST">
                  @csrf

                  @hasAccess('admin', 'super admin access')
                    <div class="form-group">
                      <label>Partner/Agent</label>
                      <select class="form-control js-example-basic-single" 
                        name="user_id"
                        style="width: 100%">
                        @foreach ($userGroups  as $users)
                          @if ($company = $users->first()->partnerCompany)
                            <optgroup label="{{ $company->company_name }}">
                          @else
                            <optgroup label="No Company">
                          @endif
                              @foreach ($users->sortBy('full_name') as $user)
                                <option value="{{ $user->id }}">
                                  {{ $user->full_name }}
                                </option>
                              @endforeach
                            </optgroup>
                        @endforeach
                      </select>
                    </div>
                  @endhasAccess

                  <div class="form-group">
                    <label>Project Name</label>
                    <input class="form-control"
                      type="text" 
                      name="project_name" 
                      placeholder="Enter title here..."
                      max="20"
                      value="{{ old('project_name') }}">
                  </div>

                  <div class="form-group">
                    <label>API Key</label>
                    <input class="form-control"
                      type="text" 
                      name="key" 
                      readonly
                      value="{{ $key }}">
                  </div>
      
                  <div class="form-group">
                    <label>Note</label>
                    <textarea class="form-control"
                      name="note"
                      placeholder="Enter message here..."
                      rows="3"
                      value="{{ old('message') }}"></textarea>
                  </div>

                  <div class="form-group">
                    <button type="submit" class="btn btn-primary">Save</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        @endhasAccess
      </div>
    </section>
  </div>
@endsection

@section('script')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
  <script>
    $('.js-example-basic-single').select2();
    $('table').dataTable({
      responsive: true,
      pageLength: 25,
      "bLengthChange": false,
      "bFilter": false,
      aaSorting: [],
    })
  </script>
@endsection