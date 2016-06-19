<?php

/**
 * Fired during plugin deactivation
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    JGood_Archives
 * @subpackage JGood_Archives/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    JGood_Archives
 * @subpackage JGood_Archives/includes
 * @author     Your Name <email@example.com>
 */
class JGood_Archives_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		//JGood_Archives_Deactivator::destroy_tables();
	}

	// remove custom db table on deactivation
	public function destroy_tables(){
		global $wpdb;

		$jgood_archives_options = $wpdb->prefix . "archives_options";

		$sql = "DROP TABLE ". $jgood_archives_options."; ";
		$wpdb->query($sql);
	}

}
