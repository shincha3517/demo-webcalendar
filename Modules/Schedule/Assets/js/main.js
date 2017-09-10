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

            $('#step2').hide();
            $('#step3').hide();
            $('#step4').hide();

            $('html, body').animate({
                scrollTop: ($('#step1').offset().top)
            },500);
        });
    },

    onSelectUser: function () {
        $('#ddUser').on('change',function(e){

            if($(this).val() > 0){
                if(Home.timeline !=''){
                    console.log('disable timeline');
                    Home.timeline.destroy();
                }
                //show timeline
                Home.onShowTimeLine();
            }
            else{
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

        $('#step2').show();

        $('html, body').animate({
            scrollTop: ($('#step2').offset().top)
        },500);

    },
    onSelectTimeline: function(){

        this.timeline.on('select', function (properties) {
            var eventId = parseInt(properties.items,10);
            if(eventId > 0){
                $('#step3').fadeIn('slow');
                Home.onShowAvailableUser();

                $('html, body').animate({
                    scrollTop: ($('#step3').offset().top)
                },500);
            }
        });
    },
    onShowAvailableUser: function(){
        // DOM element where the Timeline will be attached
        var container = document.getElementById('availableUserTimeline');
        var groups = new vis.DataSet([
            {id: 0, content: 'Username 1', value: 1},
            {id: 1, content: 'Username 2', value: 3}
        ]);

        // Create a DataSet (allows two way data-binding)
        var items = new vis.DataSet([
            {"id": 1,"group":0,"content": "item 1","start": "2017-08-22T04:00:00","end": "2017-08-22T04:30:00"},
            {"id": 2,"group":0,"content": "item 1","start": "2017-08-22T06:00:00","end": "2017-08-22T10:30:00"},
            {"id": 3,"group":1,"content": "item 1","start": "2017-08-22T04:00:00","end": "2017-08-22T04:30:00"},
            {"id": 4,"group":1,"content": "item 1","start": "2017-08-22T15:00:00","end": "2017-08-22T15:30:00"},
        ]);

        // Configuration for the Timeline
        var options = {
            height: '200px',
            min: '2017-08-22T00:00:00',                // lower limit of visible range
            max: '2017-08-22T24:00:00',                // upper limit of visible range
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
                $('#step4').fadeIn('slow');

                $('html, body').animate({
                    scrollTop: ($('#step3').offset().top)
                },500);
            }
        });
    },

    init: function(){
        $('#step2').hide();
        $('#step3').hide();
        $('#step4').hide();
        $('select').select2();
        $('#datepicker').datepicker();

        this.onSelectUser();
        this.onSelectCalendar();
        // this.onShowTimeLine();
        // this.onSelectTimeline();
    }
}
Home.init();