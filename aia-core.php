<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
/**
 * GForm functions.
 *
 * $Id$
 *
 * (c) 2011 by Mike Walsh
 *
 * @author Mike Walsh <mike@walshcrew.com>
 * @package iAthletics
 * @subpackage functions
 * @version $Revision$
 * @lastmodified $Date$
 * @lastmodifiedby $Author$
 *
 */

// Filesystem path to this plugin.
define('AIA_PREFIX', 'aia_') ;
define('AIA_PATH', WP_PLUGIN_DIR.'/'.dirname(plugin_basename(__FILE__))) ;

//  Appapro iAthletics API
define('AIA_API', 'http://www.appapro.com/iAthletics/services/version1_0.asmx') ;
define('AIA_API_DEVICE_ID', 'WordPress') ;
define('AIA_API_DEVICE_OS', PHP_OS) ;
define('AIA_API_DEVICE_VER', get_bloginfo('version')) ;

// i18n plugin domain
define( 'AIA_I18N_DOMAIN', 'aia' );

/**
 * Initialise the internationalisation domain
 */
$is_aia_i18n_setup = false ;
function aia_init_i18n()
{
	global $is_aia_i18n_setup;

	if ($is_aia_i18n_setup == false) {
		load_plugin_textdomain(AIA_I18N_DOMAIN, false, dirname(plugin_basename(__FILE__)) . '/languages/') ;
		$is_aia_i18n_setup = true;
	}
}



//  Need the plugin options to initialize debug
$aia_options = aia_get_plugin_options() ;

//  Enable debug content?
define('AIA_DEBUG', $aia_options['enable_debug'] == 1) ;
//define('AIA_DEBUG', true) ;

if (AIA_DEBUG)
{
    error_log(sprintf('%s::%s', basename(__FILE__), __LINE__)) ;
    error_reporting(E_ALL) ;
    require_once('aia-debug.php') ;
    add_action('send_headers', 'aia_send_headers') ;
}

/**
 * aia_init()
 *
 * Init actions to enable shortcodes.
 *
 * @return null
 */
function aia_init()
{
    $aia_options = aia_get_plugin_options() ;

    if ($aia_options['sc_posts'] == 1)
    {
        add_shortcode('aia', array('iAthletics', 'aia_sc')) ;
    }

    if ($aia_options['sc_widgets'] == 1)
        add_filter('widget_text', 'do_shortcode') ;

    add_filter('the_content', 'wpautop');
    //add_filter('the_content', 'aia_the_content');
    add_action('template_redirect', 'aia_head') ;
    add_action('wp_footer', 'aia_footer') ;
}

/**
 * Filter to render a iAthletics when a public CPT URL is
 * requested.  The filter will inject the proper shortcode into
 * the content which is then in turn processed by WordPress to
 * render the form as a regular short code would be processed.
 *
 * @param $content string post content
 * @since v0.46
 */
function aia_the_content($content)
{
    return (AIA_CPT_FORM == get_post_type(get_the_ID())) ?
        sprintf('[aia id=\'%s\']', get_the_ID()) : $content ;
}

//add_action('init', array('iAthletics', 'ProcessiAthletics')) ;

/**
 * Returns the default options for iAthletics.
 *
 * @since iAthletics 0.11
 */
function aia_get_default_plugin_options()
{
	$default_plugin_options = array(
        'api_key' => 1
       ,'date_format' => 'D M d, Y h:i a'
       ,'sc_posts' => 1
       ,'sc_widgets' => 1
       ,'default_css' => 1
       ,'custom_css' => 0
       ,'custom_css_styles' => ''
       ,'donation_message' => 0
       ,'http_api_timeout' => 5
       ,'enable_debug' => 0
       ,'fsockopen_transport' => 0
       ,'streams_transport' => 0
       ,'curl_transport' => 0
       ,'local_ssl_verify' => 0
       ,'ssl_verify' => 0
       ,'http_request_timeout' => 0
       ,'http_request_timeout_value' => 30
	) ;

	return apply_filters('aia_default_plugin_options', $default_plugin_options) ;
}

/**
 * Returns the options array for the iAthletics plugin.
 *
 * @since iAthletics 0.11
 */
