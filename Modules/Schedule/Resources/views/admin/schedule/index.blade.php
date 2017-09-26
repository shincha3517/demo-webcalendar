@extends('layouts.master')

@section('content-header')
<h1>
    Assign Teacher
</h1>
<ol class="breadcrumb">
    <li><a href="{{ URL::route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ trans('core::core.breadcrumb.home') }}</a></li>
    <li class="active">Assign Teacher</li>
</ol>
@stop
@push('css-stack')
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<link rel="stylesheet" href="{{ Module::asset('schedule:css/bootstrap-datepicker.min.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/vis/4.20.1/vis.min.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />

<style>

    .vis-item.orange {
        background-color: gold;
        border-color: orange;
    }
    .vis-item.vis-selected.orange {
        /* custom colors for selected orange items */
        background-color: orange;
        border-color: orangered;
    }
</style>

@endpush


@section('content')
<div class="row">
    <div class="col-xs-12">

        <div class="box box-primary">
            <div class="box-header">
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <div class="row">
                    <div class="col-xs-12" id="step1">
                        <div class="panel panel-default">
                            <div class="panel-heading">Step 1: Choose absent teacher with date</div>
                            <div class="panel-body">
                                <div class="well">
                                    <div class="row">
                                        <div class="col-xs-6">
                                            <div class="row">
                                                <div class="col-xs-12">
                                                    <div id="datepicker"></div>
                                                    <input type="hidden" id="my_hidden_input" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xs-6">
                                            <select class="form-control" id="ddUser">
                                                <option>--Please select date first</option>
                                            </select>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xs-12" id="step2">
                        <div class="panel panel-default">
                            <div class="panel-heading">Step 2: Select subject name</div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-xs-12">
                                        <div id="visualization"></div>
                                    </div>
                                    <div class="col-xs-12">
                                        <button type="button" id="searchTeacher" class="btn btn-default">Search available teacher</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xs-12" id="step3">
                        <div class="panel panel-default">
                            <div class="panel-heading">Step 3: Show Available teacher on selected day </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-xs-12">
                                        <div id="availableUserTimeline"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xs-12" id="step4">
                        <div class="panel panel-default">
                            <div class="panel-heading">Step 4: Send Push notification to selected teacher</div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-xs-12">
                                        <form class="form-horizontal" method="post" action="{{route('admin.schedule.sendSMS')}}">
                                            {{csrf_field()}}
                                            <div class="form-group hide">
                                                <label for="name" class="col-md-4 control-label">Select Teacher</label>
                                                <div class="col-md-6">
                                                    <select class="form-control" id="selectedUserAvailabel">

                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="name" class="col-md-4 control-label">Send Notification</label>
                                                <div class="col-md-6">
                                                    <input type="checkbox" value="email" /> Email
                                                    <input type="checkbox" value="email" /> SMS
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="name" class="col-md-4 control-label">Send Notification</label>
                                                <div class="col-md-6">
                                            <textarea class="form-control" cols="4" rows="5">Hello {User}
You has recieved invite to handle new job in date {date_format}
Regards,
                                            </textarea>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-md-6 col-md-offset-4">
                                                    <button type="submit" class="btn btn-primary">
                                                        Send
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <!-- /.box-body -->
            </div>
        <!-- /.box -->
    </div>
<!-- /.col (MAIN) -->
</div>
</div>


@stop

@push('js-stack')
<?php $locale = App::getLocale(); ?>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="{{ Module::asset('schedule:js/bootstrap-datepicker.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/vis/4.20.1/vis.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>

<script src="{{ Module::asset('schedule:js/main.js?v='.\Carbon\Carbon::now()->timestamp) }}" type="text/javascript"></script>
@endpush
