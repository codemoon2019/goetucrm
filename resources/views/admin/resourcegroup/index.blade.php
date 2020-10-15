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
      <h1>Resource Group Access</h1>
      
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/">Admin</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.resourcegroup.index') }}">Dev Access</a></li>
      </ol>

      <div class="dotted-hr"></div>
    </section>

    <section class="content container-fluid">
        <div class="row px-4 mb-3">
          <div class="col-lg-12 d-flex flex-row-reverse">
              
            {{-- @hasAccess('admin', 'dev-access') --}}
              <a href="{{ route('admin.resourcegroup.create') }}"  class="btn btn-sm btn-primary">
                <i class="fa fa-plus"></i>&nbsp;
                <span>Create Resource Group</span>
              </a>
            {{-- @endhasAccess --}}
          </div>
        </div>

        <div class="row px-2">
          <div class="col-lg-12">
            <table id="table-access" class="table table-striped table-bordered">
              <thead>
                <tr>
                  <th width="20%">Title</th>
                  <th width="80%">Description</th>
                </tr>
              </thead>
              
              <tbody>
                @foreach ($resourceGroups as $resourceGroup)
                  <tr>
                    <td><a href="{{ route('admin.resourcegroup.edit', $resourceGroup->id) }}">{{ $resourceGroup->name }}</td>
                    <td>{!! html_entity_decode($resourceGroup->description) !!}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>

    </section>
  </div>
@endsection

@section('script')
  <script src=@cdn('/js/admin/banners/index.js')></script>
@endsection