function aia_get_plugin_options()
{
    //  Get the default options in case anything new has been added
    $default_options = aia_get_default_plugin_options() ;

    //  If there is nothing persistent saved, return the default

    if (get_option('aia_options') === false)
        return $default_options ;

    //  One of the issues with simply merging the defaults is that by
    //  using checkboxes (which is the correct UI decision) WordPress does
    //  not save anything for the fields which are unchecked which then
    //  causes wp_parse_args() to incorrectly pick up the defaults.
    //  Since the array keys are used to build the form, we need for them
    //  to "exist" so if they don't, they are created and set to null.

    $plugin_options = wp_parse_args(get_option('aia_options'), $default_options) ;

    //  If the array key doesn't exist, it means it is a check box option
    //  that is not enabled so the array element(s) needs to be set to zero.

    //foreach ($default_options as $key => $value)
    //    if (!array_key_exists($key, $plugin_options)) $plugin_options[$key] = 0 ;

    return $plugin_options ;
}

/**
 * aia_admin_menu()
 *
 * Adds admin menu page(s) to the Dashboard.
 *
 * @return null
 */
function aia_admin_menu()
{
    aia_init_i18n() ;
    require_once(AIA_PATH . '/aia-options.php') ;

    $aia_options_page = add_options_page(
        __('Appapro iAthletics', AIA_I18N_DOMAIN),
        __('Appapro iAthletics', AIA_I18N_DOMAIN),
        'manage_options', 'aia-options.php', 'aia_options_page') ;
    add_action('admin_footer-'.$aia_options_page, 'aia_options_admin_footer') ;
    add_action('admin_print_scripts-'.$aia_options_page, 'aia_options_print_scripts') ;
    add_action('admin_print_styles-'.$aia_options_page, 'aia_options_print_styles') ;

    if (0):
    add_submenu_page(
        'edit.php?post_type=aia',
        'WordPress iAthletics Submission Log', /*page title*/
        'Form Submission Log', /*menu title*/
        'manage_options', /*roles and capabiliyt needed*/
        'aia-entry-log-page',
        'aia_entry_log_page' /*replace with your own function*/
    );
    endif;
}

function aia_entry_log_page()
{
    require_once('aia-logging.php') ;
}



/**
 * aia_admin_init()
 *
 * Init actions for the Dashboard interface.
 *
 * @return null
 */
function aia_admin_init()
{
    register_setting('aia_options', 'aia_options') ;
}

/**
 * aia_register_activation_hook()
 *
 * Adds the default options so WordPress options are
 * configured to a default state upon plugin activation.
 *
 * @return null
 */
function aia_register_activation_hook()
{
    aia_init_i18n() ;
    add_option('aia_options', aia_get_default_plugin_options()) ;
    add_filter('widget_text', 'do_shortcode') ;
}

/**
 * iAthletics class definition
 *
 * @author Mike Walsh <mike@walshcrew.com>
 * @access public
 * @see wp_remote_get()
 * @see wp_remote_post()
 * @see RenderiAthletics()
 * @see ConstructiAthletics()
 */
class iAthletics
{
    /**
     * Property to hold iAthletics Response
     */
    static $response ;

    /**
     * Property to hold iAthletics Post Error
     */
    static $post_error = false ;

    /**
     * Property to hold iAthletics Post Status
     */
    static $posted = false ;

    /**
     * Property to indicate Javascript output state
     */
    static $aia_js = false ;

    /**
     * Property to store Javascript output in footer
     */
    static $aia_footer_js = '' ;

    /**
     * Property to indicate CSS output state
     */
    static $aia_css = false ;

    /**
     * Property to indicate Debug output state
     */
    static $aia_debug = false ;

    /**
     * Property to store the various options which control the
     * HTML manipulation and generation.  These array keys map
     * to the meta data stored with the iAthletics Custom Post Type.
     *
     * The Unite theme from Paralleus mucks with the submit buttons
     * which breaks the ability to submit the form to Google correctly.
     * This "special" hack will "unbreak" the submit buttons.
     *
     */
    protected static $options = array(
        'api_key'        => false,          // iAthletics API key
    ) ;

    /**
     * Constructor
     */
    function iAthletics()
    {
        // empty for now
    }

    /**
     * 'aia' short code handler
     *
     * @since 1.0
     */
    function aia_sc($options)
    {
        if (self::ProcessiAthleticsCPT($options))
            return self::ConstructiAthletics() ;
        else
            return sprintf('<div class="aia-error aia-error">%s</div>',
               __('Unable to process iAthletics short code.', AIA_I18N_DOMAIN)) ;
    }

