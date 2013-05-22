<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
/**
 * Appapro iAthletics options.
 *
 * $Id$
 *
 * (c) 2011 by Mike Walsh
 *
 * @author Mike Walsh <mpwalsh8@gmail.com>
 * @package iAthletics
 * @subpackage options
 * @version $Revision$
 * @lastmodified $Date$
 * @lastmodifiedby $Author$
 *
 */

/**
 * aia_options_admin_footer()
 *
 * Hook into Admin head when showing the options page
 * so the necessary jQuery script that controls the tabs
 * is executed.
 *
 * @return null
 */
function aia_options_admin_footer()
{
?>
<!-- Setup jQuery Tabs -->
<script type="text/javascript">
    jQuery(document).ready(function() {
        jQuery("#aia-tabs").tabs() ;
    }) ;
</script>
<?php
} /* function aia_options_admin_footer() */

/**
 * aia_options_print_scripts()
 *
 * Hook into Admin Print Scripts when showing the options page
 * so the necessary scripts that controls the tabs are loaded.
 *
 * @return null
 */
function aia_options_print_scripts()
{
    //  Need to load jQuery UI Tabs to make the page work!

    wp_enqueue_script('jquery-ui-tabs') ;
}

/**
 * aia_options_print_styles()
 *
 * Hook into Admin Print Styles when showing the options page
 * so the necessary style sheets that control the tabs are
 * loaded.
 *
 * @return null
 */
