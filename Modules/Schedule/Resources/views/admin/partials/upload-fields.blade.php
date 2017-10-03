<div class="box-body">
    <div class="box-body">
        <div class='form-group{{ $errors->has("importedFile") ? ' has-error' : '' }}'>
            {!! Form::label("importedFile", trans('schedule::form.upload')) !!}
            {!! Form::file("importedFile", null) !!}
            {!! $errors->first("importedFile", '<span class="help-block">:message</span>') !!}
        </div>
        <div class='form-group{{ $errors->has("interval") ? ' has-error' : '' }}'>
            {!! Form::label("interval", 'Intervals') !!}
            {!! Form::select("interval", [''=>'Please select your intervals','15'=>'15 mins','30'=>'30 mins'],'30',['class' => 'form-control','id'=>'interval']) !!}
            {!! $errors->first("interval", '<span class="help-block">:message</span>') !!}
        </div>
        <div class='form-group{{ $errors->has("startTime") ? ' has-error' : '' }}'>
            {!! Form::label("startTime", 'Start Time') !!}
            {!! Form::text("startTime", old("startTime"), ['class' => 'form-control','id'=>'startTime']) !!}
            {!! $errors->first("startTime", '<span class="help-block">:message</span>') !!}
        </div>

        <?php if (config('asgard.page.config.partials.translatable.create') !== []): ?>
            <?php foreach (config('asgard.page.config.partials.translatable.create') as $partial): ?>
                @include($partial)
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</div>
