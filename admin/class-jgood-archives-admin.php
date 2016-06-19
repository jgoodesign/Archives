<?php



/**
 * The dashboard-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    JGood_Archives
 * @subpackage JGood_Archives/admin
 */

/**
 * The dashboard-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the dashboard-specific stylesheet and JavaScript.
 *
 * @package    JGood_Archives
 * @subpackage JGood_Archives/admin
 * @author     Your Name <email@example.com>
 */
class JGood_Archives_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $jgood_archives    The ID of this plugin.
	 */
	private $jgood_archives;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @var      string    $jgood_archives       The name of this plugin.
	 * @var      string    $version    The version of this plugin.
	 */
	public function __construct( $jgood_archives, $version ) {

		$this->jgood_archives = $jgood_archives;
		$this->version = $version;

		// include admin display file to access html functions
		require_once plugin_dir_path( __FILE__ ) . 'partials/jgood-archives-admin-display.php';
	}

	/**
	 * Register the stylesheets for the Dashboard.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in JGood_Archives_Admin_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The JGood_Archives_Admin_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->jgood_archives, plugin_dir_url( __FILE__ ) . 'css/jgood-archives-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the dashboard.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in JGood_Archives_Admin_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The JGood_Archives_Admin_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->jgood_archives, plugin_dir_url( __FILE__ ) . 'js/jgood-archives-admin.js', array( 'jquery' ), $this->version, false );

	}

	// add settings option to jgood archives admin menu
	public function jgood_archives_menu() {
		add_submenu_page( "edit.php?post_type=jgood_archives", "Settings", "Settings", "manage_options", "jgood-archives", "display_archive_menu_settings");
	}

	// remove default parent hierarchy for custom archive post
	public function jgood_archives_post_parent_menu(){
		remove_meta_box('pageparentdiv', 'jgood_archives', 'normal');
	}

	// add url to all archives admin column
	public function jgood_archives_admin_list_columns( $defaults ) {
		$newDetails = array();

		foreach($defaults as $key=>$value) {
			$newDetails[$key] = $value;
			if($key=="title"){
				$newDetails['url'] = 'Link';
			}
		}

		return $newDetails;
	}

	// insert url value into url column
	public function jgood_archives_admin_list( $column_name, $post_ID ) {
		if ($column_name == 'url') {
	        echo "<a href='".get_permalink( $post_ID )."'>".get_permalink( $post_ID )."</a>";
	    }
	}

	// save archive admin settings
	public function jgood_archives_process_settings()
	{
		if ( !current_user_can( 'manage_options' ) )
		{
			wp_die( 'You are not allowed to be on this page.' );
		}
		// Check that nonce field
		check_admin_referer( 'jgood_archives_settings_verify' );

		global $wpdb;
		$jgood_archives_options = $wpdb->prefix . 'archives_options';

		// grab all default "archive by..." settings
		$options_groupA = $wpdb->get_results("SELECT option_id, name, value FROM $jgood_archives_options WHERE option_category = 0 AND object_id = 0");

		// loop through default options
		foreach ($options_groupA as $num => $details) {
			$name = "jgood_archives_".$details->name;

			// set option value
			$option_value = 0;
			if(isset($_POST[$name])){ $option_value = $_POST[$name]; }

			// if option has been changed update the db
			if( $option_value != $details->value){
				$wpdb->update( 
					$jgood_archives_options, 
					array("value" => $option_value), 
					array("option_id" => $details->option_id) 
				);
			}
		}

		$options_groupB = $wpdb->get_results("SELECT option_id FROM $jgood_archives_options WHERE option_category = 1 AND object_id = 0");
		
		foreach ($options_groupB as $num => $rule) {
			$wpdb->delete( 
				$jgood_archives_options, 
				array ( "option_id" => $rule->option_id )
			);
		}

		foreach ($_POST['jgood_archives_rule_text'] as $id => $value) {
			$name = "jgood_archives_rule_".$id;
			$data = array("filters"=>$value, "sidebar"=>$_POST['jgood_archives_rule_sidebar'][$id]);
			
			if(!is_serialized($data)){ $data = serialize($data); }

			if($value != "" && $_POST['jgood_archives_rule_sidebar'][$id] != ""){
				$wpdb->insert(
					$jgood_archives_options, 
					array( "option_category" => 1, "object_id" => 0, "name"=>$name, "label"=>"Sidebar Rule", "value"=>$data)
				);
			}
		}

		// send back to settings back with success message
		wp_redirect(  admin_url( 'edit.php?post_type=jgood_archives&page=jgood-archives&alert=1' ) );
		exit;
	}

	// add archive options to custom post pages
	function jgood_archives_add_metaboxes() {
		add_meta_box('jgood_archives_options_meta', 'Archive By...', 'display_jgood_archives_hierarchy', 'jgood_archives', 'normal', 'high');

		// add new parent option to archive page
		//add_meta_box('jgood_archives_parent', 'Parent', 'jgood_archives_attributes_meta_box', 'jgood_archives', 'side', 'default');
	}

	// save archive options for each post to custom db table
	function jgood_archives_save_archive($post_id, $post, $update) {
		// verify this came from the our screen and with proper authorization,
		if(empty($_POST)){
			return;
		}

		// handle the case when the custom post is quick edited
		// otherwise all custom meta fields are cleared out
		if(isset($_POST['_inline_edit']) || !isset($_POST['eventmeta_nonce'])){
			return;
		}

		if ( !wp_verify_nonce( $_POST['eventmeta_nonce'], "edit_archive_meta" )) {
			return $post->ID;
		}

		// Is the user allowed to edit the post or page?
		if ( !current_user_can( 'edit_post', $post->ID ))
			return $post->ID;

		// Is current post an archive post
		if ( $post->post_type != 'jgood_archives')
			return $post->ID;

		global $wpdb;
		$jgood_archives_options = $wpdb->prefix . 'archives_options';

		// grab default admin options
		$default_options = $wpdb->get_results("SELECT label, name FROM $jgood_archives_options WHERE option_category = 0 AND object_id = 0 AND value = 1");
		
		// grab post options if already set
		$post_options = $wpdb->get_results("SELECT option_id FROM $jgood_archives_options WHERE option_category = 0 AND object_id = $post->ID");

		foreach($post_options as $old){
			$wpdb->delete( 
				$jgood_archives_options, 
				array ( "option_id" => $old->option_id )
			);
		}

		// loop through default post options
		foreach ($default_options as $id => $details) {
			
			$name = "jgood_archives_".$details->name;

			if(isset($_POST[$name])){
				foreach($_POST[$name] as $id=>$value){
					if($value != ""){
						$option_name = $name."_".$id;
						$wpdb->insert(
							$jgood_archives_options, 
							array( "option_category" => 0, "object_id" => $post->ID, "name"=>$option_name, "label"=>$details->label, "value"=>$value)
						);
					}
				}
			}
		}
	}
}
