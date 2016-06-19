<?php


/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * Dashboard. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://jgoodesign.ca
 * @since             1.0.0
 * @package           JGood_Archives
 *
 * @wordpress-plugin
 * Plugin Name:       JGood Archives
 * Plugin URI:        http://jgoodesign.ca
 * Description:       Fully customizable archive pages.
 * Version:           1.0.7
 * Author:            Jordan Good
 * Author URI:        mailto: jgoodesign@gmail.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       jgood-archives
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-jgood-archives-activator.php
 */
function activate_jgood_archives() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-jgood-archives-activator.php';
	JGood_Archives_Activator::activate();
	//manually register post type, create rewrite rules and then flush rewrite rules
	jgood_archives_init_post_types();
	jgood_archives_rewrite_rules();
	flush_rewrite_rules();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-jgood-archives-deactivator.php
 */
function deactivate_jgood_archives() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-jgood-archives-deactivator.php';
	JGood_Archives_Deactivator::deactivate();
	//flush rewrite rules
	flush_rewrite_rules();
}

register_activation_hook( __FILE__, 'activate_jgood_archives' );
register_deactivation_hook( __FILE__, 'deactivate_jgood_archives' );

// add post type register to init action
add_action('init', 'jgood_archives_init_post_types');

/**
 * Register custom post type
 *
 * @since    1.0.0
 */
function jgood_archives_init_post_types() {
	$labels = array(
		'name'               => _x( 'JGood Archives', 'project type general name', 'JGood Archives' ),
		'singular_name'      => _x( 'Archive', 'project type singular name', 'JGood Archives' ),
		'add_new'            => _x( 'Add New', 'project item', 'JGood Archives' ),
		'add_new_item'       => __( 'Add New Archive', 'JGood Archives' ),
		'edit_item'          => __( 'Edit Archive', 'JGood Archives' ),
		'new_item'           => __( 'New Archive', 'JGood Archives' ),
		'all_items'          => __( 'All Archives', 'JGood Archives' ),
		'view_item'          => __( 'View Archive', 'JGood Archives' ),
		'search_items'       => __( 'Search Archives', 'JGood Archives' ),
		'not_found'          => __( 'Nothing found', 'JGood Archives' ),
		'not_found_in_trash' => __( 'Nothing found in Trash', 'JGood Archives' ),
		'parent_item_colon'  => '',
	);

	$args = array(
		'labels'             => $labels,
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'can_export'         => true,
		'show_in_nav_menus'  => true,
		'query_var'          => true,
		'has_archive'        => false,
		'rewrite'            => apply_filters( 'jgood_archives_posttype_rewrite_args', array(
			'feeds'      => true,
			'slug'       => '',
			'with_front' => false,
		) ),
		'capability_type'    => 'page',
		'hierarchical'       => true,
		'menu_position'      => null,
		'menu_icon'          => 'dashicons-networking',
		'supports'           => array( 'title', 'revisions'/*, 'page-attributes'*/ ),
	);

	register_post_type( 'jgood_archives', apply_filters( 'archive_posttype_args', $args ) );
}

// add rewrite rules to init 
add_action( 'init', 'jgood_archives_rewrite_rules' );

/**
 * Creates rewrite rules
 *
 * @since    1.0.0
 */
function jgood_archives_rewrite_rules() {

	// assign rewrite rule for custom archive pagination
	add_rewrite_rule(
		'^jgood_custom_archives/(.*)/page/(\d)+',
		'index.php?post_type=jgood_archives&jgood_url=true&jgood_fields=$matches[1]&paged=$matches[2]',
		'top'
	);

	// assign rewrite rule for basic custom archive
	add_rewrite_rule(
		'^jgood_custom_archives/(.*)',
		'index.php?post_type=jgood_archives&jgood_url=true&jgood_fields=$matches[1]',
		'top'
	);
	
	// assign url paremeters to query vars for later use
	add_rewrite_tag('%jgood_url%', '([^&]+)');
	add_rewrite_tag('%jgood_fields%', '([^&]+)');
}

/**
 * The core plugin class that is used to define internationalization,
 * dashboard-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-jgood-archives.php';


// include array_column function if not php 5.5
include_once plugin_dir_path( __FILE__ ) . 'includes/array-column-lib.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_jgood_archives() {

	$plugin = new JGood_Archives();
	$plugin->run();

	// include updater class
	require_once( plugin_dir_path( __FILE__ ) . 'includes/class-jgood-archives-updater.php' );
	if ( is_admin() ) {
		// if admin, start updater class
		// __FILE__,
		// github user name
		// github repo name
		// optional private github access token
	    new JGood_Archives_Plugin_Updater( __FILE__, 'jgoodesign', 'Archives' );
	}

}
run_jgood_archives();
