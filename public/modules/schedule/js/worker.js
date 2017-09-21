/**
 * Created by eric on 8/22/17.
 */
var Home = {

    timeline: '',
    availableUserTimeline: '',

    onSelectCalendar: function(){
        $('#datepicker').datepicker();
        $('#datepicker').on('changeDate', function() {

            $('#my_hidden_input').val(
                $('#datepicker').datepicker('getFormattedDate')
            );


            var teacher_id = $('#teacherId').val();

            if(teacher_id > 0){
                if(Home.timeline !=''){
                    // console.log('disable timeline');
                    Home.timeline.destroy();
                }
                var selectedDate = $('#my_hidden_input').val();
                //show timeline
                Home.onShowTimeLine(teacher_id,selectedDate);
            }
            else{
                Home.timeline.destroy();
            }

            $('#step2').show();
            $('#step3').hide();
            $('#step4').hide();

            $('html, body').animate({
                scrollTop: ($('#step1').offset().top)
            },500);
        });
    },

    onSelectUser: function () {
        $('#ddUser').on('change',function(e){



        });
    },
    onShowTimeLine: function(teacherId,dateSelected){

        $.ajax({
            type: "GET",
            url: "/backend/schedule/getUserTimeline?teacher_id="+teacherId+'&date='+dateSelected,
            success: function(data)
            {
                if(data.status==1){
                    // DOM element where the Timeline will be attached
                    var container = document.getElementById('visualization');

                    // Create a DataSet (allows two way data-binding)
                    // var items = new vis.DataSet([
                    //     {"id": 1,"content": "item 1","start": "2017-09-22T04:00:00","end": "2017-08-22T04:30:00"},
                    //     {"id": 2,"content": "item 1","start": "2017-08-22T06:00:00","end": "2017-08-22T10:30:00"},
                    //     {"id": 3,"content": "item 1","start": "2017-08-22T11:00:00","end": "2017-08-22T11:30:00"},
                    //     {"id": 4,"content": "item 1","start": "2017-08-22T15:00:00","end": "2017-08-22T15:30:00"},
                    // ]);
                    // console.log(data);

                    var items = new vis.DataSet(data.result);

                    // Configuration for the Timeline
                    var options = {
                        moment: function(date) {
                            return vis.moment(date).utcOffset('+07:00');
                        },
                        height: '200px',
                        min: data.min,                // lower limit of visible range
                        max: data.max,                // upper limit of visible range
                        zoomMin: 5
                    };

                    // Create a Timeline
                    Home.timeline = new vis.Timeline(container, items, options);

                    Home.onSelectTimeline();

                    $('#step2').show();

                    $('html, body').animate({
                        scrollTop: ($('#step2').offset().top)
                    },500);
                }
            }
        });




    },
    onSelectTimeline: function(){

        $(".btn-pref .btn").click(function () {
            $(".btn-pref .btn").removeClass("btn-primary").addClass("btn-default");
            // $(".tab").addClass("active"); // instead of this do the below
            $(this).removeClass("btn-default").addClass("btn-primary");
        });

        this.timeline.on('select', function (properties) {
            var eventId = parseInt(properties.items,10);
            if(eventId > 0){
                $('#step3').fadeIn('slow');
                $('#step4').fadeIn('slow');

                $('html, body').animate({
                    scrollTop: ($('#step3').offset().top)
                },500);
            }
        });
    },
    onShowAvailableUser: function(users,timelines,min,max){
        if(this.availableUserTimeline){
            this.availableUserTimeline.destroy();
            $('#availableUserTimeline').html();
        }
        // DOM element where the Timeline will be attached
        var container = document.getElementById('availableUserTimeline');
        // var groups = new vis.DataSet([
        //     {id: 0, content: 'Username 1', value: 1},
        //     {id: 1, content: 'Username 2', value: 3}
        // ]);
        var groups = new vis.DataSet(users);

        // Create a DataSet (allows two way data-binding)
        // var items = new vis.DataSet([
        //     {"id": 1,"group":0,"content": "item 1","start": "2017-08-22T04:00:00","end": "2017-08-22T04:30:00"},
        //     {"id": 2,"group":0,"content": "item 1","start": "2017-08-22T06:00:00","end": "2017-08-22T10:30:00"},
        //     {"id": 3,"group":1,"content": "item 1","start": "2017-08-22T04:00:00","end": "2017-08-22T04:30:00"},
        //     {"id": 4,"group":1,"content": "item 1","start": "2017-08-22T15:00:00","end": "2017-08-22T15:30:00"},
        // ]);
        var items = new vis.DataSet(timelines);

        // Configuration for the Timeline
        var options = {
            // height: '200px',
            moment: function(date) {
                return vis.moment(date).utcOffset('+07:00');
            },
            min: min,                // lower limit of visible range
            max: max,                // upper limit of visible range
            zoomMin: 5,
            groupOrder: function (a, b) {
                return a.value - b.value;
            }
        };

        // Create a Timeline
        this.availableUserTimeline = new vis.Timeline(container, items,groups ,options);

        this.onAvailableUserSelectTimeline();
    },
    onAvailableUserSelectTimeline: function(){

        this.availableUserTimeline.on('select', function (properties) {
            var eventId = parseInt(properties.items,10);
            if(eventId > 0){
                $.ajax({
                    type: "GET",
                    url: "/backend/schedule/getUserByEvent?eventId=" + eventId,
                    success: function (data) {
                        if(data.status == 1){

                            $('#selectedUserAvailabel').html(' <option>--Please select date first</option>');
                            $('#selectedUserAvailabel').select2({
                                data: data.result,
                                allowClear: true
                            });

                            $('#step4').fadeIn('slow');

                            $('html, body').animate({
                                scrollTop: ($('#step4').offset().top)
                            },500);
                        }
                        else{
                            $('#step4').fadeOut('slow');
                        }
                    }
                });

            }
        });
    },

    init: function(){
        $('#step2').hide();
        $('#step3').hide();
        $('#step4').hide();
        // $('select').select2();
        $('#datepicker').datepicker({
            todayHighlight: true,
        }).on('changeDate', function(e) {
            // `e` here contains the extra attributes
            var data = e.format();
            $.ajax({
                type: "GET",
                url: "/backend/schedule/getUserByDate?date="+data,
                success: function(data)
                {
                    if(data.status == 1){
                        $('#ddUser').select2({
                            data: data.result,
                            allowClear: true
                        });
                    }
                    else{
                        // $('#ddUser').select2('destroy');
                        $('#ddUser').html(' <option>--Please select date first</option>');
                    }
                }
            });
        });

        this.onSelectUser();
        this.onSelectCalendar();
        // this.onShowTimeLine();
        // this.onSelectTimeline();

        $(document).on('click', '#addMoreTeacher', function(e) {
            var element = $('.teacherForm div').first();
            console.log(element);
            $('.teacherForm').append(element.clone());

            var last = $('.teacherForm div').last();
            $(last).find('#addMoreTeacher').attr('id','removeTeacher').html('<span class="glyphicon glyphicon-minus" aria-hidden="true"></span>Remove teacher')
        });

        $(document).on('click', '#removeTeacher', function(e) {

            $(this).parents('.form-group').remove();

        });
    }
}
Home.init();