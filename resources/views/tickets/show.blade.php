@extends('layouts.app')

@section('content')
    <div class="hidden">
        <input type="hidden" id="ticketId" value="{{ $detail->ticketHeader->id }}"/>
    </div>
    <div class="content-wrapper">
        <section class="content-header">
            <h1>{{ __("model.ticket.show") }}</h1>
            <ol class="breadcrumb">
                <li><a href="#">{{ __("common.dashboard") }}</a></li>
                <li><a href="{{ url('tickets') }}">{{ __("model.ticket.list") }}</a></li>
                <li class="active">{{ __("model.ticket.show") }}</li>
            </ol>
            <div class="dotted-hr"></div>
        </section>

        <section class="content container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <!-- Ticket Information Card -->
                    <div class="card bg-primary">
                        <div class="card-header text-white">
                            {{ __("model.ticket.show") . ": " . ucfirst($detail->ticketHeader->subject) }} <br/>
                        </div>
                        <div class="card-body bg-light">

                            <!-- Content -->
                            <div class="row crt-tickets">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <label class="input-group-addon">{{ __("model.ticket.ticketCreator") }}</label>
                                            <span class="label custom-form-field">{{ \Illuminate\Support\Facades\Auth::user()->first_name . " " . \Illuminate\Support\Facades\Auth::user()->last_name }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <label class="input-group-addon">{{ __("model.ticket.product") }}
                                                :</label>
                                            <span class="label custom-form-field">{{ isset($detail->product) ? $detail->product->name: __("common.no") . " " .  __("model.ticket.product") }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <label class="input-group-addon">{{ __("model.ticket.partner") }}
                                                :</label>
                                            <span class="label custom-form-field">{{ isset($detail->ticketHeader->partnerCompany) ? $detail->ticketHeader->partnerCompany->company_name : __("common.no") . " " . __("model.ticket.partner") }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <label class="input-group-addon">{{ __("model.ticket.department") }}
                                                :</label>
                                            <span class="label custom-form-field">{{ isset($detail->department) ? $detail->department->description : __("common.no") . " " . __("model.ticket.department") }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <label class="input-group-addon">{{ __("model.ticket.type") }} :</label>
                                            <span class="label custom-form-field">{{ isset($detail->type) ? $detail->type->description : __("common.no") . " " . __("model.ticket.type") }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <label class="input-group-addon">{{ __("model.ticket.dueDate") }}
                                                :</label>
                                            <span class="label custom-form-field">{{ \Carbon\Carbon::parse($detail->ticketHeader->due_date)->format('Y-m-d h:i A') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <label class="input-group-addon">{{ __("model.ticket.priority") }}
                                                :</label>
                                            <span class="label custom-form-field">{{ $detail->priority->description }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <label class="input-group-addon">{{ __("model.ticket.status") }}
                                                :</label>
                                            <select name="status" id="status" class="form-control">
                                                @foreach(\App\Models\TicketStatus::where('status','=', \App\Contracts\Constant::DEFAULT_STATUS_ACTIVE)->get() as $status)
                                                    <option value="{{ $status->code }}" {{ $status->code == $detail->ticketHeader->status ? "selected='selected'" : "" }}>{{ $status->description }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <label class="input-group-addon">{{ __("model.ticket.description") }}</label>
                                            {!! html_entity_decode($detail->ticketHeader->description) !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <label class="input-group-addon">{{ __("model.ticket.assignedTo") }}
                                                :</label>
                                            <div class="col-md-12">
                                                @if(isset($detail->users))
                                                    @foreach($detail->users as $user)
                                                        <span>{{ $user->first_name . " " . $user->last_name }}</span>
                                                        <br/>
                                                    @endforeach
                                                @else
                                                    <span>No assignee</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <label class="input-group-addon">{{ __("model.ticket.attachment") . "s" }}</label>
                                            <div class="col-md-12">
                                                @if(!empty($detail->ticketHeader->attachment))
                                                    @foreach(json_decode($detail->ticketHeader->attachment,true) as $attachmentKey=>$attachment)
                                                        <label>Attachment # {{ $attachmentKey + 1 }}:
                                                            <a
                                                                    href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($attachment) }}"
                                                                    target="_blank">{{ basename( \Illuminate\Support\Facades\Storage::disk('public')->url($attachment)) }}</a>
                                                        </label><br/>
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    @if(\Illuminate\Support\Facades\Auth::user()->username == $detail->ticketHeader->create_by)
                                        <div class="form-group pull-right">
                                            <a href="{{ url('/tickets/' . $detail->ticketHeader->id . '/edit') }}"
                                               class="btn btn-primary">Edit</a>
                                        </div>
                                    @endif

                                </div>
                            </div>
                            <!-- End Content -->
                        </div>
                    </div>
                    <!-- End ticket information card -->
                    <div style="margin-top: 10px; clear: both"></div>
                    <!-- Comment Section -->
                    <div class="card bg-primary">
                        <div class="card-header text-white">
                            {{ __("model.ticket.commentSection") }}
                        </div>
                        <div class="card-body bg-light">
                            <div class="col-md-12">
                                <div class="row crt-tickets">
                                    @foreach($detail->ticketHeader->ticketDetails as $ticketDetailKey=>$ticketDetail)
                                        <div style="border-bottom: 1px solid #CCC;width: 95%; padding: 5px;margin-bottom: 10px">
                                            <p>{!!  html_entity_decode($ticketDetail->message) !!}</p>
                                            @if(!empty($ticketDetail->attachment))
                                                @foreach(\json_decode($ticketDetail->attachment) as $attachmentKey=>$attachment)
                                                    <p>Attachment {{ $attachmentKey + 1 }}: <a
                                                                href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($attachment) }}"
                                                                target="_blank">{{ basename(\Illuminate\Support\Facades\Storage::disk('public')->url($attachment)) }}</a>
                                                    </p>
                                                @endforeach
                                            @endif
                                        </div>
                                    @endforeach
                                    <div class="col-md-12">
                                        <form role="form"
                                              action="{{ url("/tickets/". $detail->ticketHeader->id . "/store-comment") }}"
                                              method="POST"
                                              enctype="multipart/form-data">
                                            {{ csrf_field() }}
                                            <div class="form-group">
                                                <label>{{ __("model.ticket.comment") }}</label>
                                                <textarea id="comment" name="comment"
                                                          placeholder="place your comment"></textarea>
                                            </div>
                                            <div class="form-group">
                                                <label for="exampleFormControlFile1" class="btn btn-primary">Attachment</label>
                                                <label id="file_uploaded"></label>
                                                <input type="file" name="attachment" id="exampleFormControlFile1" hidden>
                                            </div>
                                            <button class="btn btn-primary pull-right">{{ __("common.submit") }}</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Comment Section -->
        </section>
    </div>
@endsection
@section('script')
    <script src="{{ config("app.cdn") . "/js/ticket/show.js" . "?v=" . config("app.version") }}"></script>
@endsection