    /**
     * Function ProcessShortcode loads HTML from a iAthletics URL,
     * processes it, and inserts it into a WordPress filter to output
     * as part of a post, page, or widget.
     *
     * @param $options array Values passed from the shortcode.
     * @see gform_sc
     * @return boolean - abort processing when false
     */
    function ProcessShortCodeOptions($options)
    {
        //  Property short cut
        $o = &self::$options ;

        //  Override default options based on the short code attributes

        foreach ($o as $key => $value)
        {
            if (array_key_exists($key, $options))
                $o[$key] = $options[$key] ;
        }

        if (AIA_DEBUG) aia_whereami(__FILE__, __LINE__, 'ProcessShortCodeOptions') ;
        if (AIA_DEBUG) aia_preprint_r($o) ;

        //  Have to have a API Key otherwise the short code is meaningless!

        return (!empty($o['api_key'])) ;
    }

    /**
     * Function ProcessShortcode loads HTML from a iAthletics URL,
     * processes it, and inserts it into a WordPress filter to output
     * as part of a post, page, or widget.
     *
     * @param $options array Values passed from the shortcode.
     * @see RenderiAthletics
     * @return boolean - abort processing when false
     */
    function ProcessiAthleticsCPT($options)
    {
        //  Property short cut
        $o = &self::$options ;

        //  API Key?  Required - make sure it is reasonable.

        if ((empty($options) || !array_key_exists('api_key', $options)))
        {
            $aia_options = aia_get_plugin_options() ;
            $options['api_key'] = $aia_options['api_key'] ;
        }

        if ($options['api_key'])
        {
            $o['api_key'] = $options['api_key'] ;

            //  Make sure we didn't get something nonsensical
            if (is_numeric($o['api_key']) && ($o['api_key'] > 0) && ($o['api_key'] == round($o['api_key'])))
                $o['api_key'] = (int)$o['api_key'] ;
            else
                return false ;
        }
        else
        {
            return false ;
        }

        // get current form meta data

        if (0):
        $mb = aia_form_meta_box_content() ;

        foreach ($mb['fields'] as $field)
        {
            //  Only show the fields which are not hidden
            if ($field['type'] !== 'hidden')
            {
                // get current post meta data
                $meta = get_post_meta($o['api_key'], $field['api_key'], true);

                //  If a meta value is found, strip off the prefix
                //  from the meta key so the api_key matches the options
                //  used by the form rendering method.

                if ($meta)
                    $o[substr($field['api_key'], strlen(AIA_PREFIX))] = $meta ;
            }
        }

        if (AIA_DEBUG) aia_whereami(__FILE__, __LINE__, 'ProcessiAthleticsCPT') ;
        if (AIA_DEBUG) aia_preprint_r($o) ;

        //  Have to have an API KEY otherwise the short code is meaningless!
        endif;

        return (!empty($o['api_key'])) ;
    }