function aia_options_print_styles()
{
    //  Need the jQuery UI CSS to make the tabs look correct.
    //  Load them from Google - should not be an issue since
    //  this plugin is all about consuming Google content!

    wp_enqueue_style('xtra-jquery-ui-css', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.24/themes/base/jquery-ui.css') ;
}

/**
 * aia_options_page()
 *
 * Build and render the options page.
 *
 * @return null
 */
function aia_options_page()
{
?>
<div class="wrap">

<?php
    if (function_exists('screen_icon')) screen_icon() ;
?>
<h2><?php _e('Appapro iAthletics Plugin Settings') ; ?></h2>
<?php
    $aia_options = aia_get_plugin_options() ;
    if (!$aia_options['donation_message'])
    {
?>
<small><?php printf(__('Please consider making a <a href="%s" target="_blank">PayPal donation</a> if you find this plugin useful.', WPGFORM_I18N_DOMAIN), 'https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=DK4MS3AA983CC') ; ?></small>
<?php
    }
?>
<br /><br />
<div class="container">
    <div id="aia-tabs">
        <ul>
        <li><a href="#aia-tabs-1"><?php _e('Options', WPGFORM_I18N_DOMAIN);?></a></li>
        <li><a href="#aia-tabs-2"><?php _e('Advanced Options', WPGFORM_I18N_DOMAIN);?></a></li>
        <li><a href="#aia-tabs-3"><?php _e('FAQs', WPGFORM_I18N_DOMAIN);?></a></li>
        <li><a href="#aia-tabs-4"><?php _e('Usage', WPGFORM_I18N_DOMAIN);?></a></li>
        <li><a href="#aia-tabs-5"><?php _e('About', WPGFORM_I18N_DOMAIN);?></a></li>
        </ul>
        <div id="aia-tabs-1">
            <form method="post" action="options.php">
                <?php settings_fields('aia_options') ; ?>
                <?php aia_settings_input() ; ?>
                <input type="submit" name="Submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
            </form>
        </div>
        <div id="aia-tabs-2">
            <form method="post" action="options.php">
                <?php settings_fields('aia_options') ; ?>
                <?php aia_settings_advanced_options() ; ?>
                <input type="submit" name="Submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
            </form>
        </div>
        <div id="aia-tabs-3">
<?php
    //
    //  Instead of duplicating the FAQ and Other Notes content in the ReadMe.txt file,
    //  let's simply extract it from the WordPress plugin repository!
    //
	//  Fetch via the content via the WordPress Plugins API which is largely undocumented.
    //
    //  @see http://dd32.id.au/projects/wordpressorg-plugin-information-api-docs/
    //
    //  We want just the 'sections' content of the ReadMe file which will yield an array
    //  which contains an element for each section of the ReadMe file.  We'll use 'faq' and
    //  'other_notes'.
    //

	require_once( ABSPATH . '/wp-admin/includes/plugin-install.php' );
	$readme = plugins_api( 'plugin_information', array('slug' => 'aia', 'fields' => array( 'sections' ) ) );

    if (is_wp_error($readme))
    {
?>
<div class="updated error"><?php _e('Unable to retrive FAQ content from WordPress plugin repository.', WPGFORM_I18N_DOMAIN);?></div>
<?php
    }
    else
    {
        echo $readme->sections['faq'] ;
    }
?>
        </div>
        <div id="aia-tabs-4">
<?php

    if (is_wp_error($readme))
    {
?>
<div class="updated error"><?php _e('Unable to retrive Usage content from WordPress plugin repository.', WPGFORM_I18N_DOMAIN);?></div>
<?php
    }
    else
    {
        echo $readme->sections['other_notes'] ;
    }
?>
        </div>
        <div id="aia-tabs-5">
        <h4><?php _e('About Appapro iAthletics', WPGFORM_I18N_DOMAIN);?></h4>
<div style="margin-left: 25px; text-align: center; float: right;" class="postbox">
<h3 class="hndle"><span><?php _e('Make a Donation', MAILUSERS_I18N_DOMAIN);?></span></h3>
<div class="inside">
<div style="text-align: center; font-size: 0.75em;padding:0px 5px;margin:0px auto;"><!-- PayPal box wrapper -->
<div><!-- PayPal box-->
	<p style="margin: 0.25em 0"><b>WordPress Goolge Forms v<?php echo WPGFORM_VERSION; ?></b></p>
	<p style="margin: 0.25em 0"><a href="http://wordpress.org/extend/plugins/aia/" target="_blank"><?php _e('Plugin\'s Home Page', MAILUSERS_I18N_DOMAIN); ?></a></p>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="DK4MS3AA983CC">
<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form>
</div><!-- PayPal box -->
</div>
</div><!-- inside -->
</div><!-- postbox -->
<div>

        <p><?php _e('An easy to implement integration of a Appapro iAthletics with WordPress. This plugin allows you to leverage the power of Appapro iAthletics to display the same information on your WordPress site as appears on your Apparo iAthletics based mobile application.', WPGFORM_I18N_DOMAIN);?></p>
        <p><?php _e('Appapro iAthletics is based on the <a href="%s"><b>WordPress HTTP API</b></a> and in particular, the <a href="%s"><b>wp_remote_get()</b></a> and <a href="http://codex.wordpress.org/Function_API/wp_remote_post"><b>wp_remote_post()</b></a> functions for retrieving and posting the form.  Appapro iAthletics also makes use of the <a href="%s"><b>wp_kses()</b></a> function for processing the XML retrieved from Appapro and extracting the relevant information.</p><p>If you find this plugin useful, please consider <a href="%s" target="_blank">making small donation towards this plugin</a> to help keep it up to date.</p>', 'http://codex.wordpress.org/HTTP_API', 'http://codex.wordpress.org/Function_API/wp_remote_get', 'http://codex.wordpress.org/Function_Reference/wp_kses', 'https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=DK4MS3AA983CC', WPGFORM_I18N_DOMAIN);?>
</div>
        </div>
    </div>
</div>
<?php
}


/**
 * aia_settings_input()
 *
 * Build the form content and populate with any current plugin settings.
 *
 * @return none
 */
function aia_settings_input()
{
    $aia_options = aia_get_plugin_options() ;
?>
    <table class="form-table">
        <tr valign="top">
            <th scope="row"><label><?php _e('<b><i>iAthletics</i></b> API Key', WPGFORM_I18N_DOMAIN);?></label></th>
            <td><fieldset>
            <label for="aia_api_key">
            <input name="aia_options[api_key]" type="text" id="aia_api_key" value="<?php echo $aia_options['api_key'] ; ?>" /><br />
           <small><?php _e('(mandatory)', WPGFORM_I18N_DOMAIN);?></small></label>
            </fieldset></td>
        </tr>
        <tr valign="top">
            <th scope="row"><label><b><i>iAthletics</i></b> Shortcode</label></th>
            <td><fieldset>
            <label for="aia_sc_posts">
            <input name="aia_options[sc_posts]" type="checkbox" id="aia_sc_posts" value="1" <?php checked('1', $aia_options['sc_posts']) ; ?> />
            <?php _e('Enable shortcodes for posts and pages', WPGFORM_I18N_DOMAIN);?></label>
            <br />
            <label for="aia_sc_widgets">
            <input name="aia_options[sc_widgets]" type="checkbox" id="aia_sc_widgets" value="1" <?php checked('1', $aia_options['sc_widgets']) ; ?> />
            <?php _e('Enable shortcodes in text widget', WPGFORM_I18N_DOMAIN);?></label>
            </fieldset></td>
        </tr>
        <tr valign="top">
            <th scope="row"><label><b><i>iAthletics</i></b> CSS</label></th>
            <td><fieldset>
            <label for="aia_default_css">
            <input name="aia_options[default_css]" type="checkbox" id="aia_default_css" value="1" <?php checked('1', $aia_options['default_css']) ; ?> />
            <?php _e('Enable default Appapro iAthletics CSS', WPGFORM_I18N_DOMAIN);?></label>
            <br />
            <label for="aia_custom_css">
            <input name="aia_options[custom_css]" type="checkbox" id="aia_custom_css" value="1" <?php checked('1', $aia_options['custom_css']) ; ?> />
            <?php _e('Enable custom Appapro iAthletics CSS', WPGFORM_I18N_DOMAIN);?></label>
            </fieldset></td>
        </tr>
        <tr valign="top">
        <th scope="row"><label><?php printf(__('Custom %s CSS', WPGFORM_I18N_DOMAIN), 'iAthletics');?></label><br/><small><i><?php _e('Optional CSS styles to control the appearance of the iAthletics content.', WPGFORM_I18N_DOMAIN);?></i></small></th>
            <td>
            <textarea class="regular-text code" name="aia_options[custom_css_styles]" rows="15" cols="80"  id="aia_custom_css_styles"><?php echo $aia_options['custom_css_styles']; ?></textarea>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row"><label><?php _e('Donation Request', WPGFORM_I18N_DOMAIN);?></label></th>
            <td><fieldset>
            <label for="aia_donation_message">
            <table style="padding: 0px;" border="0" cellpadding="0" cellspacing="0">
            <tr>
            <td style="padding: 5px 0px; vertical-align: top;">
            <input name="aia_options[donation_message]" type="checkbox" id="aia_donation_message" value="1" <?php checked('1', $aia_options['donation_message']) ; ?> />
            </td>
            <td style="padding: 5px;">
            <?php _e('Hide the request for donation at the top of this page.<br/><small>The donation request will remain on the <b>About</b> tab.</small>', WPGFORM_I18N_DOMAIN);?>
            </td>
            </tr>
            </table>
            </label>
            </fieldset></td>
        </tr>
    </table>
    <br /><br />
    <input name="aia_options[enable_debug]" type="hidden" id="aia_enable_debug" value="<?php echo $aia_options['enable_debug'] ; ?>" />
    <input name="aia_options[fsockopen_transport]" type="hidden" id="aia_fsockopen_transport" value="<?php echo $aia_options['fsockopen_transport'] ; ?>" />
    <input name="aia_options[streams_transport]" type="hidden" id="aia_streams_transport" value="<?php echo $aia_options['streams_transport'] ; ?>" />
    <input name="aia_options[curl_transport]" type="hidden" id="aia_curl_transport" value="<?php echo $aia_options['curl_transport'] ; ?>" />
    <input name="aia_options[ssl_verify]" type="hidden" id="aia_ssl_verify" value="<?php echo $aia_options['ssl_verify'] ; ?>" />
    <input name="aia_options[local_ssl_verify]" type="hidden" id="aia_local_ssl_verify" value="<?php echo $aia_options['local_ssl_verify'] ; ?>" />
    <input name="aia_options[http_request_timeout]" type="hidden" id="aia_http_request_timeout" value="<?php echo $aia_options['http_request_timeout'] ; ?>" />
    <input name="aia_options[http_request_timeout_value]" type="hidden" id="aia_http_request_timeout_value" value="<?php echo $aia_options['http_request_timeout_value'] ; ?>" />
<?php
}

/**
 * aia_settings_advanced_options()
 *
 * Build the form content and populate with any current plugin settings.
 *
 * @return none
 */
function aia_settings_advanced_options()
{
    $aia_options = aia_get_plugin_options() ;
?>
    <table class="form-table">
        <tr valign="top">
            <th scope="row"><label><?php _e('HTTP API Timeout', WPGFORM_I18N_DOMAIN);?></label></th>
            <td><fieldset>
            <label for="aia_http_api_timeout">
            <select style="width: 150px;" name="aia_options[http_api_timeout]" id="aia_http_api_timeout">
            <option value="5" <?php selected($aia_options['http_api_timeout'], 5); ?>>5 Seconds</option>
            <option value="10" <?php selected($aia_options['http_api_timeout'], 10); ?>>10 Seconds</option>
            <option value="15" <?php selected($aia_options['http_api_timeout'], 15); ?>>15 Seconds</option>
            <option value="25" <?php selected($aia_options['http_api_timeout'], 25); ?>>25 Seconds</option>
            <option value="30" <?php selected($aia_options['http_api_timeout'], 30); ?>>30 Seconds</option>
            <option value="45" <?php selected($aia_options['http_api_timeout'], 45); ?>>45 Seconds</option>
            <option value="60" <?php selected($aia_options['http_api_timeout'], 60); ?>>60 Seconds</option>
            </select>
            <br />
            <small><?php _e('Change the default HTTP API Timeout setting (default is 5 seconds).', WPGFORM_I18N_DOMAIN);?></small></label>
            </fieldset></td>
        </tr>
        <tr valign="top">
        <th scope="row"><label><?php _e('Enable Debug', WPGFORM_I18N_DOMAIN);?></label></th>
            <td><fieldset>
            <label for="aia_enable_debug">
            <table style="padding: 0px;" border="0" cellpadding="0" cellspacing="0">
            <tr>
            <td style="padding: 5px 0px; vertical-align: top;">
            <input name="aia_options[enable_debug]" type="checkbox" id="aia_enable_debug" value="1" <?php checked('1', $aia_options['enable_debug']) ; ?> />
            </td>
            <td style="padding: 5px;">
            <?php printf(__('Enabling debug will collect data during the data rendering and processing process.<p>The data is added to the page footer but hidden with a link appearing above the form which can toggle the display of the debug data.  This data is useful when trying to understand why the plugin isn\'t operating as expected.</p><p>When debugging is enabled, specific transports employed by the <a href="%s">WordPress HTTP API</a> can optionally be disabled.  While rarely required, disabling transports can be useful when the plugin is not communcating correctly with the Appapro iAthletics API.  <i>Extra care should be taken when disabling transports as other aspects of WordPress may not work correctly.</i>  The <a href="%s">WordPress Core Control</a> plugin is recommended for advanced debugging of <a href="%s">WordPress HTTP API issues.</a></p>', WPGFORM_I18N_DOMAIN), 'http://codex.wordpress.org/HTTP_API', 'http://wordpress.org/extend/plugins/core-control/', 'http://codex.wordpress.org/HTTP_API');?>
            </td>
            </tr>
            </table>
            </label>
            </fieldset></td>
        </tr>
        <tr valign="top">
            <th scope="row"><label><?php _e('WordPress HTTP API<br/>Transport Control', WPGFORM_I18N_DOMAIN);?></label></th>
            <td><fieldset>
            <label for="aia_fsockopen_transport">
            <input name="aia_options[fsockopen_transport]" type="checkbox" id="aia_fsockopen_transport" value="1" <?php checked('1', $aia_options['fsockopen_transport']) ; ?> />
            <?php _e('Disable <i><b>FSockOpen</b></i> Transport', WPGFORM_I18N_DOMAIN);?></label>
            <br />
            <label for="aia_streams_transport">
            <input name="aia_options[streams_transport]" type="checkbox" id="aia_streams_transport" value="1" <?php checked('1', $aia_options['streams_transport']) ; ?> />
            <?php _e('Disable <i><b>Streams</b></i> Transport', WPGFORM_I18N_DOMAIN);?></label>
            <br />
            <label for="aia_curl_transport">
            <input name="aia_options[curl_transport]" type="checkbox" id="aia_curl_transport" value="1" <?php checked('1', $aia_options['curl_transport']) ; ?> />
            <?php _e('Disable <i><b>cURL</b></i> Transport', WPGFORM_I18N_DOMAIN);?></label>
            <br />
            <label for="aia_ssl_verify">
            <input name="aia_options[ssl_verify]" type="checkbox" id="aia_ssl_verify" value="1" <?php checked('1', $aia_options['ssl_verify']) ; ?> />
            <?php _e('Disable <i><b>SSL Verify</b></i>', WPGFORM_I18N_DOMAIN);?></label>
            <br />
            <label for="aia_local_ssl_verify">
            <input name="aia_options[local_ssl_verify]" type="checkbox" id="aia_local_ssl_verify" value="1" <?php checked('1', $aia_options['local_ssl_verify']) ; ?> />
            <?php _e('Disable <i><b>Local SSL Verify</b></i>', WPGFORM_I18N_DOMAIN);?></label>
            </fieldset></td>
        </tr>
        <tr valign="top">
            <th scope="row"><label><?php _e('HTTP Request Timeout', WPGFORM_I18N_DOMAIN);?></label></th>
            <td><fieldset>
            <label for="aia_http_request_timeout">
            <input name="aia_options[http_request_timeout]" type="checkbox" id="aia_http_request_timeout" value="1" <?php checked('1', $aia_options['http_request_timeout']) ; ?> />
            <?php _e('Change <i><b>HTTP Request Timeout</b></i>', WPGFORM_I18N_DOMAIN);?></label>
            <br />
            <label for="aia_http_request_timeout_value">
            <input name="aia_options[http_request_timeout_value]" type="text" id="aia_http_request_timeout_value" value="<?php echo $aia_options['http_request_timeout_value'] ; ?>" /><br />
           <small><?php _e('(in seconds)', WPGFORM_I18N_DOMAIN);?></small></label>
            </fieldset></td>
        </tr>
    </table>
    <br /><br />
    <input name="aia_options[api_key]" type="hidden" id="aia_api_key" value="<?php echo $aia_options['api_key'] ; ?>" />
    <input name="aia_options[sc_posts]" type="hidden" id="aia_sc_posts" value="<?php echo $aia_options['sc_posts'] ; ?>" />
    <input name="aia_options[sc_widgets]" type="hidden" id="aia_sc_widgets" value="<?php echo $aia_options['sc_widgets'] ; ?>" />
    <input name="aia_options[default_css]" type="hidden" id="aia_default_css" value="<?php echo $aia_options['default_css'] ; ?>" />
    <input name="aia_options[custom_css]" type="hidden" id="aia_custom_css" value="<?php echo $aia_options['custom_css'] ; ?>" />
    <input name="aia_options[custom_css_styles]" type="hidden" id="aia_custom_css_styles" value="<?php echo $aia_options['custom_css_styles'] ; ?>" />
    <input name="aia_options[donation_message]" type="hidden" id="aia_donation_message" value="<?php echo $aia_options['donation_message'] ; ?>" />
<?php
}
?>
