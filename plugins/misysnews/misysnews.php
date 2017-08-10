<?php
/**
 * Plugin Name: MisysNews
 * Plugin URI: http://www.misys.com
 * Description: MisysNews integration for Wordpress
 * Version: 0.0.1
 * Author: Nicolas Demange
 * License: GPL2
 */


define( 'MISYSNEWS__PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'MISYSNEWS__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'MISYSNEWS__APP_URL', MISYSNEWS__PLUGIN_URL . 'app/' );
define( 'MISYSNEWS__APP_DIR', MISYSNEWS__PLUGIN_DIR . 'app/' );

include MISYSNEWS__PLUGIN_DIR . 'admin.php';
//include MISYSNEWS__PLUGIN_DIR. 'adminnew.php';
include MISYSNEWS__PLUGIN_DIR . 'custom_rss.php';
include MISYSNEWS__PLUGIN_DIR . 'custom_url.php';
//include MISYSNEWS__PLUGIN_DIR . 'database.php';
require_once ( 'databaseoop.php');
require_once ('deviceoop.php');
require_once ('tool.php');

register_activation_hook( __FILE__, 'setupPlugin' );
function setupPlugin(){
	$db = new misysnews_dbconnection();
	$db->initTables();
	$db->releaseConnection();
	//set up the options
	// the option of the serial number is runnable_serial_number
	$version = "1.0.1";
	$initial_serial_number = 1;
	if(!get_option("misys_news_version")){
		add_option("misys_news_version",$version);
	}else {
		update_option("misys_news_version",$version);
	}
	if(!get_option("misysnews_runnable_serial_number")){
		add_option("misysnews_runnable_serial_number",$initial_serial_number);
	}else {
		update_option("misysnews_runnable_serial_number",$initial_serial_number);
	}

	// contains the
	if(!get_option('misysnews_feeds_new')){
    add_option('misysnews_feeds_new');
    update_option('misysnews_feeds_new',"");
  }else {
    update_option('misysnews_feeds_new',"");
  }
	//used to notice the number of down devices
	if(!get_option("misysnews_number_of_down_device")){
		add_option("misysnews_number_of_down_device");
		update_option("misysnews_number_of_down_device",0);
	}else{
		update_option("misysnews_number_of_down_device",0);
	}
}

// create the daily event to check the device status
register_activation_hook(__FILE__,"checkDeviceDaily");
function checkDeviceDaily(){
	if(!wp_next_scheduled("misysnews_check_device_daily")){
		wp_schedule_event(time(),'daily',"misysnews_check_device_daily");
	}
}
add_action("misysnews_check_device_daily","checkTheWholeDevices");
function checkTheWholeDevices(){
	$deviceMgt = new misysnews_deviceManagement();
	$registeredDevices = $deviceMgt->listRegisteredDevice();
	$numberOfDownDevices = 0;
	for($i = 0; $i < count($registeredDevices);$i++){
		if($registeredDevices[$i]->isDeviceDown()){
			$numberOfDownDevices++;
		}
	}
	if($numberOfDownDevices > 0){
		//send the email to user
		$misysnews_mailObj = new misysnews_mail();
		$misysnews_mailObj->sendEmailWithDownDevices();
	}
}
// hook for deactive
register_deactivation_hook(__FILE__,"deactive_device_daily_check");
function deactive_device_daily_check(){
	wp_clear_scheduled_hook("misysnews_check_device_daily");
}


//sregister_deactivation_hook( __FILE__, 'removeTablesFromeDatabase');
register_uninstall_hook(__FILE__, 'removeTablesFromeDatabase');
function removeTablesFromeDatabase(){
	$db = new misysnews_dbconnection();
	$db->removeTables();
	$db->releaseConnection();
}

add_action( 'wp_enqueue_scripts', 'register_misysnews_styles_and_scripts' );
function register_misysnews_styles_and_scripts() {
	wp_register_style( 'misysnews_lib_fullpage', plugins_url( 'misysnews/app/bower_components/fullpage.js/jquery.fullPage.css' ) );
	wp_register_style( 'misysnews_lib_weathericons', plugins_url( 'misysnews/app/bower_components/weather-icons/css/weather-icons.min.css' ) );
	wp_register_style( 'misysnews_main', plugins_url( 'misysnews/app/styles/main.css' ) );
	wp_register_style( 'misysnews_header', plugins_url( 'misysnews/app/styles/header.css' ) );
	wp_register_style( 'misysnews_news', plugins_url( 'misysnews/app/styles/news.css' ) );
	wp_register_style( 'misysnews_styles', plugins_url( 'misysnews/app/styles/bar.css' ), array(
		'misysnews_lib_fullpage',
		'misysnews_lib_weathericons',
		'misysnews_main',
		'misysnews_header',
		'misysnews_news') );

	wp_register_script( 'misysnews_lib_jslimscroll', plugins_url( 'misysnews/app/bower_components/jquery.slimscroll/jquery.slimscroll.min.js' ), array('jquery'), null, true );
	wp_register_script( 'misysnews_lib_jfullpage', plugins_url( 'misysnews/app/bower_components/fullpage.js/jquery.fullPage.js' ), array('jquery'), null, true );
	wp_register_script( 'misysnews_lib_angular', plugins_url( 'misysnews/app/bower_components/angular/angular.js' ), null, null, true );
	wp_register_script( 'misysnews_lib_angular-sanitize', plugins_url( 'misysnews/app/bower_components/angular-sanitize/angular-sanitize.js' ), null, null, true );
	wp_register_script( 'misysnews_lib_angular-fullscreen', plugins_url( 'misysnews/app/bower_components/angular-fullscreen/src/angular-fullscreen.js' ), null, null, true );
	wp_register_script( 'misysnews_lib_jscrollbox', plugins_url( 'misysnews/app/libs/jquery.scrollbox.js' ), array('jquery'), null, true );
	wp_register_script( 'misysnews_lib_prefixfree', plugins_url( 'misysnews/app/libs/prefixfree.min.js' ), array('jquery'), null, true );
	wp_register_script( 'misysnews_lib_xml2json', plugins_url( 'misysnews/app/libs/xml2json.min.js' ), null, null, true );
	wp_register_script( 'misysnews_main', plugins_url( 'misysnews/app/scripts/main.js' ), null, null, true );
	wp_register_script( 'misysnews_newscontroller', plugins_url( 'misysnews/app/scripts/news-controller.js' ), null, null, true );
	wp_register_script( 'misysnews_barcontroller', plugins_url( 'misysnews/app/scripts/bar-controller.js' ), null, null, true );
	wp_register_script( 'misysnews_feedservice', plugins_url( 'misysnews/app/scripts/feed-service.js' ), null, null, true );

	wp_register_script( 'misysnews_scripts', plugins_url( 'misysnews/app/scripts/filters.js' ), array(
		'misysnews_lib_jslimscroll',
		'misysnews_lib_jfullpage',
		'misysnews_lib_angular',
		'misysnews_lib_angular-sanitize',
		'misysnews_lib_angular-fullscreen',
		'misysnews_lib_jscrollbox',
		'misysnews_lib_xml2json',
		'misysnews_main',
		'misysnews_newscontroller',
		'misysnews_barcontroller',
		'misysnews_feedservice'), null, true );

	if( is_page('misysnews') ) {
		wp_enqueue_style( 'misysnews_styles' );
		wp_enqueue_script( 'misysnews_scripts' );
	}
}

?>
