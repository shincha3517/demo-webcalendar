/**
 * Created by eric on 8/22/17.
 */
var UploadExcel = {


    onIntervalSelect: function(){
        $('#interval').on('change', function (properties) {
            var interval = $(this).val();
            console.log(interval);
            $('#startTime').timepicker({
                'step': interval
            });
        });
    },


    init: function(){
        this.onIntervalSelect();
        $('#sync-button').on('click',function(e){

            loadProgressBar($('#progressBar'));

            // SET INTERVAL IN MILLISECONDS TO REPEAT THE FUNCTION
            var intval   = 1000;
            var pBLoader = setInterval(function(){ loadProgressBar($("#progressBar")); }, intval);

            // A VARIABLE TO HOLD THE RESPONSE FROM THE BACKGROUND SCRIPT
            var percent = 0;

        });
    }
}
UploadExcel.init();