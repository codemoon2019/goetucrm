@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                Training Modules
                <!-- <small>Dito tayo magpapasok ng different pages</small> -->
            </h1>
            <ol class="breadcrumb">
                <li><a href="/">Dashboard </a></li>
                <li class="active">Training Modules</li>
            </ol>
            <div class="dotted-hr"></div>
        </section>

        <section class="content container-fluid">
            <input type="hidden" id="webroot" value = "{{ url('') }}">
            <div class="col-md-12">
                <div class="row">
                   @include("training.menu")
                   @if(isset($module))
                        @if($module->name == "Credit Card Processing")
                            <input type="hidden" id="folderpath" value = "credit-card">
                            @include("training.modules.creditcard")
                        @endif
                        @if($module->name == "POS")
                            <input type="hidden" id="folderpath" value = "pos">
                            @include("training.modules.pos")
                        @endif
                        @if($module->name == "Gift Card & Loyalty")
                            <input type="hidden" id="folderpath" value = "giftcard-loyalty">
                            @include("training.modules.giftcard")
                        @endif
                        @if($module->name == "Website & Online Ordering")
                            <input type="hidden" id="folderpath" value = "website-onlineorder">
                            @include("training.modules.webolo")
                        @endif
                        @if($module->name == "Terminals & Hardware")
                            <input type="hidden" id="folderpath" value = "terminals-hardware">
                            @include("training.modules.terminal")
                        @endif
                        @if($module->name == "Agent Office")
                            <input type="hidden" id="folderpath" value = "agent-office">
                            @include("training.modules.agentoffice")
                        @endif
                        @if($module->name == "Agent Resources")
                            <input type="hidden" id="folderpath" value = "agent-resources">
                            @include("training.modules.agentresources")
                        @endif
                        @if($module->name == "Rewards")
                            <input type="hidden" id="folderpath" value = "rewards">
                            @include("training.modules.rewards")
                        @endif
                        @if($module->name == "Reservation")
                            <input type="hidden" id="folderpath" value = "reservation">
                            @include("training.modules.reservations")
                        @endif                        
                   @endif
                </div>
            </div>
        </section>
    </div>
@endsection
@section('script')
<script>
    $('a.prod-items-link').click(function(e) {
        e.preventDefault();
        var target = $(this).attr('id');
        var path = $('#folderpath').val();
        $('#training-content').load('/training-assets/'+path+'/training_'+target+'.html');
    });
</script>
@endsection