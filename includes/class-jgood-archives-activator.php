<?php

/**
 * Fired during plugin activation
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    JGood_Archives
 * @subpackage JGood_Archives/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    JGood_Archives
 * @subpackage JGood_Archives/includes
 * @author     Your Name <email@example.com>
 */
class JGood_Archives_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		global $wpdb;

		// set up db for plugin
		$charset_collate = $wpdb->get_charset_collate();
		
		$jgood_archives_options = $wpdb->prefix . "archives_options";

		$sql = "CREATE TABLE $jgood_archives_options (
		  option_id mediumint(9) NOT NULL AUTO_INCREMENT,
		  option_category mediumint(9) DEFAULT 0 NOT NULL,
		  object_id mediumint(9) DEFAULT 0 NOT NULL,
		  name varchar(55) DEFAULT '' NOT NULL,
		  label varchar(55) DEFAULT '' NOT NULL,
		  description varchar(1024) DEFAULT '' NOT NULL,
		  value longtext DEFAULT '' NOT NULL,
		  UNIQUE KEY id (option_id)
		) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta($sql);

		// set up initial options
		$initial_options = array(
			array("name"=>"post-type", "label"=>"Post-Type", "value"=>1),
			array("name"=>"category", "label"=>"Category", "value"=>1),
			array("name"=>"tag", "label"=>"Tag", "value"=>1),
			array("name"=>"year", "label"=>"Year", "value"=>1),
			array("name"=>"month", "label"=>"Month", "value"=>1),
			array("name"=>"day", "label"=>"Day", "value"=>1),
			array("name"=>"author", "label"=>"Author", "value"=>1)
		);

		foreach ($initial_options as $id => $option) {
			$name = $option['name'];
			$results = $wpdb->get_results("SELECT value FROM $jgood_archives_options WHERE name = '$name' AND option_category = 0 AND object_id = 0", ARRAY_A);
			if(empty($results)){
				$wpdb->insert($jgood_archives_options, $option);
			}
		}
	}

}
