<div class="box-body">
    <div class="box-body">
        <div class='form-group{{ $errors->has("{$lang}.title") ? ' has-error' : '' }}'>
            {!! Form::label("{$lang}[title]", trans('schedule::form.upload')) !!}
            {!! Form::file('file', null,['id' => 'exampleInputFile']) !!}
            {!! $errors->first("{$lang}.title", '<span class="help-block">:message</span>') !!}
        </div>

        <?php if (config('asgard.page.config.partials.translatable.create') !== []): ?>
            <?php foreach (config('asgard.page.config.partials.translatable.create') as $partial): ?>
                @include($partial)
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</div>
