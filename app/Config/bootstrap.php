<?php
/**
 * This file is loaded automatically by the app/webroot/index.php file after core.php
 *
 * This file should load/create any application wide configuration settings, such as
 * Caching, Logging, loading additional configuration files.
 *
 * You should also use this file to include any files that provide global functions/constants
 * that your application uses.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Config
 * @since         CakePHP(tm) v 0.10.8.2117
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

// Setup a 'default' cache configuration for use in the application.
Cache::config('default', array('engine' => 'File'));

/**
 * The settings below can be used to set additional paths to models, views and controllers.
 *
 * App::build(array(
 *     'Model'                     => array('/path/to/models/', '/next/path/to/models/'),
 *     'Model/Behavior'            => array('/path/to/behaviors/', '/next/path/to/behaviors/'),
 *     'Model/Datasource'          => array('/path/to/datasources/', '/next/path/to/datasources/'),
 *     'Model/Datasource/Database' => array('/path/to/databases/', '/next/path/to/database/'),
 *     'Model/Datasource/Session'  => array('/path/to/sessions/', '/next/path/to/sessions/'),
 *     'Controller'                => array('/path/to/controllers/', '/next/path/to/controllers/'),
 *     'Controller/Component'      => array('/path/to/components/', '/next/path/to/components/'),
 *     'Controller/Component/Auth' => array('/path/to/auths/', '/next/path/to/auths/'),
 *     'Controller/Component/Acl'  => array('/path/to/acls/', '/next/path/to/acls/'),
 *     'View'                      => array('/path/to/views/', '/next/path/to/views/'),
 *     'View/Helper'               => array('/path/to/helpers/', '/next/path/to/helpers/'),
 *     'Console'                   => array('/path/to/consoles/', '/next/path/to/consoles/'),
 *     'Console/Command'           => array('/path/to/commands/', '/next/path/to/commands/'),
 *     'Console/Command/Task'      => array('/path/to/tasks/', '/next/path/to/tasks/'),
 *     'Lib'                       => array('/path/to/libs/', '/next/path/to/libs/'),
 *     'Locale'                    => array('/path/to/locales/', '/next/path/to/locales/'),
 *     'Vendor'                    => array('/path/to/vendors/', '/next/path/to/vendors/'),
 *     'Plugin'                    => array('/path/to/plugins/', '/next/path/to/plugins/'),
 * ));
 *
 */

/**
 * Custom Inflector rules can be set to correctly pluralize or singularize table, model, controller names or whatever other
 * string is passed to the inflection functions
 *
 * Inflector::rules('singular', array('rules' => array(), 'irregular' => array(), 'uninflected' => array()));
 * Inflector::rules('plural', array('rules' => array(), 'irregular' => array(), 'uninflected' => array()));
 *
 */

/**
 * Plugins need to be loaded manually, you can either load them one by one or all of them in a single call
 * Uncomment one of the lines below, as you need. Make sure you read the documentation on CakePlugin to use more
 * advanced ways of loading plugins
 *
 * CakePlugin::loadAll(); // Loads all plugins at once
 * CakePlugin::load('DebugKit'); //Loads a single plugin named DebugKit
 *
 */
//CakePlugin::loadAll();
/**
 * You can attach event listeners to the request lifecycle as Dispatcher Filter. By default CakePHP bundles two filters:
 *
 * - AssetDispatcher filter will serve your asset files (css, images, js, etc) from your themes and plugins
 * - CacheDispatcher filter will read the Cache.check configure variable and try to serve cached content generated from controllers
 *
 * Feel free to remove or add filters as you see fit for your application. A few examples:
 *
 * Configure::write('Dispatcher.filters', array(
 *		'MyCacheFilter', //  will use MyCacheFilter class from the Routing/Filter package in your app.
 *		'MyPlugin.MyFilter', // will use MyFilter class from the Routing/Filter package in MyPlugin plugin.
 * 		array('callable' => $aFunction, 'on' => 'before', 'priority' => 9), // A valid PHP callback type to be called on beforeDispatch
 *		array('callable' => $anotherMethod, 'on' => 'after'), // A valid PHP callback type to be called on afterDispatch
 *
 * ));
 */
Configure::write('Dispatcher.filters', array(
	'AssetDispatcher'
));

Configure::load('company');
Configure::load('marketing');
Configure::load('permission');

Configure::write('App.default_meta_title_prefix', __('Fyooz CRM | '));
Configure::write('App.default_meta_title_surfix', __(' - www.fyooz.com'));
Configure::write('App.default_meta_keywords', __('Fyooz, CRM'));
Configure::write('App.default_meta_description', __('Fyooz CRM - Developed by www.onlinebizsoft.com'));

/**
 * Configures default file logging options
 */
App::uses('CakeLog', 'Log');
CakeLog::config('debug', array(
	'engine' => 'File',
	'types' => array('notice', 'info', 'debug'),
	'file' => 'debug',
));
CakeLog::config('error', array(
	'engine' => 'File',
	'types' => array('warning', 'error', 'critical', 'alert', 'emergency'),
	'file' => 'error',
));

CakePlugin::loadAll(
	array( 
		'Permissionable' => array('bootstrap' => true),
		'DebugKit',
		'Client'  => array('bootstrap' => true),
		'Company' => array('bootstrap' => true),
		'Marketing' => array('bootstrap' => true),
		'Accounting' => array('bootstrap' => true),
		'Project' => array('bootstrap' => true)
	)
);

define('PAGE_NAME', 'Fyooz CRM'); 
define('UPLOAD_PATH', WWW_ROOT . 'uploads');
define('UPLOAD_TEMP', UPLOAD_PATH . DS . 'tmp');
define('ROWPERPAGE', 10);
define('FULL_PATH_DOMAIN', 'http://crm.demoserver.sg/');

