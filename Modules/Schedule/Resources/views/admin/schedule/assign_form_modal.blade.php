<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
{{--<link rel="stylesheet" href="{{ Module::asset('schedule:css/bootstrap-datepicker.min.css') }}">--}}
{{--<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />--}}

<link rel="stylesheet" type="text/css" href="{{ Module::asset('schedule:css/schedule.css?v='.\Carbon\Carbon::now()->timestamp) }}">



    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <div class="row">
        <div class="col-md-7 col-left">
            <div class="title clearfix">
                <div class="number">5</div>
                <div class="title_text">Review and edit SMS body if necessary</div>
            </div>
            <form method="post" action="{{route('admin.schedule.sendSMS')}}">
                {{csrf_field()}}
                @foreach($schedules as $schedule)
                    <input type="hidden" name="schedules[]" value="{{$schedule->id}}" />
                @endforeach
                <input type="hidden" name="replaceTeacher" value="{{$teacher->id}}" />
                <input type="hidden" name="replaceDate" value="{{$selectedDate}}" />

                <div class="form-group">
								<textarea name="msg_body" class="form-control step5-textarea txt-sms" rows="10">Dear {{$teacher->name}},
Please substitute for {{$schedules[0]->teacher->name}} for:
{{$selectedDate}}

@foreach($schedules as $key => $schedule)
 {{$key++}}, ({{$schedule->start_time}}) - {{$schedule->end_time}}){{$schedule->subject_code}}
@endforeach

Thank you.</textarea>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="ch-sms">Character: <span>0</span>/306</div>
                    </div>
                    <div class="col-md-5 text-right p-r-0">
                        <div class="remind-me">Remind me if <b>Mr Noor</b> does not reply in</div>
                    </div>
                    <div class="col-md-3 text-right">
                        <select class="step5-select">
                            <option value="15">15 mins</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label class="check-send">
                            <input type="checkbox" name="send_sms" checked value="">
                            Send SMS
                        </label>
                        <label>
                            <input type="checkbox" name="send_email" value="">
                            Send email
                        </label>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <p>Additional remarks</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-7">
                        <div class="form-group">
                            <textarea class="form-control step5-textarea txt-mark" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="col-md-5 text-right">
                        <button type="button" class="btn btn-cancel btn-close">Cancel</button>
                        <button type="submit" class="btn btn-assign">Assign</button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="ch-mark">Character: <span>0</span>/120</div>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-md-5 col-right">
            <div class="relief">
                <h5>Mr Noor</h5>
                <p>Relief assigned August 15, 2017: <span>0</span></p>
                <p>Relief assigned in week: <span>0</span></p>
                <p>Relief assigned this term: <span>0</span></p>
                <p>Relief assigned this year: <span>0</span></p>
            </div>
            <div class="info">
                <p>Email: noor_hazemi_ribot@moe.edu.sg</p>
                <p>Mobile: 6597327704</p>
            </div>
        </div>
    </div>



<?php $locale = App::getLocale(); ?>
<!-- Include all compiled plugins (below), or include individual files as needed -->

<!-- Include all compiled plugins (below), or include individual files as needed -->
<!-- Include all compiled plugins (below), or include individual files as needed -->
{{--<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>--}}
{{--<script src="{{ Module::asset('schedule:js/bootstrap-datepicker.min.js') }}"></script>--}}
{{--<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>--}}

{{--<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>--}}
{{--<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>--}}

{{--<script src="{{ Module::asset('schedule:js/admin-schedule.js?v='.\Carbon\Carbon::now()->timestamp) }}" type="text/javascript" charset="utf-8" async defer></script>--}}


