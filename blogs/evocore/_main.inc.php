<?php
/**
 * This file initializes everything BUT the blog!
 *
 * It is useful when you want to do very customized templates!
 * It is also called by more complete initializers.
 *
 * This file is part of the b2evolution/evocms project - {@link http://b2evolution.net/}.
 * See also {@link http://sourceforge.net/projects/evocms/}.
 *
 * @copyright (c)2003-2004 by Francois PLANQUE - {@link http://fplanque.net/}.
 * Parts of this file are copyright (c)2004 by Daniel HAHLER - {@link http://thequod.de/contact}.
 *
 * @license http://b2evolution.net/about/license.html GNU General Public License (GPL)
 * {@internal
 * b2evolution is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * b2evolution is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with b2evolution; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * In addition, as a special exception, the copyright holders give permission to link
 * the code of this program with the PHP/SWF Charts library by maani.us (or with
 * modified versions of this library that use the same license as PHP/SWF Charts library
 * by maani.us), and distribute linked combinations including the two. You must obey the
 * GNU General Public License in all respects for all of the code used other than the
 * PHP/SWF Charts library by maani.us. If you modify this file, you may extend this
 * exception to your version of the file, but you are not obligated to do so. If you do
 * not wish to do so, delete this exception statement from your version.
 * }}
 *
 * {@internal
 * Daniel HAHLER grants Fran�ois PLANQUE the right to license
 * Daniel HAHLER's contributions to this file and the b2evolution project
 * under any OSI approved OSS license (http://www.opensource.org/licenses/).
 * }}
 *
 * @package evocore
 *
 * {@internal Below is a list of authors who have contributed to design/coding of this file: }}
 * @author fplanque: Fran�ois PLANQUE
 * @author blueyed: Daniel HAHLER
 *
 * {@internal Below is a list of former authors whose contributions to this file have been
 *            either removed or redesigned and rewritten anew:
 *            - t3dworld
 *            - tswicegood
 * }}
 *
 * @version $Id$
 */

if( isset( $main_init ) )
{ // Prevent double loading since require_once won't work in all situations
	// on windows when some subfolders have caps :(
	// (Check it out on static page generation)
	return;
}
$main_init = true;


/**
 * Load base + advanced configuration:
 */
require_once( dirname(__FILE__).'/../conf/_config.php' );
if( !$config_is_done )
{ // base config is not done!
	$error_message = 'Base configuration is not done! (see /conf/_config.php)';
}
elseif( !isset( $locales[$default_locale] ) )
{
	$error_message = 'The default locale does not exist! (see /conf/_locales.php)';
}
if( isset( $error_message ) )
{
	require dirname(__FILE__).'/_conf_error.inc.php';	// error & exit
}


/**
 * Check conf...
 */
if( !function_exists( 'gzencode' ) )
{ // when there is no function to gzip, we won't do it
	$use_gzipcompression = false;
}


/**
 * Load logging class
 */
require_once( dirname(__FILE__).'/_log.class.php' );
/**
 * Debug message log for debugging only (initialized here)
 * @global Log $Debuglog
 */
$Debuglog = new Log( 'note' );
/**
 * Info & error message log for end user (initialized here)
 * @global Log $Messages
 */
$Messages = new Log( 'error' );


/**
 * Includes:
 */
require_once( dirname(__FILE__).'/_misc.funcs.php' );

timer_start();

/**
 * Sets various arrays and vars
 */
require_once( dirname(__FILE__).'/_vars.inc.php' );


/**
 * Database connection (connection opened here)
 *
 * @global DB $DB
 */
require_once( dirname(__FILE__).'/_db.class.php' );
$DB = new DB( DB_USER, DB_PASSWORD, DB_NAME, DB_HOST, $db_aliases );

require_once( dirname(__FILE__).'/_results.class.php' );


/**
 * Interface to general settings
 *
 * @global GeneralSettings $Settings
 */
require_once( dirname(__FILE__).'/_generalsettings.class.php' );
$Settings = & new GeneralSettings();

/**
 * Absolute Unix timestamp for server
 * @global int $servertimenow
 */
$servertimenow = time();
/**
 * Corrected Unix timestamp to match server timezone
 * @global int $localtimenow
 */
$localtimenow = $servertimenow + ($Settings->get('time_difference') * 3600);


/**
 * Interface to user settings
 *
 * @global UserSettings $UserSettings
 */
require_once( dirname(__FILE__).'/_usersettings.class.php' );
$UserSettings = & new UserSettings();


/**
 * Includes:
 */