function formatDate($date){
	if(empty($date))
		return;
	$timezone = Configure::read('Config.timezone');
	$timezone = empty($timezone) ? 'Asia/Singapore' : Configure::read('Config.timezone');
	$format = Configure::read('Settings.Formats.date_format');
	$date = new DateTime($date, new DateTimeZone('UTC')); 
	$date->setTimezone(new DateTimeZone($timezone)); 
	return $date->format($format);

	// $format = Configure::read('Settings.Formats.date_format');
	// return date($format,strtotime($date));
}
function formatDateTime($datetime) {
	$timezone = Configure::read('Config.timezone');
	$timezone = empty($timezone) ? 'Asia/Singapore' : Configure::read('Config.timezone');
	$format = Configure::read('Settings.Formats.date_format');
	$format .= ' H:i:s';
	$date = new DateTime($datetime, new DateTimeZone('UTC')); 
	$date->setTimezone(new DateTimeZone($timezone)); 
	return $date->format($format);
}

function sqlFormatDate($date_str){
	$format = Configure::read('Settings.Formats.date_format');
	$date = date_create_from_format($format, $date_str);
	return gmdate('Y-m-d', strtotime($date->format('Y-m-d')));
}
function sqlFormatDateTime($date_str){
	$timezone = Configure::read('Config.timezone');
	$timezone = empty($timezone) ? 'Asia/Singapore' : Configure::read('Config.timezone');
	$current_timezone = new DateTimeZone($timezone);
	$utc_timezone = new DateTimeZone('UTC');
	$dateTimeUTC = new DateTime("now", $utc_timezone);
	$timeOffset = $current_timezone->getOffset($dateTimeUTC);
	$format = Configure::read('Settings.Formats.date_format'). ' H:i:s';
	$date = date_create_from_format($format, $date_str);
	return gmdate('Y-m-d H:i:s', strtotime($date->format('Y-m-d H:i:s')) - $timeOffset );
}
function phpToJsDateFormat($php_format)
{
    $SYMBOLS_MATCHING = array(
        // Day
        'd' => 'DD',
        'D' => 'DDD',
        'j' => 'D',
        'l' => 'DDDD',
        'N' => '',
        'S' => '',
        'w' => '',
        'z' => 'O',
        // Week
        'W' => '',
        // Month
        'F' => 'MMMM',
        'm' => 'MM',
        'M' => 'MMM',
        'n' => 'M',
        't' => '',
        // Year
        'L' => '',
        'o' => '',
        'Y' => 'YYYY',
        'y' => 'YY',
        // Time
        'a' => '',
        'A' => '',
        'B' => '',
        'g' => '',
        'G' => '',
        'h' => '',
        'H' => '',
        'i' => '',
        's' => '',
        'u' => ''
    );
    $jqueryui_format = "";
    $escaping = false;
    for($i = 0; $i < strlen($php_format); $i++)
    {
        $char = $php_format[$i];
        if($char === '\\') // PHP date format escaping character
        {
            $i++;
            if($escaping) $jqueryui_format .= $php_format[$i];
            else $jqueryui_format .= '\'' . $php_format[$i];
            $escaping = true;
        }
        else
        {
            if($escaping) { $jqueryui_format .= "'"; $escaping = false; }
            if(isset($SYMBOLS_MATCHING[$char]))
                $jqueryui_format .= $SYMBOLS_MATCHING[$char];
            else
                $jqueryui_format .= $char;
        }
    }
    return $jqueryui_format;
}
function getPassword(){
	return substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789') , 0 , 10 );
}
function tz_list(){
	$zones_array = array();
	$timestamp = time();
	$zones_array['Asia/Singapore'] = 'UTC/GMT +08:00 - Asia/Singapore';
	foreach(timezone_identifiers_list() as $key => $zone){
		date_default_timezone_set($zone);
		//$zones_array[$key]['zone'] = $zone;
		if($zone != 'Asia/Singapore'){
			if($zone == 'Asia/Ho_Chi_Minh'){
				$zones_array[$zone] = 'UTC/GMT ' . date('P', $timestamp) . ' - Asia/Ha Noi';
			}else{
				$zones_array[$zone] = 'UTC/GMT ' . date('P', $timestamp) . ' - '. $zone;
			}
		}
	}
	return $zones_array;
}

$loginAction = array(
	'plugin' => null,
	'controller' => 'users',
	'action' => 'login'
);

$loginRedirect = array(
	'plugin' => null,
	'controller' => 'users',
	'action' => 'dashboard'
);

$logoutRedirect = array(
	'plugin' => null,
	'controller' => 'users',
	'action' => 'login'
);


$allow_action = array(
	array('c' => 'users','a' => array('logout','login','profiles','captcha','changepass')) 
);

$crud_mapping = array(
	array('c' => 'users','a' => array('view' => array('index','dashboard'), 'edit' => array('resetPassword', 'resetSignature', 'block'))),
	array('c' => 'countries','a' => array('view' => array('index'))),
	array('c' => 'currencies','a' => array('view' => array('index'))),
	array('c' => 'groups','a' => array('view' => array('index'))),
	array('c' => 'settings','a' => array('edit' => array('formats','accounting','company'))),
	array('c' => 'owneraccounts','a' => array('edit' => array('index'))),
	array('c' => 'Histories', 'a' => array('view' => array('index'), 'edit' => array( 'edit')))
);

Configure::write('AllowAction.Core', $allow_action);
Configure::write('CrudMapping.Core', $crud_mapping);
Configure::write('Core.LoginAction', $loginAction);
Configure::write('Core.LoginRedirect', $loginRedirect);
Configure::write('Core.LogoutRedirect', $logoutRedirect);

//define function debug
function prd($var)
{
	pr($var);
	exit;
}
