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

            var teacher_id = $('#teacherId').val();

            if(teacher_id > 0){

                var selectedDate = $('#my_hidden_input').val();
                //show timeline
                Home.onShowTimeLine(teacher_id,selectedDate);

                $('#step3').show();
                $('#step4').show();
            }
        });
    },

    onSelectUser: function () {
        $('#ddUser').on('change',function(e){

        });
    },
    onShowTimeLine: function(teacherId,dateSelected){
        this.schedule_ids = [];
        this.list_id = [];
        $('#step2').show();

        $.ajax({
            type: "GET",
            url: "/backend/schedule/getUserTimeline?teacher_id="+teacherId+'&date='+dateSelected,
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

                    // var html = '<div class="step3">';
                    // html += '<div class="title clearfix">';
                    // html += '<div class="number">2</div>';
                    // html += '<div class="title_text">Select the lesson to be replace</div>';
                    // html += '</div>';
                    // html += '<table class="time_slot">';
                    // html += '<thead>';
                    // html += '<tr>';
                    // html += '<th></th>';
                    // for (var i = 0; i < data.time_slot.length; i++) {
                    //     html += '<th>';
                    //     html += '<p>' + data.time_slot[i].start + '</p>';
                    //     html += '<p>' + data.time_slot[i].end + '</p>';
                    //     html += '<p class="lesson">' + data.time_slot[i].slot + '</p>';
                    //     html += '</th>';
                    // }
                    // html += '</tr>';
                    // html += '</thead>';
                    // html += '<tbody>';
                    // for (var j = 0; j < data.time_data.length; j++) {
                    //     html += '<tr>';
                    //     html += '<td>';
                    //     html += '<b>' + data.time_data[j].required.teacher + '</b>';
                    //     html += '</td>';
                    //     for (var k = 1; k <= data.time_slot.length; k++) {
                    //         if (typeof data.time_data[j].required.classes != 'undefined') {
                    //             for (var c = 0; c < data.time_data[j].required.classes.length; c++) {
                    //                 if (k == data.time_data[j].required.classes[c].slot[0]) {
                    //                     html += '<td colspan="' + data.time_data[j].required.classes[c].slot.length + '">';
                    //                     html += '<b>' + data.time_data[j].required.classes[c].lesson + '</b>';
                    //                     html += '<p>' + data.time_data[j].required.classes[c].class + '</p>';
                    //                     html += '<table class="time_slot-child">';
                    //                     html += '<tbody>';
                    //                     html += '<tr>';
                    //                     for (var l = 0; l < data.time_data[j].required.classes[c].slot.length; l++) {
                    //                         html += '<td class="action show-step4" data-id="' + data.time_data[j].required.classes[c].slot[l] + '" data-scheduleid="'+data.time_data[j].required.classes[c].id+'">&nbsp;</td>';
                    //                     }
                    //                     html += '</tr>';
                    //                     html += '</tbody>';
                    //                     html += '</table>';
                    //                     html += '</td>';
                    //                     k = k + data.time_data[j].required.classes[c].slot.length;
                    //                 }
                    //                 if (typeof data.time_data[j].required.paired != 'undefined') {
                    //                     for (var p = 0; p < data.time_data[j].required.paired.length; p++) {
                    //                         if (k == data.time_data[j].required.paired[p].slot[0]) {
                    //                             html += '<td colspan="' + data.time_data[j].required.paired[p].slot.length + '">';
                    //                             html += '<b>' + data.time_data[j].required.paired[p].lesson + '</b>';
                    //                             html += '<p>' + data.time_data[j].required.paired[p].class + '</p>';
                    //                             html += '<table class="time_slot-child">';
                    //                             html += '<tbody>';
                    //                             html += '<tr>';
                    //                             for (var l = 0; l < data.time_data[j].required.paired[p].slot.length; l++) {
                    //                                 html += '<td class="paired confirm" data-id="' + data.time_data[j].required.paired[p].slot[l] + '" data-scheduleid="'+data.time_data[j].required.paired[p].id+'">&nbsp;</td>';
                    //                             }
                    //                             html += '</tr>';
                    //                             html += '</tbody>';
                    //                             html += '</table>';
                    //                             html += '</td>';
                    //                             k = k + data.time_data[j].required.paired[p].slot.length;
                    //                         }
                    //                         if (typeof data.time_data[j].required.substituted != 'undefined') {
                    //                             for (var s = 0; s < data.time_data[j].required.substituted.length; s++) {
                    //                                 if (k == data.time_data[j].required.substituted[s].slot[0]) {
                    //                                     html += '<td colspan="' + data.time_data[j].required.substituted[s].slot.length + '">';
                    //                                     html += '<b>' + data.time_data[j].required.substituted[s].lesson + '</b>';
                    //                                     html += '<p>' + data.time_data[j].required.substituted[s].class + '</p>';
                    //                                     html += '<table class="time_slot-child">';
                    //                                     html += '<tbody>';
                    //                                     html += '<tr>';
                    //                                     for (var l = 0; l < data.time_data[j].required.substituted[s].slot.length; l++) {
                    //                                         html += '<td class="substituted show-step4" data-id="' + data.time_data[j].required.substituted[s].slot[l] + '" data-scheduleid="'+data.time_data[j].required.substituted[s].id+'">&nbsp;</td>';
                    //                                     }
                    //                                     html += '</tr>';
                    //                                     html += '</tbody>';
                    //                                     html += '</table>';
                    //                                     html += '</td>';
                    //                                     k = k + data.time_data[j].required.substituted[s].slot.length;
                    //                                 }
                    //                                 if (typeof data.time_data[j].required.red != 'undefined') {
                    //                                     for (var r = 0; r < data.time_data[j].required.red.length; r++) {
                    //                                         if (k == data.time_data[j].required.red[r].slot[0]) {
                    //                                             html += '<td colspan="' + data.time_data[j].required.red[r].slot.length + '">';
                    //                                             html += '<b>' + data.time_data[j].required.red[r].lesson + '</b>';
                    //                                             html += '<p>' + data.time_data[j].required.red[r].class + '</p>';
                    //                                             html += '<table class="time_slot-child">';
                    //                                             html += '<tbody>';
                    //                                             html += '<tr>';
                    //                                             for (var l = 0; l < data.time_data[j].required.red[r].slot.length; l++) {
                    //                                                 html += '<td class="red show-step4" data-id="' + data.time_data[j].required.red[r].slot[l] + '" data-scheduleid="'+data.time_data[j].required.red[r].id+'">&nbsp;</td>';
                    //                                             }
                    //                                             html += '</tr>';
                    //                                             html += '</tbody>';
                    //                                             html += '</table>';
                    //                                             html += '</td>';
                    //                                             k = k + data.time_data[j].required.red[r].slot.length;
                    //                                         }
                    //                                     }
                    //                                 }
                    //                             }
                    //                         }
                    //                     }
                    //                 }
                    //             }
                    //         }
                    //         if (typeof data.time_data[j].required.paired != 'undefined') {
                    //             for (var p = 0; p < data.time_data[j].required.paired.length; p++) {
                    //                 if (k == data.time_data[j].required.paired[p].slot[0]) {
                    //                     html += '<td colspan="' + data.time_data[j].required.paired[p].slot.length + '">';
                    //                     html += '<b>' + data.time_data[j].required.paired[p].lesson + '</b>';
                    //                     html += '<p>' + data.time_data[j].required.paired[p].class + '</p>';
                    //                     html += '<table class="time_slot-child">';
                    //                     html += '<tbody>';
                    //                     html += '<tr>';
                    //                     for (var l = 0; l < data.time_data[j].required.paired[p].slot.length; l++) {
                    //                         html += '<td class="paired confirm" data-id="' + data.time_data[j].required.paired[p].slot[l] + '" data-scheduleid="'+data.time_data[j].required.paired[p].id+'">&nbsp;</td>';
                    //                     }
                    //                     html += '</tr>';
                    //                     html += '</tbody>';
                    //                     html += '</table>';
                    //                     html += '</td>';
                    //                     k = k + data.time_data[j].required.paired[p].slot.length;
                    //                 }
                    //                 if (typeof data.time_data[j].required.classes != 'undefined') {
                    //                     for (var c = 0; c < data.time_data[j].required.classes.length; c++) {
                    //                         if (k == data.time_data[j].required.classes[c].slot[0]) {
                    //                             html += '<td colspan="' + data.time_data[j].required.classes[c].slot.length + '">';
                    //                             html += '<b>' + data.time_data[j].required.classes[c].lesson + '</b>';
                    //                             html += '<p>' + data.time_data[j].required.classes[c].class + '</p>';
                    //                             html += '<table class="time_slot-child">';
                    //                             html += '<tbody>';
                    //                             html += '<tr>';
                    //                             for (var l = 0; l < data.time_data[j].required.classes[c].slot.length; l++) {
                    //                                 html += '<td class="action show-step4" data-id="' + data.time_data[j].required.classes[c].slot[l] + '" data-scheduleid="'+data.time_data[j].required.classes[c].id+'">&nbsp;</td>';
                    //                             }
                    //                             html += '</tr>';
                    //                             html += '</tbody>';
                    //                             html += '</table>';
                    //                             html += '</td>';
                    //                             k = k + data.time_data[j].required.classes[c].slot.length;
                    //                         }
                    //                         if (typeof data.time_data[j].required.substituted != 'undefined') {
                    //                             for (var s = 0; s < data.time_data[j].required.substituted.length; s++) {
                    //                                 if (k == data.time_data[j].required.substituted[s].slot[0]) {
                    //                                     html += '<td colspan="' + data.time_data[j].required.substituted[s].slot.length + '">';
                    //                                     html += '<b>' + data.time_data[j].required.substituted[s].lesson + '</b>';
                    //                                     html += '<p>' + data.time_data[j].required.substituted[s].class + '</p>';
                    //                                     html += '<table class="time_slot-child">';
                    //                                     html += '<tbody>';
                    //                                     html += '<tr>';
                    //                                     for (var l = 0; l < data.time_data[j].required.substituted[s].slot.length; l++) {
                    //                                         html += '<td class="substituted show-step4" data-id="' + data.time_data[j].required.substituted[s].slot[l] + '" data-scheduleid="'+data.time_data[j].required.substituted[s].id+'">&nbsp;</td>';
                    //                                     }
                    //                                     html += '</tr>';
                    //                                     html += '</tbody>';
                    //                                     html += '</table>';
                    //                                     html += '</td>';
                    //                                     k = k + data.time_data[j].required.substituted[s].slot.length;
                    //                                 }
                    //                                 if (typeof data.time_data[j].required.red != 'undefined') {
                    //                                     for (var r = 0; r < data.time_data[j].required.red.length; r++) {
                    //                                         if (k == data.time_data[j].required.red[r].slot[0]) {
                    //                                             html += '<td colspan="' + data.time_data[j].required.red[r].slot.length + '">';
                    //                                             html += '<b>' + data.time_data[j].required.red[r].lesson + '</b>';
                    //                                             html += '<p>' + data.time_data[j].required.red[r].class + '</p>';
                    //                                             html += '<table class="time_slot-child">';
                    //                                             html += '<tbody>';
                    //                                             html += '<tr>';
                    //                                             for (var l = 0; l < data.time_data[j].required.red[r].slot.length; l++) {
                    //                                                 html += '<td class="red show-step4" data-id="' + data.time_data[j].required.red[r].slot[l] + '" data-scheduleid="'+data.time_data[j].required.red[r].id+'">&nbsp;</td>';
                    //                                             }
                    //                                             html += '</tr>';
                    //                                             html += '</tbody>';
                    //                                             html += '</table>';
                    //                                             html += '</td>';
                    //                                             k = k + data.time_data[j].required.red[r].slot.length;
                    //                                         }
                    //                                     }
                    //                                 }
                    //                             }
                    //                         }
                    //                     }
                    //                 }
                    //             }
                    //         }
                    //         if (typeof data.time_data[j].required.substituted != 'undefined') {
                    //             for (var s = 0; s < data.time_data[j].required.substituted.length; s++) {
                    //                 if (k == data.time_data[j].required.substituted[s].slot[0]) {
                    //                     html += '<td colspan="' + data.time_data[j].required.substituted[s].slot.length + '">';
                    //                     html += '<b>' + data.time_data[j].required.substituted[s].lesson + '</b>';
                    //                     html += '<p>' + data.time_data[j].required.substituted[s].class + '</p>';
                    //                     html += '<table class="time_slot-child">';
                    //                     html += '<tbody>';
                    //                     html += '<tr>';
                    //                     for (var l = 0; l < data.time_data[j].required.substituted[s].slot.length; l++) {
                    //                         html += '<td class="substituted show-step4" data-id="' + data.time_data[j].required.substituted[s].slot[l] + '" data-scheduleid="'+data.time_data[j].required.substituted[s].id+'">&nbsp;</td>';
                    //                     }
                    //                     html += '</tr>';
                    //                     html += '</tbody>';
                    //                     html += '</table>';
                    //                     html += '</td>';
                    //                     k = k + data.time_data[j].required.substituted[s].slot.length;
                    //                 }
                    //                 if (typeof data.time_data[j].required.classes != 'undefined') {
                    //                     for (var c = 0; c < data.time_data[j].required.classes.length; c++) {
                    //                         if (k == data.time_data[j].required.classes[c].slot[0]) {
                    //                             html += '<td colspan="' + data.time_data[j].required.classes[c].slot.length + '">';
                    //                             html += '<b>' + data.time_data[j].required.classes[c].lesson + '</b>';
                    //                             html += '<p>' + data.time_data[j].required.classes[c].class + '</p>';
                    //                             html += '<table class="time_slot-child">';
                    //                             html += '<tbody>';
                    //                             html += '<tr>';
                    //                             for (var l = 0; l < data.time_data[j].required.classes[c].slot.length; l++) {
                    //                                 html += '<td class="action show-step4" data-id="' + data.time_data[j].required.classes[c].slot[l] + '" data-scheduleid="'+data.time_data[j].required.classes[c].id+'">&nbsp;</td>';
                    //                             }
                    //                             html += '</tr>';
                    //                             html += '</tbody>';
                    //                             html += '</table>';
                    //                             html += '</td>';
                    //                             k = k + data.time_data[j].required.classes[c].slot.length;
                    //                         }
                    //                         if (typeof data.time_data[j].required.paired != 'undefined') {
                    //                             for (var p = 0; p < data.time_data[j].required.paired.length; p++) {
                    //                                 if (k == data.time_data[j].required.paired[p].slot[0]) {
                    //                                     html += '<td colspan="' + data.time_data[j].required.paired[p].slot.length + '">';
                    //                                     html += '<b>' + data.time_data[j].required.paired[p].lesson + '</b>';
                    //                                     html += '<p>' + data.time_data[j].required.paired[p].class + '</p>';
                    //                                     html += '<table class="time_slot-child">';
                    //                                     html += '<tbody>';
                    //                                     html += '<tr>';
                    //                                     for (var l = 0; l < data.time_data[j].required.paired[p].slot.length; l++) {
                    //                                         html += '<td class="paired confirm" data-id="' + data.time_data[j].required.paired[p].slot[l] + '" data-scheduleid="'+data.time_data[j].required.paired[p].id+'">&nbsp;</td>';
                    //                                     }
                    //                                     html += '</tr>';
                    //                                     html += '</tbody>';
                    //                                     html += '</table>';
                    //                                     html += '</td>';
                    //                                     k = k + data.time_data[j].required.paired[p].slot.length;
                    //                                 }
                    //                                 if (typeof data.time_data[j].required.red != 'undefined') {
                    //                                     for (var r = 0; r < data.time_data[j].required.red.length; r++) {
                    //                                         if (k == data.time_data[j].required.red[r].slot[0]) {
                    //                                             html += '<td colspan="' + data.time_data[j].required.red[r].slot.length + '">';
                    //                                             html += '<b>' + data.time_data[j].required.red[r].lesson + '</b>';
                    //                                             html += '<p>' + data.time_data[j].required.red[r].class + '</p>';
                    //                                             html += '<table class="time_slot-child">';
                    //                                             html += '<tbody>';
                    //                                             html += '<tr>';
                    //                                             for (var l = 0; l < data.time_data[j].required.red[r].slot.length; l++) {
                    //                                                 html += '<td class="red show-step4" data-id="' + data.time_data[j].required.red[r].slot[l] + '" data-scheduleid="'+data.time_data[j].required.red[r].id+'">&nbsp;</td>';
                    //                                             }
                    //                                             html += '</tr>';
                    //                                             html += '</tbody>';
                    //                                             html += '</table>';
                    //                                             html += '</td>';
                    //                                             k = k + data.time_data[j].required.red[r].slot.length;
                    //                                         }
                    //                                     }
                    //                                 }
                    //                             }
                    //                         }
                    //                     }
                    //                 }
                    //             }
                    //         }
                    //         if (typeof data.time_data[j].required.red != 'undefined') {
                    //             for (var r = 0; r < data.time_data[j].required.red.length; r++) {
                    //                 if (k == data.time_data[j].required.red[r].slot[0]) {
                    //                     html += '<td colspan="' + data.time_data[j].required.red[r].slot.length + '">';
                    //                     html += '<b>' + data.time_data[j].required.red[r].lesson + '</b>';
                    //                     html += '<p>' + data.time_data[j].required.red[r].class + '</p>';
                    //                     html += '<table class="time_slot-child">';
                    //                     html += '<tbody>';
                    //                     html += '<tr>';
                    //                     for (var l = 0; l < data.time_data[j].required.red[r].slot.length; l++) {
                    //                         html += '<td class="red show-step4" data-id="' + data.time_data[j].required.red[r].slot[l] + '" data-scheduleid="'+data.time_data[j].required.red[r].id+'">&nbsp;</td>';
                    //                     }
                    //                     html += '</tr>';
                    //                     html += '</tbody>';
                    //                     html += '</table>';
                    //                     html += '</td>';
                    //                     k = k + data.time_data[j].required.red[r].slot.length;
                    //                 }
                    //                 if (typeof data.time_data[j].required.classes != 'undefined') {
                    //                     for (var c = 0; c < data.time_data[j].required.classes.length; c++) {
                    //                         if (k == data.time_data[j].required.classes[c].slot[0]) {
                    //                             html += '<td colspan="' + data.time_data[j].required.classes[c].slot.length + '">';
                    //                             html += '<b>' + data.time_data[j].required.classes[c].lesson + '</b>';
                    //                             html += '<p>' + data.time_data[j].required.classes[c].class + '</p>';
                    //                             html += '<table class="time_slot-child">';
                    //                             html += '<tbody>';
                    //                             html += '<tr>';
                    //                             for (var l = 0; l < data.time_data[j].required.classes[c].slot.length; l++) {
                    //                                 html += '<td class="action show-step4" data-id="' + data.time_data[j].required.classes[c].slot[l] + '" data-scheduleid="'+data.time_data[j].required.classes[c].id+'">&nbsp;</td>';
                    //                             }
                    //                             html += '</tr>';
                    //                             html += '</tbody>';
                    //                             html += '</table>';
                    //                             html += '</td>';
                    //                             k = k + data.time_data[j].required.classes[c].slot.length;
                    //                         }
                    //                         if (typeof data.time_data[j].required.paired != 'undefined') {
                    //                             for (var p = 0; p < data.time_data[j].required.paired.length; p++) {
                    //                                 if (k == data.time_data[j].required.paired[p].slot[0]) {
                    //                                     html += '<td colspan="' + data.time_data[j].required.paired[p].slot.length + '">';
                    //                                     html += '<b>' + data.time_data[j].required.paired[p].lesson + '</b>';
                    //                                     html += '<p>' + data.time_data[j].required.paired[p].class + '</p>';
                    //                                     html += '<table class="time_slot-child">';
                    //                                     html += '<tbody>';
                    //                                     html += '<tr>';
                    //                                     for (var l = 0; l < data.time_data[j].required.paired[p].slot.length; l++) {
                    //                                         html += '<td class="paired confirm" data-id="' + data.time_data[j].required.paired[p].slot[l] + '" data-scheduleid="'+data.time_data[j].required.paired[p].id+'">&nbsp;</td>';
                    //                                     }
                    //                                     html += '</tr>';
                    //                                     html += '</tbody>';
                    //                                     html += '</table>';
                    //                                     html += '</td>';
                    //                                     k = k + data.time_data[j].required.paired[p].slot.length;
                    //                                 }
                    //                                 if (typeof data.time_data[j].required.substituted != 'undefined') {
                    //                                     for (var s = 0; s < data.time_data[j].required.substituted.length; s++) {
                    //                                         if (k == data.time_data[j].required.substituted[s].slot[0]) {
                    //                                             html += '<td colspan="' + data.time_data[j].required.substituted[s].slot.length + '">';
                    //                                             html += '<b>' + data.time_data[j].required.substituted[s].lesson + '</b>';
                    //                                             html += '<p>' + data.time_data[j].required.substituted[s].class + '</p>';
                    //                                             html += '<table class="time_slot-child">';
                    //                                             html += '<tbody>';
                    //                                             html += '<tr>';
                    //                                             for (var l = 0; l < data.time_data[j].required.substituted[s].slot.length; l++) {
                    //                                                 html += '<td class="substituted show-step4" data-id="' + data.time_data[j].required.substituted[s].slot[l] + '" data-scheduleid="'+data.time_data[j].required.substituted[s].id+'">&nbsp;</td>';
                    //                                             }
                    //                                             html += '</tr>';
                    //                                             html += '</tbody>';
                    //                                             html += '</table>';
                    //                                             html += '</td>';
                    //                                             k = k + data.time_data[j].required.substituted[s].slot.length;
                    //                                         }
                    //                                     }
                    //                                 }
                    //                             }
                    //                         }
                    //                     }
                    //                 }
                    //             }
                    //         }
                    //         if (k <= data.time_slot.length) {
                    //             html += '<td></td>';
                    //         }
                    //     }
                    //     html += '</tr>';
                    // }
                    html += '</tbody>';
                    html += '</table>';

                    $(".container-step3").html(html);

                    $('#step2').show();

                    $('html, body').animate({
                        scrollTop: ($('#step2').offset().top)
                    },500);

                }
            }
        });




    },
    onSelectTimeline: function(){

        $(".container-step3").on("click", "td.show-step4",function(event) {
            event.stopPropagation();
            $('#step3').fadeIn('slow');
            $('#step4').fadeIn('slow');

            $('#input_scheduleId').val($(this).data('scheduleid'));

            $('html, body').animate({
                scrollTop: ($('#step3').offset().top)
            },500);
        });

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
        // $('#selectedUserAvailabel').select2();
        // $('select').select2();
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

                // console.log(formattedDate);

                var holidaysWeekNumber = [11,22,23,24,25,36,47,48,49,50,51,52];
                if(holidaysWeekNumber.indexOf(curr_week) >= 0 ){
                    return false;
                }

                if ($.inArray(formattedDate, Home.active_assignment_dates) != -1){
                    return {
                        classes: 'activeDate'
                    };
                }
                return;
            }
        });

        this.onSelectUser();
        this.onSelectCalendar();
        // this.onShowTimeLine();
        this.onSelectTimeline();

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
Home.init();