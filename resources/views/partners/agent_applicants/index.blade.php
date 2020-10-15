@extends('layouts.app')

@section('content')
    <style>
        .btn-approve,
        .btn-disapprove,
        .btn-restore {
            cursor: pointer;
        }
    </style>

    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                Agent Applicants
            </h1>
            <div class="dotted-hr"></div>
        </section>

        <section class="content container-fluid">
            <div>
                <div class="col-md-12 mb-plus-20">
                    <div class="row">
                        <div class="col-md-6 offset-md-6 text-right">
                            <div class="input-group">
                                <button class="btn btn-info btn-approve">Approve</button>&nbsp;
                                <button class="btn btn-danger btn-disapprove">Disapprove</button>&nbsp;
                                <button class="btn btn-success btn-restore">Restore</button>&nbsp;
                                <select class="form-control" id="select-filter-agent-applicants">
                                    <option value="{{ App\Models\AgentApplicant::AGENT_APPLICANT_PENDING }}">Pending</option>
                                    <option value="{{ App\Models\AgentApplicant::AGENT_APPLICANT_APPROVED }}">Approved</option>
                                    <option value="{{ App\Models\AgentApplicant::AGENT_APPLICANT_DISAPPROVED }}">Disapproved</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="no-padding">
                    <form id="form-agent-applicants">
                        <table class="table responsive table-condense p-0" >
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>Company</th>
                                    <th>Email Address</th>
                                    <th>Mobile Number</th>
                                    <th>Business Address</th>
                                    <th>Source</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                        </table>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('script')
    <script src="{{ config('app.cdn') . '/js/partners/agent_applicants/index.js' . '?v=' . config('app.version') }}"></script>
@endsection