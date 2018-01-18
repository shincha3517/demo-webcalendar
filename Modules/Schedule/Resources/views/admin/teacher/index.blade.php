@extends('layouts.master')

@section('content-header')
    <h1>
        {{ trans('schedule::teacher.title.teacher') }}
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ URL::route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ trans('core::core.breadcrumb.home') }}</a></li>
        <li class="active">{{ trans('schedule::teacher.title.teacher') }}</li>
    </ol>
@stop

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="row">
                <div class="btn-group pull-right" style="margin: 0 15px 15px 0;">
                    <a href="{{ URL::route('admin.schedule.teacher.create') }}" class="btn btn-primary btn-flat" style="padding: 4px 10px;">
                        <i class="fa fa-pencil"></i> {{ trans('schedule::teacher.create') }}
                    </a>
                </div>
            </div>
            <div class="box box-primary">
                <div class="box-header">
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <table class="data-table table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th>Id</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Subject</th>
                            <th>Can access</th>
                            <th>{{ trans('core::core.table.created at') }}</th>
                            <th data-sortable="false">{{ trans('core::core.table.actions') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (isset($teachers)): ?>
                        <?php foreach ($teachers as $teacher): ?>
                        <tr>
                            <td>
                                <a href="{{ URL::route('admin.schedule.teacher.edit', [$teacher->id]) }}">
                                    {{ $teacher->id }}
                                </a>
                            </td>
                            <td>
                                <a href="{{ URL::route('admin.schedule.teacher.edit', [$teacher->id]) }}">
                                    {{ $teacher->name }}
                                </a>
                            </td>
                            <td>
                                <a href="{{ URL::route('admin.schedule.teacher.edit', [$teacher->id]) }}">
                                    {{ $teacher->email }}
                                </a>
                            </td>
                            <td>
                                <a href="{{ URL::route('admin.schedule.teacher.edit', [$teacher->id]) }}">
                                    {{ $teacher->phone_number }}
                                </a>
                            </td>
                            <td>
                                <a href="{{ URL::route('admin.schedule.teacher.edit', [$teacher->id]) }}">
                                    {{ $teacher->subject }}
                                </a>
                            </td>
                            <td>
                                <a href="{{ URL::route('admin.schedule.teacher.edit', [$teacher->id]) }}">
                                    {{ $teacher->user ? 'Yes': 'No' }}
                                </a>
                            </td>
                            <td>
                                <a href="{{ URL::route('admin.schedule.teacher.edit', [$teacher->id]) }}">
                                    {{ $teacher->created_at }}
                                </a>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ URL::route('admin.schedule.teacher.edit', [$teacher->id]) }}" class="btn btn-default btn-flat"><i class="fa fa-pencil"></i></a>
                                    <button data-toggle="modal" data-target="#modal-delete-confirmation" data-action-target="{{ route('admin.page.page.destroy', [$teacher->id]) }}" class="btn btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
        </div>
    </div>
    @include('core::partials.delete-modal')
@stop

@section('footer')
    <a data-toggle="modal" data-target="#keyboardShortcutsModal"><i class="fa fa-keyboard-o"></i></a> &nbsp;
@stop
@section('shortcuts')
    <dl class="dl-horizontal">
        <dt><code>c</code></dt>
        <dd>{{ trans('page::pages.title.create page') }}</dd>
    </dl>
@stop

@push('js-stack')
    <?php $locale = App::getLocale(); ?>
    <script type="text/javascript">
        $( document ).ready(function() {
            $(document).keypressAction({
                actions: [
                    { key: 'c', route: "<?= route('admin.page.page.create') ?>" }
                ]
            });
        });
        $(function () {
            $('.data-table').dataTable({
                "paginate": true,
                "lengthChange": true,
                "filter": true,
                "sort": true,
                "info": true,
                "autoWidth": true,
                "order": [[ 0, "desc" ]],
                "language": {
                    "url": '<?php echo Module::asset("core:js/vendor/datatables/{$locale}.json") ?>'
                }
            });
        });
    </script>
@endpush
