/**
 * Created by eric on 8/22/17.
 */
var UploadExcel = {


    onIntervalSelect: function(){
        $('#interval').on('change', function (properties) {
            var interval = $(this).val();
            $('#startTime').timepicker({
                'step': interval
            });
        });
    },

    init: function(){
        this.onIntervalSelect();
    }
}
UploadExcel.init();