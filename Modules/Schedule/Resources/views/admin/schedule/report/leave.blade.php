@extends('layouts.master')

@section('content-header')
    <h1>
        {{ trans('page::pages.title.pages') }}
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ URL::route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ trans('core::core.breadcrumb.home') }}</a></li>
        <li class="active">{{ trans('page::pages.title.pages') }}</li>
    </ol>
@stop

@push('css-stack')
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.4.2/css/buttons.dataTables.min.css">
@endpush

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">
                <div class="box-header">
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <table class="data-table table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th>Id</th>
                            <th>Absent Teacher</th>
                            <th>Relief Teacher Name</th>
                            <th>Absent Date</th>
                            <th>Absent Time</th>
                            <th>Reason for absence</th>
                            <th>Additional remarks</th>
                            <th>Status</th>
                            <th>{{ trans('core::core.table.created at') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (isset($reports)): ?>
                        <?php foreach ($reports as $item): ?>
                        <tr>
                            <td>{{$item->id}}</td>
                            <td>{{$item->teacher_name}}</td>
                            <td>{{$item->replaced_teacher_name}}</td>
                            <td>{{$item->selected_date}}</td>
                            <td>{{ substr(\Carbon\Carbon::parse($item->start_date)->toTimeString(),0,-3)}} - {{substr(\Carbon\Carbon::parse($item->end_date)->toTimeString(),0,-3)}}</td>
                            <td>{{$item->reason}}</td>
                            <td>{{$item->additionalRemark}}</td>
                            <td>
                                <?php
                                    if($item->status ==1){
                                        $status = 'Accepted';
                                    }elseif($item->status ==2){
                                        $status = 'Rejected';
                                    }
                                    else{
                                        $status = 'Not verify';
                                    }
                                    echo $status;
                                ?>
                            </td>
                            <td>{{$item->created_at}}</td>
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
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.3.1/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.3.1/js/buttons.html5.min.js"></script>

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
            var buttonCommon = {
                exportOptions: {
                    format: {
                        body: function ( data, row, column, node ) {
                            // Strip $ from salary column to make it numeric
                            return column === 5 ?
                                data.replace( /[$,]/g, '' ) :
                                data;
                        }
                    }
                }
            };
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
                },
                dom: 'Bfrtip',
                buttons: [
                    'excelHtml5'
                ]
            });
        });
    </script>
@endpush
