<?php

use TestingPlugin\TestingPluginPlugin;
use TestingPlugin\FileManager;

/**
 * Testing plugin plugin
 *
 *
 * @link              http://premmerce.com
 * @since             1.0.0
 * @package           TestingPlugin
 *
 * @wordpress-plugin
 * Plugin Name:       Testing plugin
 * Plugin URI:        http://premmerce.com
 * Description:       About
 * Version:           1.0
 * Author:            admin
 * Author URI:        http://premmerce.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       premmerce-testing-plugin
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

call_user_func( function () {

	require_once plugin_dir_path( __FILE__ ) . 'autoload.php';

	$main = new TestingPluginPlugin( new FileManager( __FILE__ ) );

	register_activation_hook( __FILE__, [ $main, 'activate' ] );

	register_deactivation_hook( __FILE__, [ $main, 'deactivate' ] );

	register_uninstall_hook( __FILE__, [ TestingPluginPlugin::class, 'uninstall' ] );

	$main->run();
} );