@extends('layouts.master')

@section('content-header')
<h1>
    Upload Excel File
</h1>
<ol class="breadcrumb">
    <li><a href="{{ URL::route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ trans('core::core.breadcrumb.home') }}</a></li>
    <li class="active">upload</li>
</ol>
@stop
@push('css-stack')
<link rel="stylesheet" type="text/css" href="http://jonthornton.github.io/jquery-timepicker/jquery.timepicker.css" />
<link rel="stylesheet" type="text/css" href="http://jonthornton.github.io/jquery-timepicker/lib/bootstrap-datepicker.css" />
@endpush

@section('content')
    {!! Form::open(['route' => ['admin.schedule.upload.store'], 'method' => 'post','enctype'=>'multipart/form-data','id'=>'upload-frm']) !!}
    <div class="row">
        <div class="col-md-12">
            <div class="nav-tabs-custom">
                @include('partials.form-tab-headers', ['fields' => ['title', 'body']])
                <div class="tab-content">
                    <?php $i = 0; ?>
                    <?php foreach (LaravelLocalization::getSupportedLocales() as $locale => $language): ?>
                    <?php ++$i; ?>
                    <div class="tab-pane {{ App::getLocale() == $locale ? 'active' : '' }}" id="tab_{{ $i }}">
                        @include('schedule::admin.partials.upload-fields', ['lang' => $locale])
                    </div>
                    <?php endforeach; ?>
                    <?php if (config('asgard.page.config.partials.normal.create') !== []): ?>
                    <?php foreach (config('asgard.page.config.partials.normal.create') as $partial): ?>
                    @include($partial)
                    <?php endforeach; ?>
                    <?php endif; ?>

                        <div class="progress">
                            <div id="progress-label" class="progress-label">
                                <!-- Progress text will be rendered here -->
                            </div>
                            <div id="progress-container" class="progress-container">
                                <!-- Progress bar will be rendered here -->
                            </div>
                        </div>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary btn-flat">Upload</button>
                    </div>
                </div>
            </div> {{-- end nav-tabs-custom --}}
        </div>
    </div>

    {!! Form::close() !!}


@stop

@push('js-stack')
<?php $locale = App::getLocale(); ?>
<script type="text/javascript" src="http://jonthornton.github.io/jquery-timepicker/jquery.timepicker.js"></script>
<script type="text/javascript" src="http://jonthornton.github.io/jquery-timepicker/lib/bootstrap-datepicker.js"></script>

<script src="{{ Module::asset('schedule:js/uploadExcel.js?v='.\Carbon\Carbon::now()->timestamp) }}" type="text/javascript"></script>
<script type="text/javascript">

    var BASE_URL = '{{url('/')}}';
</script>
@endpush
