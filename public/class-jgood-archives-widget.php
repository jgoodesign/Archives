<?php

/**
* JGood Archives widget
*
* @since 1.0.0
**/
class JGood_Archives_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 * @since 1.0.0
	 */
	function __construct() {
		parent::__construct(
			'jgood_archives_widget', // Base ID
			__( 'JGood Archives Widget', 'jgood_archives' ), // Name
			array( 'description' => __( 'Custom archive list', 'jgood_archives' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
     	
		// display before widget code and title
     	echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
		}
		
		$list_params = "";

		if(isset($instance['list-display'])){
			$list_display = $instance['list-display'];
		}else{
			$list_display = "list";
		}

		// if widget options are set display shortcode
		if(!empty($instance['jgood_widget_option'])){
			foreach ($instance['jgood_widget_option'] as $key => $param) {
				$list_params .= key($param) . "?" . current($param);
				$list_params .= " ";
			}
		
			$list_params .= "main?" . $instance['main-filter'];

			echo do_shortcode( '[jgoodarchives display="list" pagination="false" pp_page="500" list_params="'.$list_params.'" list_display="'.$list_display.'" ]' );
		}

		// display after widget code
		echo $args['after_widget'];
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 * @since 1.0.0
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {

		global $wpdb;
		$jgood_archives_options = $wpdb->prefix . 'archives_options';

		// grab options from db
		$options_groupA = $wpdb->get_results("SELECT label, name FROM $jgood_archives_options WHERE option_category = 0 AND object_id = 0 AND value = 1", ARRAY_A);

		// create array of options from db options
		$options = $this->buildOptions($options_groupA);

		// assign title from saved values
     	$title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'New title', 'jgood_archives' );
     	$main_filter = ! empty( $instance['main-filter'] ) ? $instance['main-filter'] : "";
     	$list_display = ! empty( $instance['list-display'] ) ? $instance['list-display'] : "";
		?>

		<!-- display title -->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>

		<!-- display filters group -->
		<p>
			<strong><?php _e("Filters") ?></strong>
		</p>
		<div class="jgood_archives_widget_item">
		<?php
			// show options from saved values
			echo $this->widgetOptions($options, $instance);
		?>
		</div>

		<!-- display main list display option -->
		<p>
			<label for="<?php echo $this->get_field_id( 'main-filter' ); ?>"><?php _e( "List Display" ); ?>:</label> 
			<select id="<?php echo $this->get_field_id( 'main-filter' ); ?>" name="<?php echo $this->get_field_name( 'main-filter' ); ?>" >
				<?php
					if($main_filter == "post-title"){
						echo'<option selected>post-title</option>';
					}else{
						echo'<option>post-title</option>';
					}

					foreach ($options as $name => $values) {
						if($main_filter == $name){
							echo '<option selected>'.$name.'</option>';
						}else{
							echo '<option>'.$name.'</option>';
						}
					}
				?>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'list-display' ); ?>"><?php _e( "Type of Display" ); ?>:</label> 
			<select id="<?php echo $this->get_field_id( 'list-display' ); ?>" name="<?php echo $this->get_field_name( 'list-display' ); ?>" >
				<?php
					if($list_display == "dropdown"){
						echo'<option>list</option>
							<option selected>dropdown</option>';
					}else{
						echo'<option selected>list</option>
							<option>dropdown</option>';
					}
				?>
			</select>
		</p>
		<?php
	}

	/**
	 * display options in widget setup
	 * @since 1.0.0
	 */
	public function widgetOptions($options, $instance, $defaultNumber = 2, $defaultType = array("category", "year"), $defaultValue = array("All", "All")){
		$output = "";

		// if saved values exist use them, else use defaults
		if(isset($instance['jgood_widget_option']) && count($instance['jgood_widget_option']) >= 1){
			$optionNumber = count($instance['jgood_widget_option']);
			foreach ($instance['jgood_widget_option'] as $key => $option) {
				foreach ($option as $name => $value) {
					$optionType[] = $name;
					$optionValue[] = $value;
				}
			}
		}else{
			$optionNumber = $defaultNumber;
			$optionType = $defaultType;
			$optionValue = $defaultValue;
		}

		// loop through options and output selects
		for ($i=0; $i < $optionNumber; $i++) { 
			$fieldname = "jgood_widget_option";

			// display type selector
			$output .= '<div class="jgood_widget_option_row">';
			$output .= '<select name="'.$this->get_field_name( $fieldname ).'['.$i.']" id="'.$this->get_field_id( $fieldname ).'_'.$i.'" class="jgood_widget_option_type">';
			foreach ($options as $name => $values) {
				if($optionType[$i] == $name){
					$output .= '<option selected>'.$name.'</option>';
				}else{
					$output .= '<option>'.$name.'</option>';
				}
			}
			$output .= '</select>';

			// display type sub selectors
			foreach ($options as $name => $values) {
				$valuename = $fieldname."_".$name;

				$output .= '<select name="'.$this->get_field_name( $valuename ).'['.$i.']" id="'.$this->get_field_id( $valuename ).'_'.$i.'" 
							class="jgood_widget_option_value';
				if($optionType[$i] == $name){ $output .= ' active">'; }
				else{ $output .= '">'; }
				$output .= '<option></option>';
				if($optionValue[$i] == "All"){ $output .= '<option selected>All</option>'; }
				else{ $output .= '<option>All</option>'; }
				foreach ($values as $value) {
					if($optionValue[$i] == $value){
						$output .= '<option selected>'.$value.'</option>';
					}else{
						$output .= '<option>'.$value.'</option>';
					}
				}
				$output .= '</select>';
			}

			// display remove button			
			$removeButton = $this->get_field_id( "jgood_widget_x_button_".$i );
			$output .= '<div data-option-num="'.$i.'" data-option-id="'.$removeButton.'" class="jgood_widget_button_remove button button-primary jgood_archives_remove_button">x</div>';
			
			// display add new button
			$addButton = $this->get_field_id( "jgood_widget_button_".$i );
			$output .= '<div data-option-num="'.$i.'" data-option-id="'.$addButton.'" class="jgood_widget_button button button-primary jgood_archives_add_button">Add</div>';
			$output .= '</div>';
			
		}

		return $output;
	}

	/**
	 * create array of options from db options
	 * @since 1.0.0
	 */
	public function buildOptions($option_group){
		$options = array();

		// for each option in db create a list of values
		foreach ($option_group as $details) {
			switch ($details['label']) {
				case 'Post-Type':
					$namesarray = get_post_types( '', 'names' );
					foreach ($namesarray as &$name) {
						if($name == "page"){
							$name = "pages";
						}
					}
					$options[$details['name']] = $namesarray;
					break;
				case 'Category':
					$args = array(
						'orderby' => 'name',
						'order' => 'ASC'
					);
					$options_object = get_categories($args);
					foreach($options_object as $object){
						$options[$details['name']][] = $object->slug;
					}
					break;
				case 'Tag':
					$options_object = get_tags();
					foreach($options_object as $object){
						$options[$details['name']][] = $object->slug;
					}
					break;
				case 'Year':
					for($i=date("Y"); $i>=1980; $i--){
						$options[$details['name']][] = $i;
					}
					break;
				case 'Month':
					global $month;
					foreach($month as $name){
						$options[$details['name']][] = $name;
					}
					break;
				case 'Day':
					for($i=01; $i<=31; $i++){
						$options[$details['name']][] = $i;
					}
					break;
				case 'Author':
					$options_object = get_users();
					foreach($options_object as $object){
						$options[$details['name']][] = $object->data->user_nicename;
					}
					break;
				default:
					$options[$details['name']] = array(); 
					break;
			}
		}

		return $options;
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 *@since 1.0.0
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		
		// save title
		if($new_instance['title'] != ""){ $instance['title'] = $new_instance['title']; }
		
		// save main list display
		if($new_instance['main-filter'] != ""){ $instance['main-filter'] = $new_instance['main-filter']; }

		if($new_instance['list-display'] != ""){ $instance['list-display'] = $new_instance['list-display']; }

		// save widget options in array
		$instance['jgood_widget_option'] = array();

        if ( isset ( $new_instance['jgood_widget_option'] ) )
        {
            foreach ( $new_instance['jgood_widget_option'] as $key=>$value )
            {
            	$optionName = "jgood_widget_option_".$value;
            	if($new_instance[$optionName][$key] !== ""){
            		$instance['jgood_widget_option'][] = array($value => $new_instance[$optionName][$key]);
            	}
            }
        }

		return $instance;
	}

}

?>