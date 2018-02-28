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
    <link href="https://fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">
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
                    <div class="col-xs-12">
                        <h3 style="font-size: 32px;margin-bottom: 40px; font-weight: 100;font-family: 'Lato';">This module not available for this account. Go <a href="{{ url()->previous() }}">back</a></h3>
                    </div>

                </div>
            <!-- /.box-body -->
            </div>
        <!-- /.box -->
        </div>
    </div>
</div>


@stop

@push('js-stack')

@endpush
