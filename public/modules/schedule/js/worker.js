/**
 * Created by eric on 8/22/17.
 */
var Home = {

    timeline: '',
    availableUserTimeline: '',
    data: '',
    list_id: [],
    schedule_ids: [],
    leaveItems:[],

    onSelectCalendar: function(){
        $('#datepicker').datepicker();
        $('#datepicker').on('changeDate', function(e) {

            $('#my_hidden_input').val(
                $('#datepicker').datepicker('getFormattedDate')
            );
            var teacher_id = $('#teacherId').val();
            if(teacher_id > 0){
                var selectedDate = $('#my_hidden_input').val();
                //show timeline
                Home.onShowTimeLine(teacher_id,selectedDate);
                //get leaves items
                Home.onShowLeaveItems(e);

                Home.onSubmitCancelLeave();

                $('#step3').show();
                $('#step4').show();
            }
        });
    },

    onSelectUser: function () {
        $('#ddUser').on('change',function(e){

        });
    },
    onShowLeaveItems: function(e) {
        // `e` here contains the extra attributes
        var data = e.format();
        $.ajax({
            type: "GET",
            url: "/backend/schedule/leave/getLeavesByDate?date="+data,
            beforeSend: function( xhr ) {
                // Show full page LoadingOverlay
                $.LoadingOverlay("show");
            },
            success: function(data)
            {
                if(data.status == 1){
                    if(data.result.length > 0){
                        $('.leaves-box ul').html('');
                        $.each(data.result, function( index, value ) {
                            // console.log( value);
                            var jobStatus = '(Not verify)';
                            if(value.status == 1){
                                jobStatus = '(Accepted)';
                            }else if(value.status == 2){
                                jobStatus = '(Rejected)';
                            }

                            var cancelElement = '<a href="#" data-id="' + value.id+ '" class="btn btn-primary" data-toggle="modal" data-target="#cancelModal">Cancel</a>';

                            $('.leaves-box ul').append('<li id="leave_'+value.id+'">'+value.teacher_name+' applied for leave on '+value.start_date+' to '+value.end_date+' '+jobStatus+', '+cancelElement+'</li>');
                        });
                    }
                    else{
                        $('.leaves-box ul').html('');
                        $('.leaves-box ul').append('<li>There is no relief assigned today</li>');
                        // $('.assignment-box').fadeOut();
                    }
                }
                else{
                    console.log('empty leave item');
                }
                $.LoadingOverlay("hide");
            }
        });
    },
    onSubmitCancelLeave: function(){
        $("#cancelForm").on("submit", function(){
            //Code: Action (like ajax...)
            var data = $(this).serialize();

            $.ajax({
                type: "POST",
                url: $(this).attr('action'),
                data: data,
                beforeSend: function( xhr ) {
                    // Show full page LoadingOverlay
                    $.LoadingOverlay("show");
                    $('#cancelModal').modal('hide');
                },
                success: function(result)
                {
                    console.log(result);

                    if(result.status == 1){
                        $('.leaves-box').html('');
                    }
                    else{
                        console.log('empty leave item');
                    }
                    $.LoadingOverlay("hide");
                }
            });
            return false;
        })
    },
    onShowTimeLine: function(teacherId,dateSelected){
        this.schedule_ids = [];
        this.list_id = [];
        $('#step2').show();

        $.ajax({
            type: "GET",
            url: "/backend/schedule/getLeaveUserTimeline?teacher_id="+teacherId+'&date='+dateSelected,
            success: function(result)
            {
                if(result.status==1){
                    // var data = {
                    //     "time_slot": [{"slot":"0","start":"07:30","end":"08:00"},{"slot":"1","start":"08:00","end":"08:30"},{"slot":"2","start":"08:30","end":"09:00"},{"slot":"3","start":"09:00","end":"09:30"},{"slot":"4","start":"09:30","end":"10:00"},{"slot":"5","start":"10:00","end":"10:30"},{"slot":"6","start":"07:30","end":"08:00"},{"slot":"7","start":"08:00","end":"08:30"},{"slot":"8","start":"08:30","end":"09:00"},{"slot":"9","start":"09:00","end":"09:30"},{"slot":"10","start":"09:30","end":"10:00"},{"slot":"11","start":"10:00","end":"10:30"}],
                    //     "time_data": [{"required":{"teacher":"Ms Germain Kang","classes":[{"class":["2E4","3E3"],"lesson":"SC","slot":[1,2,3],"start":"08:00","end":"09:00","status":"unavaliable","content":"relif made","number":"99"},{"class":["2E4","3E3"],"lesson":"SC","slot":[9,10,11],"start":"08:00","end":"09:00","status":"unavaliable","content":"relif made","number":"99"}]},"paired":[{"class":"4N2","lesson":"C Chem","slot":[4,5],"start":"09:00","end":"10:30"},{"class":"4N2","lesson":"C Chem","slot":[6,7],"start":"09:00","end":"10:30"}]}]
                    // };
                    var data = result.result.data;
                    Home.data = data;

                    var html = '<div class="step3">';
                    html += '<div class="title clearfix">';
                    html += '<div class="number">2</div>';
                    html += '<div class="title_text">Lesson on day</div>';
                    html += '</div>';
                    html += '<table class="time_slot">';
                    html += '<thead>';
                    html += '<tr>';
                    html += '<th></th>';
                    for (var i = 0; i < data.time_slot.length; i++) {
                        html += '<th>';
                        html += '<p>' + data.time_slot[i].start + '</p>';
                        html += '<p>' + data.time_slot[i].end + '</p>';
                        html += '<p class="lesson">' + data.time_slot[i].slot + '</p>';
                        html += '</th>';
                    }
                    html += '</tr>';
                    html += '</thead>';
                    html += '<tbody>';
                    for (var j = 0; j < data.time_data.length; j++) {
                        html += '<tr>';
                        html += '<td>';
                        html += '<b>' + data.time_data[j].required.teacher + '</b>';
                        html += '</td>';
                        for (var k = 1; k <= data.time_slot.length; k++) {
                            if (typeof data.time_data[j].required.classes != 'undefined' && data.time_data[j].required.classes.length > 0) {
                                for (var c = 0; c < data.time_data[j].required.classes.length; c++) {
                                    if (k == data.time_data[j].required.classes[c].slot[0]) {
                                        var action = (data.time_data[j].required.classes[c].flag == 'paired') ? 'confirm' : 'show-step4';

                                        html += '<td colspan="' + data.time_data[j].required.classes[c].slot.length + '">';
                                        html += '<b>' + data.time_data[j].required.classes[c].lesson + '</b>';
                                        html += '<p>' + data.time_data[j].required.classes[c].class + '</p>';
                                        html += '<table class="time_slot-child">';
                                        html += '<tbody>';
                                        html += '<tr>';
                                        for (var l = 0; l < data.time_data[j].required.classes[c].slot.length; l++) {
                                            html += '<td class="' + data.time_data[j].required.classes[c].flag + ' '+ action +'" data-id="' + data.time_data[j].required.classes[c].slot[l] + '" data-scheduleid="'+data.time_data[j].required.classes[c].id+'">&nbsp;</td>';
                                        }
                                        html += '</tr>';
                                        html += '</tbody>';
                                        html += '</table>';
                                        html += '</td>';
                                        k = k + data.time_data[j].required.classes[c].slot.length;
                                    }
                                }
                            }

                            if (k <= data.time_slot.length) {
                                html += '<td></td>';
                            }
                        }
                        html += '</tr>';
                    }

                    html += '</tbody>';
                    html += '</table>';

                    $(".container-step3").html(html);

                    $('#step2').show();

                    // $('html, body').animate({
                    //     scrollTop: ($('#step2').offset().top)
                    // },500);

                }
            }
        });
    },
    onSelectTimeline: function(){

        // $(".container-step3").on("click", "td.show-step4",function(event) {
        //     event.stopPropagation();
        //     $('#step3').fadeIn('slow');
        //     $('#step4').fadeIn('slow');
        //
        //     $('#input_scheduleId').val($(this).data('scheduleid'));
        //
        //     $('html, body').animate({
        //         scrollTop: ($('#step3').offset().top)
        //     },500);
        // });

        // $(".btn-pref .btn").click(function () {
        //     $(".btn-pref .btn").removeClass("btn-primary").addClass("btn-default");
        //     // $(".tab").addClass("active"); // instead of this do the below
        //     $(this).removeClass("btn-default").addClass("btn-primary");
        // });
        //
        // this.timeline.on('select', function (properties) {
        //     var eventId = parseInt(properties.items,10);
        //     if(eventId > 0){
        //         $('#step3').fadeIn('slow');
        //         $('#step4').fadeIn('slow');
        //
        //         $('html, body').animate({
        //             scrollTop: ($('#step3').offset().top)
        //         },500);
        //     }
        // });
    },


    init: function(){
        $('#step2').hide();
        $('#step3').hide();
        $('#step4').hide();
        $('.select').select2({ width: '100%' });

        $('#cancelModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget) // Button that triggered the modal
            var leaveId = button.data('id') // Extract info from data-* attributes
            // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
            // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
            var modal = $(this);

            modal.find('.leave_id').val(leaveId);
        })

        $('#datepicker').datepicker({
            todayBtn: "linked",
            calendarWeeks: true,
            daysOfWeekHighlighted:[1,2,3,4,5],
            daysOfWeekDisabled: [0,6],
            beforeShowDay: function(date){
                var d = date;
                var curr_date = d.getDate();
                var curr_month = d.getMonth() + 1; //Months are zero based
                var curr_year = d.getFullYear();
                var formattedDate = curr_date + "/" + curr_month + "/" + curr_year;
                var curr_week = moment(d, "MM-DD-YYYY").week();

                console.log(formattedDate);
                console.log(Home.leaveItems);

                var holidaysWeekNumber = [11,22,23,24,25,36,47,48,49,50,51,52];
                if(holidaysWeekNumber.indexOf(curr_week) >= 0 ){
                    return false;
                }

                if ($.inArray(formattedDate, Home.leaveItems) != -1){
                    return {
                        classes: 'activeDate'
                    };
                }
                return;
            }
        });


        this.onSelectCalendar();
        this.onSelectTimeline();

        $(document).on('click', '#addMoreTeacher', function(e) {
            var element = $('.teacherForm div').first();

            var optionValues = [];
            $('#selectedUserAvailabel option').each(function() {
                var value = $(this).val();
                var text = $(this).text();
                optionValues.push({key : value,text: text});
            });

            var teacherDropdownList = $("<select></select>").attr("name", 'replaceTeacherIds[]').attr('class','form-control select');
            $.each(optionValues, function (i, el) {
                teacherDropdownList.append("<option value='"+el.key+"'>" + el.text + "</option>");
            });

            var element = '<div class="form-group"><label for="name" class="col-md-4 control-label">Select Teacher</label><div class="col-md-5">'+teacherDropdownList.get(0).outerHTML+'</div><div class="col-md-3"><a id="removeTeacher" href="javascript:void(0)"><span class="glyphicon glyphicon-minus"></span>Remove Teacher</a></div></div>';
            $('.teacherForm').append(element);

            $('.select').select2();

            // var last = $('.teacherForm div').last();
            // $(last).find('#addMoreTeacher').attr('id','removeTeacher').html('<span class="glyphicon glyphicon-minus" aria-hidden="true"></span>Remove teacher')
        });

        $(document).on('click', '#removeTeacher', function(e) {

            $(this).parents('.form-group').remove();

        });

        $("#myTab a").click(function(e){
            e.preventDefault();
            $("#myTab a").removeClass('active').removeClass('btn-primary');
            $(this).addClass('btn-primary');

            $('.tab-pane').removeClass('active');

            $(this).tab('show').addClass('active');

            $('#absentType').val($(this).attr('id'));

            if($(this).attr('id') == 'partialDay'){
                $('#input_startTime').val($('#startTime').val());
                $('#input_endTime').val($('#endTime').val());
            }
            if($(this).attr('id') == 'prolonged'){
                $('#input_startDate').val($('#startDate').val());
                $('#input_endDate').val($('#endDate').val());
            }

        });

        $('#startTime').on('change', function (e) {
            $('#input_startTime').val($(this).val());
        });
        $('#endTime').on('change', function (e) {
            $('#input_endTime').val($(this).val());
        });

        $('#startDate').on('change', function (e) {
            $('#input_startDate').val($(this).val());
        });
        $('#endDate').on('change', function (e) {
            $('#input_endDate').val($(this).val());
        });
    }
}