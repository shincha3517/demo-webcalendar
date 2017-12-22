/**
 * Created by eric on 8/22/17.
 */
var UploadExcel = {

    onIntervalSelect: function(){
        //$('.timepicker').wickedpicker({twentyFour: true});
        //Timepicker
        $('.timepicker').timepicker({
            maxHours:24,
            minuteStep:1,
            showInputs: false,
            showMeridian:true,
        })
    },
    init: function(){
        this.onIntervalSelect();
        $('.datepicker').datepicker({
            todayHighlight: true,
        });
    }
}
UploadExcel.init();