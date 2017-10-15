/**
 * Created by eric on 8/22/17.
 */
var Home = {

    timeline: '',
    availableUserTimeline: '',
    data: '',
    list_id: [],
    schedule_ids: [],

    onSelectCalendar: function(){
        $('#datepicker').datepicker();
        $('#datepicker').on('changeDate', function() {

            $('#my_hidden_input').val(
                $('#datepicker').datepicker('getFormattedDate')
            );

            if($('#ddUser').val() > 0){
                var teacher_id = $('#ddUser').val();
                var selectedDate = $('#my_hidden_input').val();
                console.log(selectedDate);
                //hide timeline
                //Home.onShowTimeLine(teacher_id,selectedDate);
            }

            $('#step2').fadeOut();
            $('#step3').hide();
            $('#step4').hide();

            // $('html, body').animate({
            //     scrollTop: ($('#step1').offset().top)
            // },500)  ;
        });
    },

    onSelectUser: function () {
        this.schedule_ids = [];
        this.list_id = [];
        $('#ddUser').on('change',function(e){

            var teacher_id = $(this).val();
            if($(this).val() > 0){
                var selectedDate = $('#my_hidden_input').val();
                //show timeline
                Home.onShowTimeLine(teacher_id,selectedDate);

                $('#step3').hide();
            }
            else{
                //hide timeline
                //Home.timeline.destroy();
            }

        });
    },
    onShowTimeLine: function(teacherId,dateSelected){
        this.schedule_ids = [];
        this.list_id = [];
        $('#step2').show();
        $.ajax({
            type: "GET",
            url: "/backend/schedule/getUserTimeline?teacher_id="+teacherId+'&date='+dateSelected,
            beforeSend: function( xhr ) {
                // Show full page LoadingOverlay
                $.LoadingOverlay("show");
            },
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
                    html += '<div class="number">3</div>';
                    html += '<div class="title_text">Select the lesson to be replace</div>';
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
                            if (typeof data.time_data[j].required.classes != 'undefined') {
                                for (var c = 0; c < data.time_data[j].required.classes.length; c++) {
                                    if (k == data.time_data[j].required.classes[c].slot[0]) {
                                        html += '<td colspan="' + data.time_data[j].required.classes[c].slot.length + '">';
                                        html += '<b>' + data.time_data[j].required.classes[c].lesson + '</b>';
                                        html += '<p>' + data.time_data[j].required.classes[c].class + '</p>';
                                        html += '<table class="time_slot-child">';
                                        html += '<tbody>';
                                        html += '<tr>';
                                        for (var l = 0; l < data.time_data[j].required.classes[c].slot.length; l++) {
                                            html += '<td class="action show-step4" data-id="' + data.time_data[j].required.classes[c].slot[l] + '" data-scheduleid="'+data.time_data[j].required.classes[c].id+'">&nbsp;</td>';
                                        }
                                        html += '</tr>';
                                        html += '</tbody>';
                                        html += '</table>';
                                        html += '</td>';
                                        k = k + data.time_data[j].required.classes[c].slot.length;
                                    }
                                    if (typeof data.time_data[j].required.paired != 'undefined') {
                                        for (var p = 0; p < data.time_data[j].required.paired.length; p++) {
                                            if (k == data.time_data[j].required.paired[p].slot[0]) {
                                                html += '<td colspan="' + data.time_data[j].required.paired[p].slot.length + '">';
                                                html += '<b>' + data.time_data[j].required.paired[p].lesson + '</b>';
                                                html += '<p>' + data.time_data[j].required.paired[p].class + '</p>';
                                                html += '<table class="time_slot-child">';
                                                html += '<tbody>';
                                                html += '<tr>';
                                                for (var l = 0; l < data.time_data[j].required.paired[p].slot.length; l++) {
                                                    html += '<td class="paired confirm" data-id="' + data.time_data[j].required.paired[p].slot[l] + '" data-scheduleid="'+data.time_data[j].required.paired[p].id+'">&nbsp;</td>';
                                                }
                                                html += '</tr>';
                                                html += '</tbody>';
                                                html += '</table>';
                                                html += '</td>';
                                                k = k + data.time_data[j].required.paired[p].slot.length;
                                            }
                                            if (typeof data.time_data[j].required.substituted != 'undefined') {
                                                for (var s = 0; s < data.time_data[j].required.substituted.length; s++) {
                                                    if (k == data.time_data[j].required.substituted[s].slot[0]) {
                                                        html += '<td colspan="' + data.time_data[j].required.substituted[s].slot.length + '">';
                                                        html += '<b>' + data.time_data[j].required.substituted[s].lesson + '</b>';
                                                        html += '<p>' + data.time_data[j].required.substituted[s].class + '</p>';
                                                        html += '<table class="time_slot-child">';
                                                        html += '<tbody>';
                                                        html += '<tr>';
                                                        for (var l = 0; l < data.time_data[j].required.substituted[s].slot.length; l++) {
                                                            html += '<td class="substituted show-step4" data-id="' + data.time_data[j].required.substituted[s].slot[l] + '" data-scheduleid="'+data.time_data[j].required.substituted[s].id+'">&nbsp;</td>';
                                                        }
                                                        html += '</tr>';
                                                        html += '</tbody>';
                                                        html += '</table>';
                                                        html += '</td>';
                                                        k = k + data.time_data[j].required.substituted[s].slot.length;
                                                    }
                                                    if (typeof data.time_data[j].required.red != 'undefined') {
                                                        for (var r = 0; r < data.time_data[j].required.red.length; r++) {
                                                            if (k == data.time_data[j].required.red[r].slot[0]) {
                                                                html += '<td colspan="' + data.time_data[j].required.red[r].slot.length + '">';
                                                                html += '<b>' + data.time_data[j].required.red[r].lesson + '</b>';
                                                                html += '<p>' + data.time_data[j].required.red[r].class + '</p>';
                                                                html += '<table class="time_slot-child">';
                                                                html += '<tbody>';
                                                                html += '<tr>';
                                                                for (var l = 0; l < data.time_data[j].required.red[r].slot.length; l++) {
                                                                    html += '<td class="red show-step4" data-id="' + data.time_data[j].required.red[r].slot[l] + '" data-scheduleid="'+data.time_data[j].required.red[r].id+'">&nbsp;</td>';
                                                                }
                                                                html += '</tr>';
                                                                html += '</tbody>';
                                                                html += '</table>';
                                                                html += '</td>';
                                                                k = k + data.time_data[j].required.red[r].slot.length;
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            if (typeof data.time_data[j].required.paired != 'undefined') {
                                for (var p = 0; p < data.time_data[j].required.paired.length; p++) {
                                    if (k == data.time_data[j].required.paired[p].slot[0]) {
                                        html += '<td colspan="' + data.time_data[j].required.paired[p].slot.length + '">';
                                        html += '<b>' + data.time_data[j].required.paired[p].lesson + '</b>';
                                        html += '<p>' + data.time_data[j].required.paired[p].class + '</p>';
                                        html += '<table class="time_slot-child">';
                                        html += '<tbody>';
                                        html += '<tr>';
                                        for (var l = 0; l < data.time_data[j].required.paired[p].slot.length; l++) {
                                            html += '<td class="paired confirm" data-id="' + data.time_data[j].required.paired[p].slot[l] + '" data-scheduleid="'+data.time_data[j].required.paired[p].id+'">&nbsp;</td>';
                                        }
                                        html += '</tr>';
                                        html += '</tbody>';
                                        html += '</table>';
                                        html += '</td>';
                                        k = k + data.time_data[j].required.paired[p].slot.length;
                                    }
                                    if (typeof data.time_data[j].required.classes != 'undefined') {
                                        for (var c = 0; c < data.time_data[j].required.classes.length; c++) {
                                            if (k == data.time_data[j].required.classes[c].slot[0]) {
                                                html += '<td colspan="' + data.time_data[j].required.classes[c].slot.length + '">';
                                                html += '<b>' + data.time_data[j].required.classes[c].lesson + '</b>';
                                                html += '<p>' + data.time_data[j].required.classes[c].class + '</p>';
                                                html += '<table class="time_slot-child">';
                                                html += '<tbody>';
                                                html += '<tr>';
                                                for (var l = 0; l < data.time_data[j].required.classes[c].slot.length; l++) {
                                                    html += '<td class="action show-step4" data-id="' + data.time_data[j].required.classes[c].slot[l] + '" data-scheduleid="'+data.time_data[j].required.classes[c].id+'">&nbsp;</td>';
                                                }
                                                html += '</tr>';
                                                html += '</tbody>';
                                                html += '</table>';
                                                html += '</td>';
                                                k = k + data.time_data[j].required.classes[c].slot.length;
                                            }
                                            if (typeof data.time_data[j].required.substituted != 'undefined') {
                                                for (var s = 0; s < data.time_data[j].required.substituted.length; s++) {
                                                    if (k == data.time_data[j].required.substituted[s].slot[0]) {
                                                        html += '<td colspan="' + data.time_data[j].required.substituted[s].slot.length + '">';
                                                        html += '<b>' + data.time_data[j].required.substituted[s].lesson + '</b>';
                                                        html += '<p>' + data.time_data[j].required.substituted[s].class + '</p>';
                                                        html += '<table class="time_slot-child">';
                                                        html += '<tbody>';
                                                        html += '<tr>';
                                                        for (var l = 0; l < data.time_data[j].required.substituted[s].slot.length; l++) {
                                                            html += '<td class="substituted show-step4" data-id="' + data.time_data[j].required.substituted[s].slot[l] + '" data-scheduleid="'+data.time_data[j].required.substituted[s].id+'">&nbsp;</td>';
                                                        }
                                                        html += '</tr>';
                                                        html += '</tbody>';
                                                        html += '</table>';
                                                        html += '</td>';
                                                        k = k + data.time_data[j].required.substituted[s].slot.length;
                                                    }
                                                    if (typeof data.time_data[j].required.red != 'undefined') {
                                                        for (var r = 0; r < data.time_data[j].required.red.length; r++) {
                                                            if (k == data.time_data[j].required.red[r].slot[0]) {
                                                                html += '<td colspan="' + data.time_data[j].required.red[r].slot.length + '">';
                                                                html += '<b>' + data.time_data[j].required.red[r].lesson + '</b>';
                                                                html += '<p>' + data.time_data[j].required.red[r].class + '</p>';
                                                                html += '<table class="time_slot-child">';
                                                                html += '<tbody>';
                                                                html += '<tr>';
                                                                for (var l = 0; l < data.time_data[j].required.red[r].slot.length; l++) {
                                                                    html += '<td class="red show-step4" data-id="' + data.time_data[j].required.red[r].slot[l] + '" data-scheduleid="'+data.time_data[j].required.red[r].id+'">&nbsp;</td>';
                                                                }
                                                                html += '</tr>';
                                                                html += '</tbody>';
                                                                html += '</table>';
                                                                html += '</td>';
                                                                k = k + data.time_data[j].required.red[r].slot.length;
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            if (typeof data.time_data[j].required.substituted != 'undefined') {
                                for (var s = 0; s < data.time_data[j].required.substituted.length; s++) {
                                    if (k == data.time_data[j].required.substituted[s].slot[0]) {
                                        html += '<td colspan="' + data.time_data[j].required.substituted[s].slot.length + '">';
                                        html += '<b>' + data.time_data[j].required.substituted[s].lesson + '</b>';
                                        html += '<p>' + data.time_data[j].required.substituted[s].class + '</p>';
                                        html += '<table class="time_slot-child">';
                                        html += '<tbody>';
                                        html += '<tr>';
                                        for (var l = 0; l < data.time_data[j].required.substituted[s].slot.length; l++) {
                                            html += '<td class="substituted show-step4" data-id="' + data.time_data[j].required.substituted[s].slot[l] + '" data-scheduleid="'+data.time_data[j].required.substituted[s].id+'">&nbsp;</td>';
                                        }
                                        html += '</tr>';
                                        html += '</tbody>';
                                        html += '</table>';
                                        html += '</td>';
                                        k = k + data.time_data[j].required.substituted[s].slot.length;
                                    }
                                    if (typeof data.time_data[j].required.classes != 'undefined') {
                                        for (var c = 0; c < data.time_data[j].required.classes.length; c++) {
                                            if (k == data.time_data[j].required.classes[c].slot[0]) {
                                                html += '<td colspan="' + data.time_data[j].required.classes[c].slot.length + '">';
                                                html += '<b>' + data.time_data[j].required.classes[c].lesson + '</b>';
                                                html += '<p>' + data.time_data[j].required.classes[c].class + '</p>';
                                                html += '<table class="time_slot-child">';
                                                html += '<tbody>';
                                                html += '<tr>';
                                                for (var l = 0; l < data.time_data[j].required.classes[c].slot.length; l++) {
                                                    html += '<td class="action show-step4" data-id="' + data.time_data[j].required.classes[c].slot[l] + '" data-scheduleid="'+data.time_data[j].required.classes[c].id+'">&nbsp;</td>';
                                                }
                                                html += '</tr>';
                                                html += '</tbody>';
                                                html += '</table>';
                                                html += '</td>';
                                                k = k + data.time_data[j].required.classes[c].slot.length;
                                            }
                                            if (typeof data.time_data[j].required.paired != 'undefined') {
                                                for (var p = 0; p < data.time_data[j].required.paired.length; p++) {
                                                    if (k == data.time_data[j].required.paired[p].slot[0]) {
                                                        html += '<td colspan="' + data.time_data[j].required.paired[p].slot.length + '">';
                                                        html += '<b>' + data.time_data[j].required.paired[p].lesson + '</b>';
                                                        html += '<p>' + data.time_data[j].required.paired[p].class + '</p>';
                                                        html += '<table class="time_slot-child">';
                                                        html += '<tbody>';
                                                        html += '<tr>';
                                                        for (var l = 0; l < data.time_data[j].required.paired[p].slot.length; l++) {
                                                            html += '<td class="paired confirm" data-id="' + data.time_data[j].required.paired[p].slot[l] + '" data-scheduleid="'+data.time_data[j].required.paired[p].id+'">&nbsp;</td>';
                                                        }
                                                        html += '</tr>';
                                                        html += '</tbody>';
                                                        html += '</table>';
                                                        html += '</td>';
                                                        k = k + data.time_data[j].required.paired[p].slot.length;
                                                    }
                                                    if (typeof data.time_data[j].required.red != 'undefined') {
                                                        for (var r = 0; r < data.time_data[j].required.red.length; r++) {
                                                            if (k == data.time_data[j].required.red[r].slot[0]) {
                                                                html += '<td colspan="' + data.time_data[j].required.red[r].slot.length + '">';
                                                                html += '<b>' + data.time_data[j].required.red[r].lesson + '</b>';
                                                                html += '<p>' + data.time_data[j].required.red[r].class + '</p>';
                                                                html += '<table class="time_slot-child">';
                                                                html += '<tbody>';
                                                                html += '<tr>';
                                                                for (var l = 0; l < data.time_data[j].required.red[r].slot.length; l++) {
                                                                    html += '<td class="red show-step4" data-id="' + data.time_data[j].required.red[r].slot[l] + '" data-scheduleid="'+data.time_data[j].required.red[r].id+'">&nbsp;</td>';
                                                                }
                                                                html += '</tr>';
                                                                html += '</tbody>';
                                                                html += '</table>';
                                                                html += '</td>';
                                                                k = k + data.time_data[j].required.red[r].slot.length;
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            if (typeof data.time_data[j].required.red != 'undefined') {
                                for (var r = 0; r < data.time_data[j].required.red.length; r++) {
                                    if (k == data.time_data[j].required.red[r].slot[0]) {
                                        html += '<td colspan="' + data.time_data[j].required.red[r].slot.length + '">';
                                        html += '<b>' + data.time_data[j].required.red[r].lesson + '</b>';
                                        html += '<p>' + data.time_data[j].required.red[r].class + '</p>';
                                        html += '<table class="time_slot-child">';
                                        html += '<tbody>';
                                        html += '<tr>';
                                        for (var l = 0; l < data.time_data[j].required.red[r].slot.length; l++) {
                                            html += '<td class="red show-step4" data-id="' + data.time_data[j].required.red[r].slot[l] + '" data-scheduleid="'+data.time_data[j].required.red[r].id+'">&nbsp;</td>';
                                        }
                                        html += '</tr>';
                                        html += '</tbody>';
                                        html += '</table>';
                                        html += '</td>';
                                        k = k + data.time_data[j].required.red[r].slot.length;
                                    }
                                    if (typeof data.time_data[j].required.classes != 'undefined') {
                                        for (var c = 0; c < data.time_data[j].required.classes.length; c++) {
                                            if (k == data.time_data[j].required.classes[c].slot[0]) {
                                                html += '<td colspan="' + data.time_data[j].required.classes[c].slot.length + '">';
                                                html += '<b>' + data.time_data[j].required.classes[c].lesson + '</b>';
                                                html += '<p>' + data.time_data[j].required.classes[c].class + '</p>';
                                                html += '<table class="time_slot-child">';
                                                html += '<tbody>';
                                                html += '<tr>';
                                                for (var l = 0; l < data.time_data[j].required.classes[c].slot.length; l++) {
                                                    html += '<td class="action show-step4" data-id="' + data.time_data[j].required.classes[c].slot[l] + '" data-scheduleid="'+data.time_data[j].required.classes[c].id+'">&nbsp;</td>';
                                                }
                                                html += '</tr>';
                                                html += '</tbody>';
                                                html += '</table>';
                                                html += '</td>';
                                                k = k + data.time_data[j].required.classes[c].slot.length;
                                            }
                                            if (typeof data.time_data[j].required.paired != 'undefined') {
                                                for (var p = 0; p < data.time_data[j].required.paired.length; p++) {
                                                    if (k == data.time_data[j].required.paired[p].slot[0]) {
                                                        html += '<td colspan="' + data.time_data[j].required.paired[p].slot.length + '">';
                                                        html += '<b>' + data.time_data[j].required.paired[p].lesson + '</b>';
                                                        html += '<p>' + data.time_data[j].required.paired[p].class + '</p>';
                                                        html += '<table class="time_slot-child">';
                                                        html += '<tbody>';
                                                        html += '<tr>';
                                                        for (var l = 0; l < data.time_data[j].required.paired[p].slot.length; l++) {
                                                            html += '<td class="paired confirm" data-id="' + data.time_data[j].required.paired[p].slot[l] + '" data-scheduleid="'+data.time_data[j].required.paired[p].id+'">&nbsp;</td>';
                                                        }
                                                        html += '</tr>';
                                                        html += '</tbody>';
                                                        html += '</table>';
                                                        html += '</td>';
                                                        k = k + data.time_data[j].required.paired[p].slot.length;
                                                    }
                                                    if (typeof data.time_data[j].required.substituted != 'undefined') {
                                                        for (var s = 0; s < data.time_data[j].required.substituted.length; s++) {
                                                            if (k == data.time_data[j].required.substituted[s].slot[0]) {
                                                                html += '<td colspan="' + data.time_data[j].required.substituted[s].slot.length + '">';
                                                                html += '<b>' + data.time_data[j].required.substituted[s].lesson + '</b>';
                                                                html += '<p>' + data.time_data[j].required.substituted[s].class + '</p>';
                                                                html += '<table class="time_slot-child">';
                                                                html += '<tbody>';
                                                                html += '<tr>';
                                                                for (var l = 0; l < data.time_data[j].required.substituted[s].slot.length; l++) {
                                                                    html += '<td class="substituted show-step4" data-id="' + data.time_data[j].required.substituted[s].slot[l] + '" data-scheduleid="'+data.time_data[j].required.substituted[s].id+'">&nbsp;</td>';
                                                                }
                                                                html += '</tr>';
                                                                html += '</tbody>';
                                                                html += '</table>';
                                                                html += '</td>';
                                                                k = k + data.time_data[j].required.substituted[s].slot.length;
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
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

                    $('html, body').animate({
                        scrollTop: ($('#step2').offset().top)
                    },500);

                }

                // Hide it after 3 seconds
                setTimeout(function(){
                    $.LoadingOverlay("hide");
                }, 100);
            }
        });
    },
    searchAvailableTeacher: function(){

        // var list_id = this.list_id;
        $(".container-step3").on("click", "td.show-step4",function(event) {
            event.stopPropagation();
            //request ajax
            if($(this).hasClass('confirm') || $(this).hasClass('red')){
                return false;
            }
            var optionAssigned = false;
            if($(this).hasClass('red')){
                var optionAssigned = true;
            }
            var selection = $(this).data('scheduleid');
            var selectedDate = $('#my_hidden_input').val();
            var $this = $(this);

            if ($.inArray(selection, Home.schedule_ids) != -1)
            {
                Home.schedule_ids.splice($.inArray(selection, Home.schedule_ids),1);
            }
            else{
                Home.schedule_ids.push(selection);
            }
            // console.log('Schedule IDs:' +Home.schedule_ids);

            $.ajax({
                url : "/backend/schedule/getAvailableUserByEvents",
                type: "GET",
                data: {eventIds: Home.schedule_ids,date:selectedDate,optionAssigned: optionAssigned},
                beforeSend: function( xhr ) {
                    // Show full page LoadingOverlay
                    $.LoadingOverlay("show");
                },
                success: function(result, textStatus, jqXHR)
                {
                    var data = Home.data;
                    // var data1 = {
                    //
                    //     "time_data": [{"required":{"teacher":"Mr Timothy","status":"unavaliable","content":"relif made","number":"99","classes":[{"slot":[4,5],"lesson":"C Phy"},{"slot":[10,11],"lesson":"C Phy"}]}}]
                    // };
                    var data1= result.result.data;
                    if(result.status ==1){
                        var html = '<div class="step4">';
                        html += '<div class="title clearfix">';
                        html += '<div class="number">3</div>';
                        html += '<div class="title_text">Select a substitute teacher</div>';
                        html += '</div>';
                        html += '<table class="time_slot-selected">';
                        html += '<thead>';
                        html += '<tr>';
                        html += '<th colspan="2"></th>';
                        for(var i = 0; i < data.time_slot.length; i++) {
                            html += '<th>';
                            html += '<p>'+data.time_slot[i].slot+'</p>';
                            html += '</th>';
                        }
                        html += '</tr>';
                        html += '</thead>';
                        html += '<tbody>';
                        for(var j = 0; j < data1.time_data.length; j++) {
                            html += '<tr>';
                            html += '<td>';
                            html += '<b>'+data1.time_data[j].required.number+'</b>';
                            html += '<p>'+data1.time_data[j].required.content+'</p>';
                            html += '</td>';
                            html += '<td>';
                            html += '<b>'+data1.time_data[j].required.teacher+'</b>';
                            html += '</td>';
                            for(var k = 1; k <= data.time_slot.length; k++) {
                                for(var c = 0; c < data1.time_data[j].required.classes.length; c++) {
                                    if(k == data1.time_data[j].required.classes[c].slot[0]) {
                                        html += '<td colspan="'+data1.time_data[j].required.classes[c].slot.length+'">';
                                        html += '<table class="time_slot-hasclass">';
                                        html += '<tbody>';
                                        html += '<tr>';
                                        html += '<td><b>'+data1.time_data[j].required.classes[c].lesson+'</b></td>'
                                        html += '</tr>';
                                        html += '</tbody>';
                                        html += '</table>';
                                        html += '</td>';
                                        k = k + data1.time_data[j].required.classes[c].slot.length;
                                    }
                                }

                                if(k <= data.time_slot.length) {
                                    html += '<td data-slot="'+k+'" data-selected-schedule-id="'+$this.data('scheduleid')+'" data-teacherid="'+data1.time_data[j].required.teacher_id+'"></td>';
                                }

                            }
                            html += '</tr>';
                        }
                        html += '</tbody>';
                        html += '</table>';

                        $(".container-step4").html(html);


                    }
                    else{
                        $(".step4").html('');
                        // alert('No available teacher');
                    }
                    // var list_id = [];
                    var id = $this.data("id");

                    if($this.hasClass('active')) {
                        $this.removeClass('active');
                        if($("td.show-step4.active").length <= 0) {
                            $(".step4").hide();
                        }
                        Home.list_id.splice($.inArray(id, Home.list_id),1);
                        for(var h = 0; h < Home.list_id.length; h++) {
                            $(".time_slot-selected > tbody > tr > td[data-slot='"+Home.list_id[h]+"']").addClass('active');
                        }
                    } else {
                        $this.addClass('active');
                        $(".step4").show();
                        Home.list_id.push(id);
                        for(var l = 0; l < Home.list_id.length; l++) {
                            $(".time_slot-selected > tbody > tr > td[data-slot='"+Home.list_id[l]+"']").addClass('active');
                        }
                    }

                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    console.log( errorThrown );
                }
            });

            $('#step3').fadeIn();
            $('html, body').animate({
                scrollTop: ($('#step3').offset().top)
            },500);

            // Hide it after 3 seconds
            setTimeout(function(){
                $.LoadingOverlay("hide");
            }, 100);

        });



    },

    init: function(){
        $('#step2').hide();
        $('#step3').hide();
        $('#step4').hide();
        // $('select').select2();
        $('#datepicker').datepicker({
            todayBtn: "linked",
            daysOfWeekHighlighted:[1,2,3,4,5],
        }).on('changeDate', function(e) {
            $('#ddUser').select2();
            // `e` here contains the extra attributes
            var data = e.format();
            $.ajax({
                type: "GET",
                url: "/backend/schedule/getUserByDate?date="+data,
                success: function(data)
                {
                    if(data.status == 1){
                        $('#ddUser').select2('destroy');
                        $("#ddUser").html("<option>Please select teacher on this date</option>");

                        $('#ddUser').select2({
                            data: data.result
                        });
                    }
                    else{
                        // $('#ddUser').select2('destroy');
                        // $("#ddUser").html("");
                        $('#ddUser').html(' <option>--Please select date first</option>');
                    }
                }
            });
        });

        $(".container-step4").on("click", "td.active", function(event) {

            $("#step5").modal('toggle', $(this));
        });
        //
        // $(".sms").click(function(event) {
        //     event.stopPropagation();
        // });
        //
        $(document).on('click','.btn-close',function() {
            $("#step5").modal('toggle', $(this));
        });

        $(".txt-sms").on("keydown keyup", function() {
            var length_sms = $(".txt-sms").val().length;
            $(".ch-sms span").html(length_sms);
        });

        $(".txt-mark").on("keydown keyup", function() {
            var length_mark = $(".txt-mark").val().length;
            $(".ch-mark span").html(length_mark);
        });

        $(".container-step3").on("click", "td.confirm", function() {

            var scheduleid = $(this).data('scheduleid');
            var selectedDate = $('#my_hidden_input').val();

            $('#confirm input[name=scheduleid]').val(scheduleid);
            $('#confirm input[name=selectedDate]').val(selectedDate);

            $("#confirm").modal("show");
        });

        $(".container-step3").on("click", "td.red", function() {

            var scheduleid = $(this).data('scheduleid');
            var selectedDate = $('#my_hidden_input').val();

            $('#confirm input[name=scheduleid]').val(scheduleid);
            $('#confirm input[name=selectedDate]').val(selectedDate);

            $("#confirm").modal("show");
        });

        // $(window).on("load",function() {
        //     var length_sms = $(".txt-sms").val().length;
        //     var length_mark = $(".txt-mark").val().length;
        //     $(".ch-sms span").html(length_sms);
        //     $(".ch-mark span").html(length_mark);
        // });

        this.onSelectUser();
        this.onSelectCalendar();
        this.searchAvailableTeacher();

        $("#step5").on("show.bs.modal", function(e) {

            var teacher_id = $(e.relatedTarget).data();
            var selectedDate = $('#my_hidden_input').val();

            $.ajax({
                url : "/backend/schedule/assign-form-modal",
                type: "GET",
                data: {schedule_ids: Home.schedule_ids,teacher_id:teacher_id,selected_date: selectedDate},
                success: function(result, textStatus, jqXHR)
                {
                    $(".modal-body").html(result);

                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    console.log( errorThrown );
                }
            });
            return;
        });

        $("#confirm").on("hide.bs.modal", function(e) {
            $('#confirm input[name=scheduleid]').val('');
            $('#confirm input[name=selectedDate]').val('');
        });


    }
}
Home.init();