    /**
     * Function ConstructiAthletics loads HTML from a iAthletics URL,
     * processes it, and inserts it into a WordPress filter to output
     * as part of a post, page, or widget.
     *
     * @return An HTML string if successful, false otherwise.
     * @see RenderiAthletics
     */
    function ConstructiAthletics()
    {
        $locale_cookie = new WP_HTTP_Cookie(array('name' => 'locale', 'value' => get_locale())) ;

        //  Property short cut
        $o = &self::$options ;

        $aia_options = aia_get_plugin_options() ;

        if (AIA_DEBUG && $aia_options['http_request_timeout'])
            $timeout = $aia_options['http_request_timeout_value'] ;
        else
            $timeout = $aia_options['http_api_timeout'] ;

        if (AIA_DEBUG) aia_whereami(__FILE__, __LINE__, 'ConstructiAthletics') ;
        if (AIA_DEBUG) aia_preprint_r($_POST) ;

        //  If no API Key then return as nothing useful can be done.
        if (!$o['api_key'])
        {
            //return false; 
            $api_key = $aia_options['api_key'] ;
        }
        else
        {
            $api_key = $o['api_key'] ;
        }

        if (AIA_DEBUG) aia_whereami(__FILE__, __LINE__, 'ConstructiAthletics') ;

        //  Start building content ... the information comes from the Appapro API in
        //  XML format.  Need to make a series of API calls in order to construct the
        //  data in a format we can use it.

        $aia_html = '' ;
        $aia = array() ;

        $season_xml = self::iAthleticsAPI('GetAppSeasons', array('appId' => $api_key)) ;

        if (is_wp_error($season_xml))
            return self::iAthleticsAPIError($season_xml) ;

        if (AIA_DEBUG)
            $debug = '<h2 class="aia-debug"><a href="#" class="aia-debug-wrapper">Show iAthletics Debug Content</a></h2>' ;
        else
            $debug = '' ;

        $css = '' ;
        $html = '<div class="aia-accordion">' ;
        $onetime_html = '' ;

        //  Assemble final HTML to return by traversing the returned XML and
        //  making subsequent API calls based on the data encoutnered in the XML.

        if (property_exists($season_xml, 'AppSeasons'))
        {
            $appSeasons = &$season_xml->AppSeasons->AppSeason ;

            $x = false ;

            //  Loop through each season

            foreach ($appSeasons as $appSeason)
            {
                $html .= sprintf('<h3>%s</h3>%s<div class="aia-season">', $appSeason->SeasonName, PHP_EOL) ;
                //$html .= sprintf('<p>Season Id:  %s</p>', (string)$appSeason->SeasonId) ;

                //  Each season has a collection of teams

                $teams_xml = self::iAthleticsAPI('GetSportListForSeason',
                    array('appId' => $api_key, 'seasonId' => (string)$appSeason->SeasonId)) ;

                $teams = &$teams_xml->SportListItems->SportListItem ;

                //  Loop through the list of teams

                $html .= '<div class="aia-accordion">' ;
                foreach ($teams as $team)
                {
                    $html .= sprintf('<h4>%s %s</h4><div class="aia-team">',
                        (string)$team->TeamName, (string)$team->SportName) ;
                    //$html .= sprintf('<p>Team Id:  %s</p>', (string)$team->TeamId) ;

                    //  Each team has News, Schedule, and Roster

                if (!$x) {
                    //aia_whereami(__FILE__, __LINE__, 'ConstructiAthletics') ;
                    //aia_preprint_r($team) ;
                    //$x = true ;

                    //  Each team has a schedule

                    $events_lut = array(
                        'EventDateTime' => __('Date / Time', AIA_I18N_DOMAIN)
                       //,'TeamName' => __('Team Name', AIA_I18N_DOMAIN)
                       //,'SportName' => __('Sport Name', AIA_I18N_DOMAIN)
                       ,'EventName' => __('Event Name', AIA_I18N_DOMAIN)
                       //,'AwayEvent' => __('Away Event', AIA_I18N_DOMAIN)
                       //,'ConferenceEvent' => __('Conference Event', AIA_I18N_DOMAIN)
                    ) ;

                    $events_xml = self::iAthleticsAPI('GetCompleteTeamSchedule', array('appId' => $api_key,
                        'seasonId' => (string)$appSeason->SeasonId, 'teamId' => (string)$team->TeamId)) ;

                    $events = &$events_xml->Events->Event ;

                    //  Set up a table to store the event schedule

                    $schedule = '<table class="aia-schedule">' . PHP_EOL . '<thead class="aia-schedule">' . PHP_EOL ;
                    $schedule .= sprintf('<thead>%s<tr>%s', PHP_EOL, PHP_EOL) ;

                    foreach($events_lut as $key => $value)
                        $schedule .= sprintf('<th>%s</th>%s', $value, PHP_EOL) ;

                    $schedule .= sprintf('</thead>%s</tr>%s', PHP_EOL, PHP_EOL) ;

                    $html .= '<div class="aia-accordion">' ;

                    $html .= sprintf('<h5>%s %s %s</h5><div class="aia-event">',
                        (string)$team->TeamName, (string)$team->SportName, __('Schedule', AIA_I18N_DOMAIN)) ;

                    //  Loop through the events and add a table row for each

                    $schedule .= sprintf('<tbody>%s', PHP_EOL) ;

                    foreach ($events as $event)
                    {
                        $schedule .= sprintf('<tr>%s', PHP_EOL) ;

                        foreach($events_lut as $key => $value)
                            if ('EventDateTime' === $key)
                                $schedule .= sprintf('<td>%s</td>%s',
                                    date($aia_options['date_format'], strtotime((string)$event->{$key})), PHP_EOL) ;
                            else
                                $schedule .= sprintf('<td>%s</td>%s', (string)$event->{$key}, PHP_EOL) ;

                        $schedule .= sprintf('</tr>%s', PHP_EOL) ;
                        //$html .= sprintf('<p>Event Id:  %s</p>', (string)$event->eventId) ;
                    }

                    $schedule .= sprintf('</tbody>%s</table>%s', PHP_EOL, PHP_EOL) ;

                    $html .= sprintf('%s</div>%s', $schedule, PHP_EOL) ;

                    $html .= sprintf('</div>%s', PHP_EOL) ;

                    //  Each team has a roster

                    $rosters_lut = array(
                        'Name' => __('Name', AIA_I18N_DOMAIN)
                       ,'Number' => __('Number', AIA_I18N_DOMAIN)
                       ,'Grade' => __('Grade', AIA_I18N_DOMAIN)
                       ,'Height' => __('Height', AIA_I18N_DOMAIN)
                       ,'Weight' => __('Weight', AIA_I18N_DOMAIN)
                       ,'Positions' => __('Positions', AIA_I18N_DOMAIN)
                    ) ;

                    $rosters_xml = self::iAthleticsAPI('GetTeamRoster', array('appId' => $api_key,
                        'seasonId' => (string)$appSeason->SeasonId, 'teamId' => (string)$team->TeamId)) ;

                    $players = &$rosters_xml->Roster->Player ;

                    //  Set up a table to store the players details

                    $roster = '<table class="aia-roster">' . PHP_EOL . '<thead class="aia-roster">' . PHP_EOL ;
                    $roster .= sprintf('<thead class="aia-roster">%s<tr>%s', PHP_EOL, PHP_EOL) ;

                    foreach($rosters_lut as $key => $value)
                        $roster .= sprintf('<th class="aia-roster">%s</th>%s', $value, PHP_EOL) ;

                    $roster .= sprintf('</thead>%s</tr>%s', PHP_EOL, PHP_EOL) ;

                    $html .= '<div class="aia-accordion">' ;

                    $html .= sprintf('<h5>%s %s %s</h5><div class="aia-roster">',
                        (string)$team->TeamName, (string)$team->SportName, __('Roster', AIA_I18N_DOMAIN)) ;

                    //  Loop through the rosters and add a table row for each

                    $roster .= sprintf('<tbody class="aia-roster">%s', PHP_EOL) ;

                    foreach ($players as $player)
                    {
                        $roster .= sprintf('<tr class="aia-roster">%s', PHP_EOL) ;

                        foreach($rosters_lut as $key => $value)
                            $roster .= sprintf('<td class="aia-roster">%s</td>%s', (string)$player->{$key}, PHP_EOL) ;

                        $roster .= sprintf('</tr>%s', PHP_EOL) ;
                    }

                    $roster .= sprintf('</tbody>%s</table>%s', PHP_EOL, PHP_EOL) ;

                    $html .= sprintf('%s</div>%s', $roster, PHP_EOL) ;

                    $html .= sprintf('</div>%s', PHP_EOL) ;
                }

                    $html .= sprintf('</div>%s', PHP_EOL) ;
                }

                $html .= sprintf('</div>', PHP_EOL) ;

                $html .= sprintf('</div>', PHP_EOL) ;
            }

            $html .= sprintf('</div>', PHP_EOL) ;
        }
        else
        {
            return sprintf('<div class="aia-error">%s</div>',
               __('No Season Information returned from iAthletics.', AIA_I18N_DOMAIN)) ;
        }
 
        if (!self::$aia_css)
        {
            $onetime_html .= PHP_EOL . $css ;
            self::$aia_css = true ;
        }

        if (!self::$aia_debug)
        {
            $onetime_html .= $debug ;
            self::$aia_debug = true ;
        }

        $html = $onetime_html . $html ;

        return $html ;
    }

