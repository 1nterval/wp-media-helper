jQuery(function($){

    $('.mediahelper_active').change(function(e){
        $(this).prev().val(this.checked);
    });

});
