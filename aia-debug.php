<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
/**
 * GForm functions.
 *
 * $Id$
 *
 * (c) 2013 by Mike Walsh
 *
 * @author Mike Walsh <mike@walshcrew.com>
 * @package iAthletics
 * @subpackage functions
 * @version $Revision$
 * @lastmodified $Date$
 * @lastmodifiedby $Author$
 *
 */

global $aia_debug_content ;

$aia_debug_content = '' ;
add_action('init', 'aia_debug', 0) ;
add_action('wp_footer', 'aia_show_debug_content') ;

//  In debug mode several filters can be disabled for debugging purposes.

$aia_options = aia_get_plugin_options() ;

//  Change the HTTP Time out
if ($aia_options['http_request_timeout'] == 1)
{
    if (is_int($aia_options['http_request_timeout_value'])
        || ctype_digit($aia_options['http_request_timeout_value']))
        add_filter('http_request_timeout', 'aia_http_request_timeout') ;
}

//  Disable fsockopen transport?
if ($aia_options['fsockopen_transport'] == 1)
    add_filter('use_fsockopen_transport', '__return_false') ;

//  Disable streams transport?
if ($aia_options['streams_transport'] == 1)
    add_filter('use_streams_transport', '__return_false') ;

//  Disable curl transport?
if ($aia_options['curl_transport'] == 1)
    add_filter('use_curl_transport', '__return_false') ;

//  Disable local ssl verify?
if ($aia_options['local_ssl_verify'] == 1)
    add_filter('https_local_ssl_verify', '__return_false') ;

//  Disable ssl verify?
if ($aia_options['ssl_verify'] == 1)
    add_filter('https_ssl_verify', '__return_false') ;

/**
 * Optional filter to change HTTP Request Timeout
 *
 */
function aia_http_request_timeout($timeout) {
    $aia_options = aia_get_plugin_options() ;
    return $aia_options['http_request_timeout'] ;
}

/**
 * Debug action to examine server variables
 *
 */
function aia_debug()
{
    global $wp_filter ;

    aia_error_log($_POST) ;

    if (!is_admin())
    {
        aia_whereami(__FILE__, __LINE__, '$_SERVER') ;
        aia_preprint_r($_SERVER) ;
        aia_whereami(__FILE__, __LINE__, '$_ENV') ;
        aia_preprint_r($_ENV) ;
        aia_whereami(__FILE__, __LINE__, '$_POST') ;
        aia_preprint_r($_POST) ;
        aia_whereami(__FILE__, __LINE__, '$_GET') ;
        aia_preprint_r($_GET) ;
        aia_whereami(__FILE__, __LINE__, 'locale') ;
        aia_preprint_r(get_locale()) ;
        aia_preprint_r(setlocale(LC_ALL,NULL)) ;

        if (array_key_exists('init', $wp_filter))
        {
            aia_whereami(__FILE__, __LINE__, '$wp_filter[\'init\']') ;
            aia_preprint_r($wp_filter['init']) ;
        }
        if (array_key_exists('template_redirect', $wp_filter))
        {
            aia_whereami(__FILE__, __LINE__, '$wp_filter[\'template_redirect\']') ;
            aia_preprint_r($wp_filter['template_redirect']) ;
        }
    }
}

/**
 * Debug action to display debug content in a DIV which can be toggled open and closed.
 *
 */
function aia_show_debug_content()
{
    global $aia_debug_content ;
?>
<style>
h2.aia-debug {
    text-align: center;
    background-color: #ffebe8;
    border: 2px solid #ff0000;
}

div.aia-debug {
    padding: 10px;
}

div.aia-debug h2 {
    background-color: #f00;
}

div.aia-debug h3 {
    padding: 10px;
    color: #fff;
    font-weight: bold;
    border: 1px solid #000000;
    background-color: #024593;
}

div.aia-debug pre {
    color: #000;
    text-align: left;
    border: 1px solid #000000;
    background-color: #c6dffd;
}
</style>
<script type="text/javascript">
jQuery(document).ready(function($) {
        $("div.aia-debug").hide();
        $("a.aia-debug-wrapper").show();
        $("a.aia-debug-wrapper").text("Show iAthletics Debug Content");
 
    $("a.aia-debug-wrapper").click(function(){
    $("div.aia-debug").slideToggle();

    if ($("a.aia-debug-wrapper").text() == "Show iAthletics Debug Content")
        $("a.aia-debug-wrapper").text("Hide iAthletics Debug Content");
    else
        $("a.aia-debug-wrapper").text("Show iAthletics Debug Content");
    });
});
</script>
<div class="aia-debug">
    <?php echo $aia_debug_content ; ?>
</div>
<?php
}

/**
 * aia_send_headers()
 *
 * @return null
 */
function aia_send_headers()
{
    header('Cache-Control: no-cache, must-revalidate'); // HTTP/1.1
    header('Expires: ' . date(DATE_RFC822, strtotime('yesterday'))); // Date in the past
    header('X-Frame-Options: SAMEORIGIN'); 
}

/**
 * Debug "where am i" function
 */
function aia_whereami($f, $l, $s = null)
{
    global $aia_debug_content ;

    if (is_null($s))
    {
        $aia_debug_content .= sprintf('<h3>%s::%s</h3>', basename($f), $l) ;
        error_log(sprintf('%s::%s', basename($f), $l)) ;
    }
    else
    {
        $aia_debug_content .= sprintf('<h3>%s::%s::%s</h3>', basename($f), $l, $s) ;
        error_log(sprintf('%s::%s::%s', basename($f), $l, $s)) ;
    }
}

/**
 * Debug functions
 */
function aia_preprint_r()
{
    global $aia_debug_content ;

    $numargs = func_num_args() ;
    $arg_list = func_get_args() ;
    for ($i = 0; $i < $numargs; $i++) {
	    $aia_debug_content .= sprintf('<pre style="text-align:left;">%s</pre>', print_r($arg_list[$i], true)) ;
    }
    aia_error_log(func_get_args()) ;
}
/**
 * Debug functions
 */
function aia_error_log()
{
    $numargs = func_num_args() ;
    $arg_list = func_get_args() ;
    for ($i = 0; $i < $numargs; $i++) {
	    error_log(print_r($arg_list[$i], true)) ;
    }
}
?>