    /**
     * iAthleticsAPI - call the iAthletics API
     *
     * @param $apiCall string API entry point
     * @param $args mixed arguments for the API entry point
     *
     * @return mixed XML object (response) or WP_Error obect (error)
     */
    function iAthleticsAPI($apiCall, $args)
    {
        $defaults = array(
            'appId' => 0
           ,'deviceId' => htmlentities(AIA_API_DEVICE_ID)
        ) ;

        $args = wp_parse_args($args, $defaults) ;

        $api = add_query_arg($args, sprintf('%s/%s', AIA_API, $apiCall)) ;

        //  Handle HTTP API timeout setting
        $aia_options = aia_get_plugin_options() ;

        if (AIA_DEBUG && $aia_options['http_request_timeout'])
            $timeout = $aia_options['http_request_timeout_value'] ;
        else
            $timeout = $aia_options['http_api_timeout'] ;

        error_log(sprintf('%s::%s', basename(__FILE__), __LINE__)) ;
        $response = wp_remote_get($api, array('sslverify' => false, 'timeout' => $timeout)) ;
        error_log(sprintf('%s::%s', basename(__FILE__), __LINE__)) ;

        //  Retrieve the XML from the iAthletics API

        if (is_wp_error($response))
        {
            //aia_whereami(__FILE__, __LINE__) ;
            //$error_string = $response->get_error_message();
            //echo '<div id="message" class="aia-error"><p>' . $error_string . '</p></div>';
            if (AIA_DEBUG)
            {
                printf('<h2>%s::%s</h2>', basename(__FILE__), __LINE__) ;
                printf('<pre>%s</pre>', print_r($response, true)) ;
                //aia_whereami(__FILE__, __LINE__, 'iAthleticsAPI') ;
                //aia_preprint_r($response) ;
            }

            $xml = new WP_Error('API Error',
                __('iAthletics API error, please try reloading this page.', AIA_I18N_DOMAIN)) ;
            $xml->add('HTTP API Error', $response->get_error_message()) ;
        }
        else
        {
            $xml = simplexml_load_string($response['body']) ;
        }

        error_log(sprintf('%s::%s', basename(__FILE__), __LINE__)) ;
        return $xml ;
    }

