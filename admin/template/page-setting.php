<?php
require_once WP_PLUGIN_DIR . '/barbwire-security/inc/Version.php';
require_once WP_PLUGIN_DIR . '/barbwire-security/inc/LoginParameter.php';
use barbsecurity\Version as Version;
use barbsecurity\LoginParameter as LoginParameter;

$options = get_option( Version::$name, array() );
?>
<div class="wrap">
    <div class="wrap">
        <h2><?= __( 'Barbwire Security Settings', 'barbwire-security' ) ?></h2>
    </div>

    <form id="secure" method="post" action="">
		<?php wp_nonce_field( Version::$name, 'barb_secure' ) ?>
        <div class="header_buttons">
            <input type="submit" class="button button-primary button-large"
                   value="<?= __( 'save', 'barbwire-security' ) ?>"/>
        </div>
        <div id="settings">
            <h3><?= __( 'ADMIN LOGIN PAGE URL PARAMETER', 'barbwire-security' ); ?><a id="login_parameter" class="help_link"
                                                                                 href="#"><img
                            src="<?= plugins_url() . '/barbwire-security/img/question_icon.png' ?>"/></a></h3>
			<?php
			$enable = isset( $options['parameter_enable'] ) && $options['parameter_enable'] == true;
			?>
            <p><?= __( 'Login URL is', 'barbwire-security' ) ?> <input id="login_url" type="text"
                                                                  value="<?= wp_login_url() ?>" onclick="this.select()"
                                                                  readonly="readonly"/></p>
            <table>
                <tr>
                    <th><?= __( 'enable login url parameter function', 'barbwire-security' ) ?></th>

                    <td><label><input type="checkbox" name="parameter_enable"
                                      value="1" <?= $enable ? "checked='checked'" : ''; ?>><?= __( 'enable', 'barbwire-security' ) ?>
                        </label></td>
                </tr>
                <tr>
                    <th><?= __( 'parameter name', 'barbwire-security' ) ?></th>
                    <td>
						<?php $value = ! empty( $_POST['param_name'] ) && $enable ? $_POST['param_name'] : $options['param_name']; ?>
                        <input type="text" name="param_name" placeholder="default:<?= LoginParameter::$key ?>"
                               value="<?= ! empty( $value ) ? $value : '' ?>" <?= $enable ? '' : 'readonly' ?>/><br/>
						<?= __( 'Alphanumeric characters and hyphens, underscores only.', 'barbwire-security' ) ?>
                    </td>
                </tr>
                <tr>
                    <th><?= __( 'parameter value', 'barbwire-security' ) ?></th>
                    <td>
						<?php $value = ! empty( $_POST['param_value'] ) && $enable ? $_POST['param_value'] : $options['param_value']; ?>
                        <input type="text" name="param_value" placeholder="default:<?= LoginParameter::$val ?>"
                               value="<?= ! empty( $value ) ? $value : '' ?>" <?= $enable ? '' : 'readonly' ?>/><br/>
						<?= __( 'Alphanumeric characters and hyphens, underscores only.', 'barbwire-security' ) ?>
                    </td>
                </tr>
            </table>
            <!-- TODO Unimplemented
            <h3>LOGIN RETRY LIMIT COUNT</h3>
            <table>
                <tr>
                    <th>Enable the login retry limit count function</th>
                    <?php /* TODO Unimplemented */ //$enable = isset($options['retry_times_enable']) && $options['retry_times_enable']==true;?>
                    <td><label><input type="checkbox" name="retry_times_enable" value="1" <?php /* TODO Unimplemented */ //isset($options['retry_times_enable']) && $options['retry_times_enable']==true?"checked='checked'":'';?>/>enable</label></td>
                </tr>
                <tr>
                    <th>Retry Limit</th>
                    <td><input class="retry_field short_num" type="number" name="retry_limit" value="<?php /* TODO Unimplemented */ //isset($options['retry_limit'])?$options['retry_limit']:''?>" <?php /* TODO Unimplemented */ //$enable?'':'readonly'?>/></td>
                </tr>
                <tr>
                    <th>Lockout Period</th>
                    <td><input class="retry_field short_num" type="number" name="retry_lock_period" value="<?php /* TODO Unimplemented */ //isset($options['retry_lock_period'])?$options['retry_lock_period']:''?>" <?php /* TODO Unimplemented */ // $enable?'':'readonly'?>> minuites</td>
                </tr>
                <tr>
                    <th>Connection Setting</th>
                    <td>
                        <label><input class="retry_field" type="radio" name="retry_connection" value="1" <?php /* TODO Unimplemented */ //isset($options['retry_connection']) && $options['retry_connection']=='1'?"checked='checked'":'';?> <?php /* TODO Unimplemented */ //$enable?'':'readonly'?>/>Direct connection to server.</label><br/>
                        <label><input class="retry_field" type="radio" name="retry_connection" value="2" <?php /* TODO Unimplemented */ //isset($options['retry_connection']) && $options['retry_connection']=='2'?"checked='checked'":'';?> <?php /* TODO Unimplemented */ //$enable?'':'readonly'?>/>Conecction via reversy proxy.</label>
                    </td>
                </tr>
            </table>
            -->
            <h3><?= __( 'AUTHOR ARCHIVE', 'barbwire-security' ) ?><a id="author_archive" class="help_link" href="#"><img
                            src="<?= plugins_url() . '/barbwire-security/img/question_icon.png' ?>"/></a></h3>
            <table>
                <tr>
                    <th><?= __( 'Block the display of author archive page', 'barbwire-security' ) ?></th>
                    <td><label><input type="checkbox" name="block_author_archive"
                                      value="1" <?= isset( $options['block_author_archive'] ) && $options['block_author_archive'] == true ? "checked='checked'" : ''; ?>>enable</label>
                    </td>
                </tr>
            </table>

            <h3><?= __( 'PINGBACK', 'barbwire-security' ) ?><a id="pingback" class="help_link" href="#"><img
                            src="<?= plugins_url() . '/barbwire-security/img/question_icon.png' ?>"/></a></h3>
            <table>
                <tr>
                    <th><?= __( 'Suppress Pingback function', 'barbwire-security' ) ?></th>
                    <td><label><input type="checkbox" name="pingback_suppress_enable"
                                      value="1" <?= isset( $options['pingback_suppress_enable'] ) && $options['pingback_suppress_enable'] == true ? "checked='checked'" : ''; ?>>enable</label>
                    </td>
                </tr>
            </table>

            <!--  TODO Unimplemented
            <h3>CAPTCHA</h3>
            <table>
                <tr>
                    <th>enable the CAPTCHA at login</th>
                    <td><label><input type="checkbox" name="captcha_enable" value="1" <?php /* TODO Unimplemented */ // isset($options['captcha_enable']) && $options['captcha_enable']==true?"checked='checked'":'';?>>enable</label></td>
                </tr>
            </table>
            -->

            <h3><?= __( 'REST API', 'barbwire-security' ) ?><a id="restapi" class="help_link" href="#"><img
                            src="<?= plugins_url() . '/barbwire-security/img/question_icon.png' ?>"/></a></h3>
            <table>
                <tr>
                    <th><?= __( 'Disable REST API', 'barbwire-security' ) ?></th>
                    <td><label><input type="checkbox" name="disable_rest_api"
                                      value="1" <?= isset( $options['disable_rest_api'] ) && $options['disable_rest_api'] == true ? "checked='checked'" : ''; ?>>enable</label>
                    </td>
                </tr>
            </table>

            <div class="header_buttons">
                <input type="submit" class="button button-primary button-large"
                       value="<?= __( 'save', 'barbwire-security' ) ?>"/>
            </div>
        </div>
    </form>
</div>