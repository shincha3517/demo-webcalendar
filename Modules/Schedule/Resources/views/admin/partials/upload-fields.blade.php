<div class="box-body">
    <div class="box-body">
        <div class='form-group{{ $errors->has("importedFile") ? ' has-error' : '' }}'>
            {!! Form::label("importedFile", trans('schedule::form.upload')) !!}
            {!! Form::file("importedFile", null) !!}
            {!! $errors->first("importedFile", '<span class="help-block">:message</span>') !!}
        </div>
        <div class='form-group{{ $errors->has("interval") ? ' has-error' : '' }}'>
            {!! Form::label("interval", 'Intervals') !!}
            {!! Form::select("interval", [''=>'Please select your intervals','15'=>'15 mins','30'=>'30 mins'],false,['class' => 'form-control','id'=>'interval']) !!}
            {!! $errors->first("interval", '<span class="help-block">:message</span>') !!}
        </div>
        <div class="bootstrap-timepicker">
            <div class="bootstrap-timepicker-widget dropdown-menu"></div>
            <div class='form-group{{ $errors->has("startTime") ? ' has-error' : '' }}'>
                {!! Form::label("startTime", 'Start Time') !!}
                <div class="input-group">
                    {!! Form::text("startTime", old("startTime"), ['class' => 'form-control timepicker','id'=>'startTime']) !!}
                    {!! $errors->first("startTime", '<span class="help-block">:message</span>') !!}

                    <div class="input-group-addon">
                        <i class="fa fa-clock-o"></i>
                    </div>
                </div>
            </div>
        </div>

        <!--
        <div class="bootstrap-timepicker">
            <div class="bootstrap-timepicker-widget dropdown-menu"></div>
            <div class='form-group{{ $errors->has("startTime") ? ' has-error' : '' }}'>
                {!! Form::label("startDate", 'Start date for odd week') !!}
                <div class="input-group">
                    {!! Form::text("startDate", old("startDate"), ['class' => 'form-control datepicker','id'=>'startDate']) !!}
                    {!! $errors->first("startDate", '<span class="help-block">:message</span>') !!}

                    <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </div>
                </div>
            </div>
        </div>-->

        <?php if (config('asgard.page.config.partials.translatable.create') !== []): ?>
            <?php foreach (config('asgard.page.config.partials.translatable.create') as $partial): ?>
                @include($partial)
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</div>
