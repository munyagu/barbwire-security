jQuery(function(){
    jQuery("input[name=parameter_enable]").on("change", function(evt){
        var param_enable = jQuery(evt.target);
        var table = param_enable.closest("table");
        bs_ctl_retry_input(param_enable.prop('checked'), table);
    });

    /* TODO Unimplemented
    jQuery("input[name=retry_times_enable]").on("change", function(evt){
        var param_enable = jQuery(evt.target);
        var table = param_enable.closest("table");
        bs_ctl_param_input(param_enable.prop('checked'), table);
    });

    */
});

/**
 * Control ADMIN LOGIN PAGE URL PARAMETER input
 * @param flag
 * @param table
 */
function bs_ctl_retry_input(flag, table){
    if(flag){
        table.find("input[name=param_name]").prop('readonly', false);
        table.find("input[name=param_value]").prop('readonly', false);
    }else{
        table.find("input[name=param_name]").prop('readonly', true);
        table.find("input[name=param_value]").prop('readonly', true);
    }
}

/**
 * Control LOGIN RETRY LIMIT COUNT input
 * @param flag
 * @param table
 */
/* TODO Unimplemented
function bs_ctl_param_input(flag, table){
    if(flag){
        table.find("input[name=retry_limit]").prop('readonly', false);
        table.find("input[name=retry_lock_period]").prop('readonly', false);
        table.find("input[name=retry_connection]").prop('readonly', false);
        table.find("input[name=retry_connection]").unbind();
    }else{
        table.find("input[name=retry_limit]").prop('readonly', true);
        table.find("input[name=retry_lock_period]").prop('readonly', true);
        table.find("input[name=retry_connection]").prop('readonly', true);
        table.find("input[name=retry_connection]").on("click", function(evt){
            evt.preventDefault();
        });
    }
}
*/
