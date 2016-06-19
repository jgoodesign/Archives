<?php


/**
 * Provide a dashboard view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    JGood_Archives
 * @subpackage JGood_Archives/admin/partials
 */

// html to display for archives settings page
function display_archive_menu_settings(){
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}

	global $wpdb;
	$jgood_archives_options = $wpdb->prefix . 'archives_options';

	echo '<div class="wrap">';
	echo '<h2>Default Settings</h2>';

	// display success message if changes have been saved
	if(isset( $_GET['alert'] ) && $_GET['alert'] == '1'){
		echo '<div class="updated fade"><p><strong>JGood Archive settings have been successfully updated.</strong></p></div>';
	}

	echo '<form id="archive-options" method="post" action="admin-post.php">
			<input type="hidden" name="action" value="jgood_archives_save_settings" />';
	wp_nonce_field( 'jgood_archives_settings_verify' );

	// display 'archive by...'  options
	// grab initial archive options from archive db table
	$options_groupA = $wpdb->get_results("SELECT label, name, value FROM $jgood_archives_options WHERE option_category = 0 AND object_id = 0");

	echo '<table class="form-table">
				<tr class="jgood-sub-heading"><th colspan="2">Archive By...</th></tr>';

	// display each option
	foreach ($options_groupA as $num => $details) {
		echo'<tr>
					<th scope="row">'.$details->label.':</th>
					<td>
						<div class="jgood-options">
							<input type="checkbox" name="jgood_archives_'.$details->name.'" id="jgood_archives_'.$details->name.'" class="jgood-options-switch" value="1"';
		if($details->value){ echo' checked'; }
						echo'>
							<label for="jgood_archives_'.$details->name.'" class="jgood-options-switch-label"><span class="jgood-options-switch-inner"></span><span class="jgood-options-switch-switch"></span></label>
						</div>
					</td>
				</tr>';
	}

	echo '</table>';
	submit_button();

	// display default widget style options
	echo '<table class="form-table">
				<tr class="jgood-sub-heading"><th colspan="2">Widget and Custom Archive Sidebar Rules</th></tr>';

	global $wp_registered_sidebars;

	$options_groupB = $wpdb->get_results("SELECT value FROM $jgood_archives_options WHERE option_category = 1 AND object_id = 0");

	if(empty($options_groupB)){
		display_rules(0, $wp_registered_sidebars);
	}else{
		foreach($options_groupB as $num=>$rule){
			display_rules($num, $wp_registered_sidebars, unserialize($rule->value));
		}
	}
				
	echo '</table>';
	submit_button();

	/* display default widget style options
	echo '<table class="form-table">
				<tr class="jgood-sub-heading"><th colspan="2">Style Options</th></tr>';
	echo '</table>';
	submit_button();*/

	echo '</form></div>';
}

// html to display archive options for custom post type
function display_jgood_archives_hierarchy(){
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}

	global $wpdb;
	$jgood_archives_options = $wpdb->prefix . 'archives_options';

	// get current post id
	global $post;
	$current_id = $post->ID;	

	// grab general admin options from db table if option is turned on
	$default_options = $wpdb->get_results("SELECT label, name, value FROM $jgood_archives_options WHERE option_category = 0 AND object_id = 0 AND value = 1");
	
	// grab any existing options from current post
	$post_options = $wpdb->get_results("SELECT label, name, value FROM $jgood_archives_options WHERE option_category = 0 AND object_id = $current_id");

	// html to display archive post options
	echo '<div class="wrap">';
	echo '<input type="hidden" name="eventmeta_nonce" id="eventmeta_nonce" value="' . 
	wp_create_nonce( 'edit_archive_meta' ) . '" />';

	echo '<table class="form-table">';

	// loop through general admin options
	foreach ($default_options as $id => $details) {
		
		$current_options = array();
		foreach ($post_options as $num => $content) {
			if($content->label == $details->label){
				$current_options[] = $content->value;
			}
		}

		// display option html
		display_option($details->name, $details->label, $current_options);
	}

	echo '</table>';
	echo '</div>';
}

