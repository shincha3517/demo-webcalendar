/**
 * Created by eric on 8/22/17.
 */
var Home = {

    timeline: '',

    onSelectUser: function () {
        $('#ddUser').on('change',function(e){

            if($(this).val() > 0){

                $('#datepicker').datepicker();
                $('#datepicker').on('changeDate', function() {

                    $('#my_hidden_input').val(
                        $('#datepicker').datepicker('getFormattedDate')
                    );

                    if(Home.timeline !=''){
                        Home.timeline.destroy();
                    }

                    //show timeline
                    Home.onShowTimeLine();
                });
            }
            else{
                $('#datepicker').datepicker('remove');
                Home.timeline.destroy();
            }

        });
    },
    onShowTimeLine: function(){
        // DOM element where the Timeline will be attached
        var container = document.getElementById('visualization');

        // Create a DataSet (allows two way data-binding)
        var items = new vis.DataSet([
            {"id": 1,"content": "item 1","start": "2017-08-22T04:00:00","end": "2017-08-22T04:30:00"},
            {"id": 2,"content": "item 1","start": "2017-08-22T06:00:00","end": "2017-08-22T10:30:00"},
            {"id": 3,"content": "item 1","start": "2017-08-22T11:00:00","end": "2017-08-22T11:30:00"},
            {"id": 4,"content": "item 1","start": "2017-08-22T15:00:00","end": "2017-08-22T15:30:00"},
        ]);

        // Configuration for the Timeline
        var options = {
            height: '200px',
            min: '2017-08-22T00:00:00',                // lower limit of visible range
            max: '2017-08-22T24:00:00',                // upper limit of visible range
            zoomMin: 5
        };

        // Create a Timeline
        this.timeline = new vis.Timeline(container, items, options);

        this.onSelectTimeline();

        $('#step2').removeClass('hide').fadeIn('slow');

        $('html, body').animate({
            scrollTop: ($('#step2').offset().top)
        },500);

    },
    onSelectTimeline: function(){

        this.timeline.on('select', function (properties) {
            var eventId = parseInt(properties.items,10);
            if(eventId > 0){
                $('#step3').fadeIn('slow');

                $('html, body').animate({
                    scrollTop: ($('#step3').offset().top)
                },500);
            }
        });
    },

    init: function(){
        $('#step2').hide();
        $('#step3').hide();
        $('select').select2();

        this.onSelectUser();
        // this.onShowTimeLine();
        // this.onSelectTimeline();
    }
}
Home.init();