require_once( dirname(__FILE__).'/_template.funcs.php' );    // function to be called from templates
require_once( dirname(__FILE__).'/'.$core_dirout.$lib_subdir.'_xmlrpc.php' );
require_once( dirname(__FILE__).'/'.$core_dirout.$lib_subdir.'_xmlrpcs.php' );
require_once( dirname(__FILE__).'/_blog.class.php' );
require_once( dirname(__FILE__).'/_itemlist.class.php' );
require_once( dirname(__FILE__).'/_itemcache.class.php' );
require_once( dirname(__FILE__).'/_commentlist.class.php' );
require_once( dirname(__FILE__).'/_archivelist.class.php' );

require_once( dirname(__FILE__).'/_dataobjectcache.class.php' );
// Object caches init:
$GroupCache = & new DataObjectCache( 'Group', true, 'T_groups', 'grp_', 'grp_ID' );
$UserCache = & new DataObjectCache( 'User', false, 'T_users', 'user_', 'ID' );
$BlogCache = & new BlogCache();
$ItemCache = & new ItemCache();

require_once( dirname(__FILE__).'/_calendar.class.php' );
require_once( dirname(__FILE__).'/_hitlog.funcs.php' );     // referer logging
require_once( dirname(__FILE__).'/_form.funcs.php' );
require_once dirname(__FILE__).'/'.$core_dirout.$lib_subdir.'_swfcharts.php';

/**
 * Plug-ins init:
 */
require_once( dirname(__FILE__).'/_plugins.class.php' );
$Plugins = & new Plugins();


/**
 * Output buffering?
 */
if( $use_obhandler )
{ // register output buffer handler
	ob_start( 'obhandler' );
}


/**
 * Locale selection:
 */
$Debuglog->add( 'default_locale from conf: '.$default_locale, 'locale' );

locale_overwritefromDB();
$Debuglog->add( 'default_locale from DB: '.$default_locale, 'locale' );

$default_locale = locale_from_httpaccept(); // set default locale by autodetect
$Debuglog->add( 'default_locale from HTTP_ACCEPT: '.$default_locale, 'locale' );

/**
 * Activate default locale:
 */
locale_activate( $default_locale );


/**
 * Login procedure:
 */
if( !isset($login_required) ) $login_required = false;
if( $error = veriflog( $login_required ) )
{ // Login failed:
	require( dirname(__FILE__).'/'.$core_dirout.$htsrv_subdir.'login.php' );
}

// Update the active session for the current user:
$Debuglog->add('Updating the active session for the current user');
online_user_update();


/**
 * User locale selection:
 */
if( is_logged_in() && $current_User->get('locale') != $current_locale )
{ // change locale to users preference
	locale_activate( $current_User->get('locale') );
	if( $current_locale == $current_User->get('locale') )
	{
		$default_locale = $current_locale;
		$Debuglog->add( 'default_locale from user profile: '.$default_locale, 'locale' );
	}
	else
	{
		$Debuglog->add( 'locale from user profile could not be activated: '.$current_User->get('locale'), 'locale' );
	}
}


/**
 * Hit type - determines if hit will be logged and/or increase view count for Items
 *
 * Possible values are:
 * - 'badchar' : referer contains junk or spam : no logging, no counting
 * - 'reload' : page is reloaded : no logging, no counting
 * - 'robot' : page is loaded by a robot: log but don't count view
 * - 'blacklist' (should be 'hidden') : we want to hide the referer, but we count the hit : log & count
 * - 'rss' : RSS feed : log & count
 * - 'invalid' : normal without a referer : log & count
 * - 'search' : referer is a search engine : log & count
 * - 'no' : normal with referer (default) : log & count
 * - 'preview' : preview mode : no logging, no counting
 * - 'already_logged' : this hit has already been logged : no relogging, no recounting
 *
 * @global string $hit_type
 */
$hit_type = filter_hit();


/**
 * Load hacks file if it exists
 */
@include_once( dirname(__FILE__) . '/../conf/hacks.php' );

/*
 * $Log$
 * Revision 1.4  2004/10/17 20:18:37  fplanque
 * minor changes
 *
 * Revision 1.3  2004/10/16 01:31:22  blueyed
 * documentation changes
 *
 * Revision 1.2  2004/10/14 18:31:25  blueyed
 * granting copyright
 *
 * Revision 1.1  2004/10/13 22:46:32  fplanque
 * renamed [b2]evocore/*
 *
 * Revision 1.73  2004/10/12 18:48:34  fplanque
 * Edited code documentation.
 *
 */
?>