    /**
     * iAthleticsAPIError - build an error message
     *
     * @param $error WP_Error arguments for the API error
     * @return $html string error message(s)
     * @see WP_Error
     */
    function iAthleticsAPIError($error)
    {
        $html = '<div class="aia-error">' ;

        if (is_wp_error($error))
        {
            $codes = $error->get_error_codes() ;

            foreach ($codes as $code)
            {
                $messages = $error->get_error_messages($code) ;

                foreach ($messages as $message)
                {
                    $html .= sprintf('<span class="aia-error-code">%s:</span>', $code) ;
                    $html .= sprintf('<span class="aia-error-message">%s:</span><br/>', $message) ;
                }
            }
        }
        else
        {
            $html .= __('Did not find expected WP_Error object.', AIA_I18N_DOMAIN) ;
        }

        $html .= '</div>' ;

        return $html ;
    }

    /**
     * WordPress Shortcode handler.
     *
     * @return HTML
     */
    function RenderiAthletics($atts) {
        $params = shortcode_atts(iAthletics::$options) ;

        return iAthletics::ConstructiAthletics($params) ;
    }
}

/**
 * aia_head()
 *
 * WordPress header actions
 */
function aia_head()
{
    //  iAthletics needs jQuery!
    wp_enqueue_script('jquery') ;
    wp_enqueue_script('jquery-ui') ;
    wp_enqueue_script('jquery-ui-accordion') ;
    
    $aia_options = aia_get_plugin_options() ;

    //  Need the jQuery UI CSS - load it from Google's CDN

    wp_enqueue_style('jquery-style',
        'http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/themes/smoothness/jquery-ui.css') ;

    //  Load default iAthletics CSS?
    if ($aia_options['default_css'] == 1)
    {
        wp_enqueue_style('aia-css',
            plugins_url(plugin_basename(dirname(__FILE__) . '/css/aia.css'))) ;
    }
}

/**
 * aia_footer()
 *
 * WordPress footer actions
 *
 */
function aia_footer()
{
    //  Output the generated jQuery script as part of the footer

    iAthletics::$aia_footer_js = sprintf('
<script type="text/javascript">
    //  Apparo iAthletics v%s jQuery script
    jQuery(document).ready(function($) {
        $(".aia-accordion").accordion({ collapsible: true, active: false, heightStyle: "content" });
    });
</script>
', AIA_VERSION) ;

    print iAthletics::$aia_footer_js ;
    
}
?>
