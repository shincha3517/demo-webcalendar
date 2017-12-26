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
                        <div class="container-step1">
                            <div class="step1">
                                <div class="title clearfix">
                                    <div class="number">1</div>
                                    <div class="title_text">Choose absent teacher with date</div>
                                </div>
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
                                <div class="well assignment-box">
                                    <div class="row">
                                        <div class="col-xs-12">
                                            <div class="">
                                                <ul></ul>
                                            </div>
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
                        <div class="container-step4"></div>

                    </div>
                    <div class="modal fade" id="step5" tabindex="-1" role="dialog">
                        <div class="container step5-container modal-dialog" role="document">
                        <div class="modal-content step5-content">
                            <div class="modal-body">

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

<div class="modal fade" id="confirm" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Cancel Message</h4>
                <p>Are you sure to cancel this subject?</p>
            </div>
            <div class="modal-footer">
                <form method="post" action="{{route('admin.schedule.cancel')}}">
                    {{csrf_field()}}
                    <input type="hidden" name="scheduleid" value="" />
                    <input type="hidden" name="selectedDate" value="" />

                    <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
                    <button type="submit" class="btn btn-primary">Yes</button>
                </form>
            </div>
        </div>
    </div>
</div>

@stop

@push('js-stack')
<?php $locale = App::getLocale(); ?>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/moment@2.20.1/moment.js"></script>
<script src="{{ Module::asset('schedule:js/bootstrap-datepicker.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>

{{--<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>--}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/gasparesganga-jquery-loading-overlay@1.5.4/src/loadingoverlay.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/gasparesganga-jquery-loading-overlay@1.5.4/extras/loadingoverlay_progress/loadingoverlay_progress.min.js"></script>

<script src="{{ Module::asset('schedule:js/admin-schedule.js?v='.\Carbon\Carbon::now()->timestamp) }}" type="text/javascript"></script>
<script>
    Home.active_assignment_dates = <?php echo json_encode($assignments) ?>;
    Home.init();
</script>
@endpush