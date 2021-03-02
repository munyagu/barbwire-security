/**
 * JavaScript for configuration page.
 */


jQuery(function ($) {

    $("input[name=parameter_enable]").on("change", function (evt) {
        var param_enable = jQuery(evt.target);
        var table = param_enable.closest("table");
        bs_ctl_retry_input(param_enable.prop('checked'), table);
    });

    $('a.help_link').on('click', function (etv) {
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


    /***** reCaptcha *****/
    $('#captcha_enable').on('change', function (evt) {
        var captcha_enable = jQuery(evt.target);
        var table = captcha_enable.closest('table');
        bs_ctl_recaptcha_input(captcha_enable.prop('checked'), table);
        rbs_ecaptcha_valid();
    });

    $('#recaptcha_site_key,#recaptcha_secret_key').on('change', function(){
        $('#rest_test').prop('disabled', false);
        $('.bs_submit').prop('disabled', true);
        rbs_ecaptcha_valid(false);
    });

    $('#rest_test').on('click', function () {

        var site_key = $('#recaptcha_site_key').val();
        var secret_key = $('#recaptcha_secret_key').val();

        if ('' === site_key) {
            alert('Site key is not entered.');
            return;
        }

        if ('' === secret_key) {
            alert('Secret key is not entered.');
            return;
        }

        // Disable test button
        $(this).prop('disabled', true);

        $('#google_recatpcha_script').remove();

        var head = document.getElementsByTagName('head')[0];
        var script = document.createElement('script');
        var url = bsVariabls.rest_namespace + '/' + bsVariabls.rest_recaptcha_test_root;

        script.id = 'google_recatpcha_script';
        script.onload = function () {

            $('#rest_test_button_wrap img').css('display', 'block');
            grecaptcha.ready(function () {

                try {
                    grecaptcha.execute(
                        site_key,
                        {action: 'login'}
                    ).then(function (token) {
                        $.ajax({
                            type: 'POST',
                            url: url,
                            data: {
                                token: token,
                                secret_key: secret_key
                            },
                            headers: {
                                "X-WP-Nonce": bsVariabls.nonce
                            }
                        }).then(function (data) {

                            if (true === data.success) {
                                alert('Ok, valid Keys.');
                                rbs_ecaptcha_valid(true);
                            } else {
                                rbs_ecaptcha_valid(false);
                                var message = "NG, invalid Keys !\n"

                                for (let key in data.errors) {
                                    message += data.errors[key] + "\n";
                                }
                                alert(message);
                            }
                        }).catch(function(){
                            console.log("failed 101");
                        }).then(function(){
                            rbs_initialize();
                        });

                    }).catch(function(){

                        alert('NG, invalid Keys !');
                        console.log("failed 102");
                        rbs_ecaptcha_valid(false);

                    }).then(function(){
                        rbs_initialize();
                        $('#google_recatpcha_script').remove();
                    });
                } catch (exception) {
                    rbs_ecaptcha_valid(false);
                    $('#rest_test').prop("disabled", false);
                    $('#rest_test_button_wrap img').css('display', 'none');

                    alert('NG, invalid Keys !' + "\n" + exception.message);
                    console.log("failed 103");
                } finally {
                    $('#google_recatpcha_script').remove();
                }

            });
        };

        head.appendChild(script);
        if( 'undefined' !== typeof grecaptcha ){
            grecaptcha = null;
        }

        script.src = "https://www.google.com/recaptcha/api.js?render=" + site_key;

    });

});

function rbs_initialize(){
    var $ = jQuery;
    $('#rest_test').prop("disabled", false);
    $('#rest_test_button_wrap img').css('display', 'none');
}

/**
 *
 *
 * @param valid test result
 */
function rbs_ecaptcha_valid( valid = null ){

    var $ = jQuery;

    if( null === valid ){
        if( $('#captcha_enable').prop('checked') ) {
            if('' === $('#recaptcha_site_key').val()
                || '' === $('#recaptcha_secret_key').val() ){
                valid = false;
            }
        }
    }

    var recaptcha_wrap = $('#recaptcha');

    if( true === valid ) {
        recaptcha_wrap.removeClass('bs_invalid');
        recaptcha_wrap.addClass('bs_valid');
        $('.bs_submit').prop("disabled", false);
    } else if( false === valid ) {
        recaptcha_wrap.removeClass('bs_valid');
        recaptcha_wrap.addClass('bs_invalid');
        $('.bs_submit').prop("disabled", true);
        $('.grecaptcha-badge').remove();
    } else {
        recaptcha_wrap.removeClass('bs_valid');
        recaptcha_wrap.removeClass('bs_invalid');
        $('.bs_submit').prop("disabled", false);
    }



}

/**
 * Control reCaptcha key input
 * @param flag
 * @param table
 */
function bs_ctl_recaptcha_input(flag, table) {
    table.find('input[name=recaptcha_site_key]').prop('readonly', ! flag);
    table.find('input[name=recaptcha_secret_key]').prop('readonly', ! flag);
}

/**
 * Control ADMIN LOGIN PAGE URL PARAMETER input
 * @param flag
 * @param table
 */
function bs_ctl_retry_input(flag, table) {
    table.find('input[name=param_name]').prop('readonly', ! flag);
    table.find('input[name=param_value]').prop('readonly', ! flag);
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
