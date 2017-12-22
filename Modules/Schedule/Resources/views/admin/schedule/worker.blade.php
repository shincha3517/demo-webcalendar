@extends('layouts.master')

@section('content-header')
<h1>
    Leave Application
</h1>
<ol class="breadcrumb">
    <li><a href="{{ URL::route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ trans('core::core.breadcrumb.home') }}</a></li>
    <li class="active">Leave application</li>
</ol>
@stop
@push('css-stack')
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<link rel="stylesheet" href="{{ Module::asset('schedule:css/bootstrap-datepicker.min.css') }}">
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />

<link rel="stylesheet" type="text/css" href="{{ Module::asset('schedule:css/schedule.css?v='.\Carbon\Carbon::now()->timestamp) }}">
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
                            <div class="panel-heading">Step 1: Select absent date</div>
                            <div class="panel-body">
                                <div class="well">
                                    <div class="row">
                                        <div class="col-xs-12">
                                            <div id="datepicker"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xs-12" id="step2">
                        <div class="container-step3"></div>
                    </div>

                    <div class="col-xs-12" id="step3">
                        <div class="panel panel-default">
                            <div class="panel-heading">Step 3: Select Full Day absent or Partial Day absent </div>
                            <div class="panel-body">
                                <div class="row btn-pref" id="myTab">
                                    <div class="col-xs-4">
                                        <a class="btn btn-default btn-primary" id="fullDay" href="#tab1">FULL DAY Absent</a>
                                    </div>
                                    <div class="col-xs-4">
                                        <a class="btn btn-default" id="partialDay" href="#tab2">PARTIAL DAY Absent</a>
                                    </div>
                                    <div class="col-xs-4">
                                        <a class="btn btn-default" id="prolonged" href="#tab3">PROLONGED Absent</a>
                                    </div>
                                </div>
                                <div class="well">
                                    <div class="tab-content">
                                        <div class="tab-pane fade in active" id="tab1">

                                        </div>
                                        <div class="tab-pane fade in" id="tab2">
                                            <div class="row">
                                                <div class="col-xs-4"></div>
                                                <div class="col-xs-4">
                                                    <label>Absent From</label>
                                                    <div class="form-group">
                                                        <select id="startTime" class="form-control">
                                                            @if(!empty($timeSlot))
                                                                @foreach($timeSlot as $ts)
                                                                    <option>{{$ts['start']}}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    </div>
                                                    <label>Absent To</label>
                                                    <div class="form-group">
                                                        <select id="endTime" class="form-control">
                                                            @if(!empty($timeSlot))
                                                                @foreach($timeSlot as $ts)
                                                                    <option>{{$ts['start']}}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-xs-4"></div>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade in" id="tab3">
                                            <div class="row">
                                                <div class="col-xs-4"></div>
                                                <div class="col-xs-4">
                                                    <label> From</label>
                                                    <div class="form-group">
                                                        <input type="text" id="startDate" value="{{\Carbon\Carbon::today()->format('m/d/Y')}}" placeholder="{{\Carbon\Carbon::today()->format('m/d/Y')}}" />
                                                    </div>
                                                    <label> To</label>
                                                    <div class="form-group">
                                                        <input type="text" id="endDate" value="{{\Carbon\Carbon::tomorrow()->format('m/d/Y')}}" placeholder="{{\Carbon\Carbon::tomorrow()->format('m/d/Y')}}" />
                                                    </div>
                                                </div>
                                                <div class="col-xs-4"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="col-xs-12" id="step4">
                        <div class="panel panel-default">
                            <div class="panel-heading">Step 4: State the reason and confirm</div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-xs-12">
                                        <form class="form-horizontal" method="post" action="{{route('admin.schedule.sendAbsentRequest')}}">
                                            {{csrf_field()}}
                                            <input type="hidden" name="absentType" value="fullDay" id="absentType" />
                                            <input type="hidden" name="selectedDate" id="my_hidden_input" />
                                            <input type="hidden" name="teacherId" id="teacherId" value="{{$teacher->id}}" />

                                            <input type="hidden" name="input_startTime" value="" id="input_startTime" />
                                            <input type="hidden" name="input_endTime" value="" id="input_endTime" />

                                            <input type="hidden" name="input_startDate" value="" id="input_startDate" />
                                            <input type="hidden" name="input_endDate" value="" id="input_endDate" />
                                            <input type="hidden" name="input_scheduleId" value="" id="input_scheduleId" />

                                            <div class="teacherForm">
                                                <div class="form-group">
                                                    <label for="name" class="col-md-4 control-label">Select Teacher</label>
                                                    <div class="col-md-5">
                                                        <select class="form-control" name="replaceTeacherId" id="selectedUserAvailabel">
                                                            @if(count($teachers) > 0)
                                                                @foreach($teachers as $item)
                                                                    <option value="{{$item->id}}">{{$item->name}}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <a id="addMoreTeacher" href="javascript:void(0)"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span>Add more teacher</a>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label for="name" class="col-md-4 control-label">Reason for being absent</label>
                                                <div class="col-md-6">
                                                    <select class="form-control" name="reason">
                                                        <option value="">--choose item bellow</option>
                                                        <option value="medical leave">medical leave</option>
                                                        <option value="on course">on course</option>
                                                        <option value="on official duty">on official duty</option>
                                                        <option value="off-in-lieu">off-in-lieu</option>
                                                        <option value="time off">time off</option>
                                                        <option value="child care leave">child care leave</option>
                                                        <option value="child care sick leave">child care sick leave</option>
                                                        <option value="compassionate leave">compassionate leave</option>
                                                        <option value="hospitalisation leave">hospitalisation leave</option>
                                                        <option value="maternity leave">maternity leave</option>
                                                        <option value="maternity leave">paternity leave</option>
                                                        <option value="others">others</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="name" class="col-md-4 control-label">Addition Information(optional):</label>
                                                <div class="col-md-6">
                                            <textarea class="form-control" cols="4" rows="5" name="remark"></textarea>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-md-6 col-md-offset-4">
                                                    <button type="submit" class="btn btn-primary">
                                                        Confirm
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>

{{--<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>--}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/gasparesganga-jquery-loading-overlay@1.5.4/src/loadingoverlay.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/gasparesganga-jquery-loading-overlay@1.5.4/extras/loadingoverlay_progress/loadingoverlay_progress.min.js"></script>

<script src="{{ Module::asset('schedule:js/worker.js?v='.\Carbon\Carbon::now()->timestamp) }}" type="text/javascript"></script>
@endpush
