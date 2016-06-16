// custom.js
jQuery(function($) {
    var $info = $("#modal-content");
    $info.dialog({                   
        'dialogClass'   : 'wp-dialog',           
        'modal'         : true,
        'autoOpen'      : false, 
        'closeOnEscape' : true,      
        'buttons'       : {
            "Close": function() {
                $(this).dialog('close');
            }
        }
    });
    $("#open-modal").click(function(event) {
        event.preventDefault();
        $info.dialog('open');
    });
}); 
