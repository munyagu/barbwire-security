/**
 * JavaScript for configuration page.
 */



jQuery(function ($) {

    jQuery("input[name=parameter_enable]").on("change", function (evt) {
        var param_enable = jQuery(evt.target);
        var table = param_enable.closest("table");
        bs_ctl_retry_input(param_enable.prop('checked'), table);
    });

    jQuery('a.help_link').on('click', function (etv) {
        var element = jQuery(etv.target);
        if (element.prop("tagName").toUpperCase() != 'A') {
            element = element.parent();
        }
        var name = element.attr('id');
        bs_disp_help_content(name);
    });

    $('#toggle_advance').on('click', function (e) {
        $('#advance').slideToggle();
        e.preventDefault();
    });

    if ($('textarea[name=end_points]').val() != "") {
        $('#advance').css("display", "block");
    }

});


/**
 * Control ADMIN LOGIN PAGE URL PARAMETER input
 * @param flag
 * @param table
 */
function bs_ctl_retry_input(flag, table) {
    if (flag) {
        table.find('input[name=param_name]').prop('readonly', false);
        table.find('input[name=param_value]').prop('readonly', false);
    } else {
        table.find('input[name=param_name]').prop('readonly', true);
        table.find('input[name=param_value]').prop('readonly', true);
    }
}

/**
 * Show help page
 *
 * @param name help page name.
 */
function bs_disp_help_content(name) {
    jQuery('#screen-meta').css('display', 'block');
    jQuery('#contextual-help-wrap').css('display', 'block');

    var tabs = jQuery('#contextual-help-columns .contextual-help-tabs ul').children();
    for (var i = 0; i < tabs.length; i++) {

        if (tabs.eq(i).attr('id') == 'tab-link-' + name) {
            tabs.eq(i).addClass('active');
        } else {
            tabs.eq(i).removeClass('active');
        }
    }
    var panels = jQuery('#screen-meta .contextual-help-tabs-wrap').children();
    for (i = 0; i < panels.length; i++) {
        if (panels.eq(i).attr('id') == 'tab-panel-' + name) {
            panels.eq(i).css('display', 'block');
        } else {
            panels.eq(i).css('display', 'none');
        }
    }

}