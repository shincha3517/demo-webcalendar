<div class="box-body">
    <div class="box-body">
        <div class='form-group{{ $errors->has("name") ? ' has-error' : '' }}'>
            {!! Form::label("name", trans('schedule::teacher.form.name')) !!}
            <?php $old = $teacher->name ? $teacher->name : '' ?>
            {!! Form::text("name", old("name", $old), ['class' => 'form-control', 'placeholder' => trans('schedule::teacher.form.name')]) !!}
            {!! $errors->first("name", '<span class="help-block">:message</span>') !!}
        </div>

        <div class='form-group{{ $errors->has("email") ? ' has-error' : '' }}'>
            {!! Form::label("email", trans('schedule::teacher.form.email')) !!}
            <?php $old = $teacher->email ? $teacher->email : '' ?>
            {!! Form::text("email", old("email", $old), ['class' => 'form-control', 'placeholder' => trans('schedule::teacher.form.email')]) !!}
            {!! $errors->first("email", '<span class="help-block">:message</span>') !!}
        </div>

        <div class='form-group{{ $errors->has("phone_number") ? ' has-error' : '' }}'>
            {!! Form::label("phone_number", trans('schedule::teacher.form.phone_number')) !!}
            <?php $old = $teacher->phone_number ? $teacher->phone_number : '' ?>
            {!! Form::text("phone_number", old("phone_number", $old), ['class' => 'form-control', 'placeholder' => trans('schedule::teacher.form.phone_number')]) !!}
            {!! $errors->first("phone_number", '<span class="help-block">:message</span>') !!}
        </div>

        <div class='form-group{{ $errors->has("subject") ? ' has-error' : '' }}'>
            {!! Form::label("subject", trans('schedule::teacher.form.subject')) !!}
            <?php $old = $teacher->subject ? $teacher->subject : '' ?>
            {!! Form::text("subject", old("subject", $old), ['class' => 'form-control', 'placeholder' => trans('schedule::teacher.form.subject')]) !!}
            {!! $errors->first("subject", '<span class="help-block">:message</span>') !!}
        </div>

        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
            {!! Form::label('password', trans('user::users.form.new password')) !!}
            {!! Form::input('password', 'password', '', ['class' => 'form-control']) !!}
            {!! $errors->first('password', '<span class="help-block">:message</span>') !!}
        </div>
        <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
            {!! Form::label('password_confirmation', trans('user::users.form.new password confirmation')) !!}
            {!! Form::input('password', 'password_confirmation', '', ['class' => 'form-control']) !!}
            {!! $errors->first('password_confirmation', '<span class="help-block">:message</span>') !!}
        </div>
    </div>

</div>
