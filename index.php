<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
/**
 * Plugin Name: Appapro iAthletics
 * Plugin URI: http://michaelwalsh.org/wordpress/wordpress-plugins/appapro-iathletics/
 * Description: WordPress integration of Appapro iAthletics (http://www.appapro.com).  This plugin uses the Appapro API to display seasons, teams, rosters, schedules, and results, for High School athletics teams.
 * Version: 0.2-beta
 * Build: 0.2-beta
 * Last Modified:  2013-05-23
 * Author: Mike Walsh
 * Author URI: http://www.michaelwalsh.org
 * License: GPL
 * 
 *
 * $Id$
 *
 * (c) 2013 by Mike Walsh
 *
 * @author Mike Walsh <mike@walshcrew.com>
 * @package wpGForm
 * @subpackage admin
 * @version $Rev$
 * @lastmodified $Date$
 * @lastmodifiedby $LastChangedBy$
 *
 */

define('AIA_VERSION', '0.2-beta') ;

require_once('aia-core.php') ;
//require_once('aia-post-type.php') ;

// Use the register_activation_hook to set default values
register_activation_hook(__FILE__, 'aia_register_activation_hook');

// Use the init action
add_action('init', 'aia_init' );

// Use the admin_menu action to add options page
add_action('admin_menu', 'aia_admin_menu');

// Use the admin_init action to add register_setting
add_action('admin_init', 'aia_admin_init' );

?>
