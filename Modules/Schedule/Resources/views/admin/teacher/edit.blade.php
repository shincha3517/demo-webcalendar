@extends('layouts.master')

@section('content-header')
    <h1>
        {{ trans('schedule::teacher.title.edit teacher') }}
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ URL::route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ trans('core::core.breadcrumb.home') }}</a></li>
        <li><a href="{{ URL::route('admin.schedule.teacher.index') }}">{{ trans('schedule::teacher.title.index') }}</a></li>
        <li class="active">{{ trans('schedule::teacher.title.edit teacher') }}</li>
    </ol>
@stop

@push('css-stack')
    <style>
        .checkbox label {
            padding-left: 0;
        }
    </style>
@endpush

@section('content')
    {!! Form::open(['route' => ['admin.schedule.teacher.update', $teacher->id], 'method' => 'put']) !!}
    <div class="row">
        <div class="col-md-10">
            <div class="nav-tabs-custom">
                @include('partials.form-tab-headers', ['fields' => ['title', 'body']])
                <div class="tab-content">
                    <?php $i = 0; ?>
                    <?php foreach (LaravelLocalization::getSupportedLocales() as $locale => $language): ?>
                    <?php ++$i; ?>
                    <div class="tab-pane {{ App::getLocale() == $locale ? 'active' : '' }}" id="tab_{{ $i }}">
                        @include('schedule::admin.teacher.partials.edit-fields', ['lang' => $locale])
                    </div>
                    <?php endforeach; ?>
                    <?php if (config('asgard.page.config.partials.normal.edit') !== []): ?>
                        <?php foreach (config('asgard.page.config.partials.normal.edit') as $partial): ?>
                            @include($partial)
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary btn-flat" name="button" value="index" >
                            <i class="fa fa-angle-left"></i>
                            {{ trans('core::core.button.update and back') }}
                        </button>
                        <button type="submit" class="btn btn-primary btn-flat">
                            {{ trans('core::core.button.update') }}
                        </button>
                        <a class="btn btn-danger pull-right btn-flat" href="{{ URL::route('admin.schedule.teacher.index')}}"><i class="fa fa-times"></i> {{ trans('core::core.button.cancel') }}</a>
                    </div>
                </div>
            </div> {{-- end nav-tabs-custom --}}
        </div>
        <div class="col-md-2">
            <div class="box box-primary">
                <div class="box-body">
                    <div class="checkbox{{ $errors->has('teacher_type') ? ' has-error' : '' }}">
                        <input type="hidden" name="teacher_type" value="0">
                        <label for="is_home">
                            <input id="teacher_type"
                                   name="teacher_type"
                                   type="checkbox"
                                   class="flat-blue"
                                    {{ isset($teacher->teacher_type) && (bool)$teacher->teacher_type == true ? 'checked' : '' }}
                                   value="1" />
                            Is part time?
                            {!! $errors->first('teacher_type', '<span class="help-block">:message</span>') !!}
                        </label>
                    </div>
                    <hr/>
                    <div class="form-group">
                        <label>{{ trans('user::users.tabs.roles') }}</label>
                        <select multiple="" class="form-control" name="roles[]">
                            <?php foreach ($roles as $role): ?>
                            <option value="{{ $role->id }}" <?php echo isset($teacher->user) && $teacher->user->hasRoleId($role->id) ? 'selected' : '' ?>>{{ $role->name }}</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {!! Form::close() !!}
@stop

@section('footer')
    <a data-toggle="modal" data-target="#keyboardShortcutsModal"><i class="fa fa-keyboard-o"></i></a> &nbsp;
@stop
@section('shortcuts')
    <dl class="dl-horizontal">
        <dt><code>b</code></dt>
        <dd>{{ trans('schedule::teacher.navigation.back to index') }}</dd>
    </dl>
@stop

@push('js-stack')
    <script>
        $( document ).ready(function() {
            $(document).keypressAction({
                actions: [
                    { key: 'b', route: "<?= route('admin.schedule.teacher.index') ?>" }
                ]
            });
            $('input[type="checkbox"].flat-blue, input[type="radio"].flat-blue').iCheck({
                checkboxClass: 'icheckbox_flat-blue',
                radioClass: 'iradio_flat-blue'
            });
        });
    </script>
@endpush