// display option group
function display_option($option, $option_label, $current_options){
	echo'<tr>
			<div class="jgood_archives_option_group" id="'.$option.'">
				<div class="jgood_archives_option_group_title"><label>'.$option_label.'</label></div>
		';

	if(empty($current_options)){ 
		create_input(0, $option, "", $option_label); 
	}else{
		foreach ($current_options as $option_ID => $option_value) {
			create_input($option_ID, $option, $option_value, $option_label);
		}
	}
	
	echo'<div class="jgood_archives_option_add button button-primary jgood_archives_add_button">Add Filter</div>
			</div>
		</tr>';
}


// create option select to be displayed
function create_input($num, $name, $value, $label){
	switch ($label) {
		case 'Post-Type':
			$select_values = get_post_types( '', 'names' );
			break;
		case 'Category':
			$args = array(
				'orderby' => 'name',
				'order' => 'ASC'
			);
			$select_values_object = get_categories($args);
			foreach($select_values_object as $object){
				$select_values[] = $object->slug;
			}
			break;
		case 'Tag':
			$select_values_object = get_tags();
			foreach($select_values_object as $object){
				$select_values[] = $object->slug;
			}
			break;
		case 'Year':
			for($i=date("Y"); $i>=1980; $i--){
				$select_values[] = $i;
			}
			break;
		case 'Month':
			for($i=01; $i<=12; $i++){
				$select_values[] = $i;
			}
			break;
		case 'Day':
			for($i=01; $i<=31; $i++){
				$select_values[] = $i;
			}
			break;
		case 'Author':
			$select_values_object = get_users();
			foreach($select_values_object as $object){
				$select_values[] = $object->data->user_nicename;
			}
			break;
		default:
			$select_values = array(); 
			break;
	}

	$input = "";

	$input .= '<div class="jgood_archives_option_sub_group"><div class="jgood_archives_option_remove button button-primary jgood_archives_remove_button">x</div>';

	$input .= '<select name="jgood_archives_'.$name.'['.$num.']" id="jgood_archives_'.$name.'_'.$num.'" class="jgood-input">';
	$input .= '<option></option>';
	if(!empty($select_values)){
		foreach ($select_values as $select_option) {
			$input .= '<option';
			if($select_option == $value){ $input .= " selected" ; }
			$input .= '>'.$select_option.'</option>';
		}
	}
	$input .= '</select></div>';

	echo $input;

}

// create a rule to display
function display_rules($fieldNum, $sidebars, $values = array("filters"=>"", "sidebar"=>"")){
	echo'<tr class="jgood_archives_rule_group">
			<td>
				<div class="jgood_archives_option_rule_title"><label>Sidebar Rule</label></div><div class="jgood_archives_rule_add button button-primary jgood_archives_add_button">Add New</div>
				<div class="jgood_archives_option_rule_sub_title_a">Filters</div><div class="jgood_archives_option_rule_sub_title_b">Sidebar</div>
				<div class="jgood_archives_option_rule_content">
		';

	echo'<input type="text" class="jgood_archives_rule_text" name="jgood_archives_rule_text['.$fieldNum.']" id="jgood_archives_rule_text_'.$fieldNum.'" placeholder="sample-category/main-author/1980/October" value="'.$values['filters'].'"/>';

	echo'<select name="jgood_archives_rule_sidebar['.$fieldNum.']" id="jgood_archives_rule_sidebar_'.$fieldNum.'" class="jgood_archives_rule_select">';
		echo'<option></option>';
	foreach ($sidebars as $name => $details) {
		if($values['sidebar'] == $details['name']){
			echo'<option selected>'.$details['name'].'</option>';
		}else{
			echo'<option>'.$details['name'].'</option>';
		}
	}
	echo'</select>';

	echo'<div class="jgood_archives_rule_remove button button-primary jgood_archives_remove_button">x</div>';
	
	echo'
				</div>
			</td>
		</tr>';
}

// display new parent drop down for custom archive post
function jgood_archives_attributes_meta_box($post) {
	$post_type_object = get_post_type_object($post->post_type);

	// if custom post type is set as hierarchial display new parent drop down
	if ( $post_type_object->hierarchical ) {
		$pages = wp_dropdown_pages(array('post_type' => 'page', 'selected' => $post->post_parent, 'name' => 'parent_id', 'show_option_none' => __('(no parent)'), 'sort_column'=> 'menu_order, post_title', 'echo' => 0));
		if ( ! empty($pages) ) {
			echo $pages;
		}
	}
}

?>