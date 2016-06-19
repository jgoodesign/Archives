<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    JGood_Archives
 * @subpackage JGood_Archives/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the dashboard-specific stylesheet and JavaScript.
 *
 * @package    JGood_Archives
 * @subpackage JGood_Archives/public
 * @author     Your Name <email@example.com>
 */
class JGood_Archives_Public {

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
	 * @var      string    $jgood_archives       The name of the plugin.
	 * @var      string    $version    The version of this plugin.
	 */
	public function __construct( $jgood_archives, $version ) {

		$this->jgood_archives = $jgood_archives;
		$this->version = $version;

		// include html file to access display functions
		require_once plugin_dir_path( __FILE__ ) . 'partials/jgood-archives-public-display.php';

		// include widget class
		require_once plugin_dir_path( __FILE__ ) . 'class-jgood-archives-widget.php';
	}

	// Register widget
	public function jgood_archives_register_widget(){
		register_widget( 'JGood_Archives_Widget' );
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in JGood_Archives_Public_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The JGood_Archives_Public_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->jgood_archives, plugin_dir_url( __FILE__ ) . 'css/jgood-archives-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in JGood_Archives_Public_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The JGood_Archives_Public_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->jgood_archives, plugin_dir_url( __FILE__ ) . 'js/jgood-archives-public.js', array( 'jquery' ), $this->version, false );

	}

	/**
	* send page to correct templates
	*
	* @since 1.0.0
	**/
	public function jgood_archives_template($template) {
    	global $wp_query, $post;

    	if (isset($wp_query->query['jgood_url'])){
	    	$jgood_url = $wp_query->query['jgood_url'];
	    }else{
	    	$jgood_url = false;
	    }

	    if (isset($post->post_type)){
	    	$jgood_post_type = $post->post_type;
	    }else{
	    	$jgood_post_type = null;
	    }

    	/* Checks for archive by url */
		if ($jgood_url == "true"){
			// if general archive has a template use that
			// jgood-archives-general.php
			if(locate_template('jgood-archives-general.php') != ''){
				return locate_template('jgood-archives-general.php');
			}
			// if archive doesnt have a template use the default archive template
		    else if(file_exists(plugin_dir_path( __FILE__ ). 'partials/custom-jgood-archives.php')){
		        return plugin_dir_path( __FILE__ ) . 'partials/custom-jgood-archives.php';
		    }

		/* Checks for archive by post type */
		}else if ($jgood_post_type == "jgood_archives"){
			// if specific archive has a public template use that
			// jgood-archives-archivenamehere.php
			if(locate_template('jgood-archives-' . $post->post_name . '.php') != ''){
				return locate_template('jgood-archives-' . $post->post_name . '.php');
			}
			// if single archive has a public template use that
			// jgood-archives-single.php
			else if(locate_template('jgood-archives-single.php') != ''){
				return locate_template('jgood-archives-single.php');
			}
			// if archive doesnt have a template use the default archive template
		    else if(file_exists(plugin_dir_path( __FILE__ ). 'partials/single-jgood-archives.php')){
		        return plugin_dir_path( __FILE__ ) . 'partials/single-jgood-archives.php';
		    }
		}

	    return $template;
	}

	/**
	* fix redirects for archives to work with pagination
	*
	* @since 1.0.0
	**/
	public function jgood_archives_redirect_canonical($redirect_url) {
		global $post;
		// check to make sure you are on an archive page
		// if you are, turn off canonical redirect
	    if (is_singular() && $post->post_type == "jgood_archives") $redirect_url = false;
		return $redirect_url;
	}

	/**
	* setup jgoodarchives shortcode
	*
	* @since 1.0.0
	**/
	public function jgood_archives_page_shortcode( $args ) {
	    // assign shortcode variables to array
	    $jgood_archives_args = shortcode_atts( array(
	    	'display' => 'posts',
	        'archive_id' => null,
	        'fields' => null,
	        'pp_page' => get_option( 'posts_per_page' ),
	        'full_content' => 'false',
	        'excerpt' => 'true',
	        'excerpt_length' => '400',
	        'meta_author' => 'true',
	        'meta_date' => 'true',
	        'meta_date_format' => 'F j, Y',
	        'meta_category' => 'true',
	        'item_title' => 'true',
	        'post_class' => '',
	        'list_params' => '',
	        'list_display' => 'list',
	        'dropdown_default' => '',
	        'pagination' => 'true',
	        'template' => '',
			'pagination_show_all' => false,
			'pagination_prev_next' => true,
			'pagination_prev_text' => __( 'Previous', 'jgood_archives' ),
			'pagination_next_text' => __( 'Next', 'jgood_archives' ),
			'pagination_end_size' => 1,
			'pagination_mid_size' => 2,
			'pagination_before_page_number' => '',
			'pagination_after_page_number' => '',
			'pagination_screen_reader_text' => __( 'Posts navigation', 'jgood_archives' )
	    ), $args );

	    global $wp_query;

	    // if display is set to 'posts' display list of post content
	    if($jgood_archives_args['display'] == "posts"){

	    	// if jgood_fields is set in the query then get fields from url
	    	if(isset($wp_query->query['jgood_fields'])){
		    	if($jgood_archives_args['fields'] == null){
					$jgood_archives_args['fields'] = $wp_query->query['jgood_fields'];
		    	}
		    
		    // if jgood_fields is not set in query treat as a single archive page
		    }else if($jgood_archives_args['fields'] == null){
		    	if($jgood_archives_args['archive_id'] == null){
			    	$jgood_archives_args['archive_id'] = get_the_ID();
			    }
		    }

		    // if archive id and url fields are not set display no content message
		    if($jgood_archives_args['archive_id'] == null && $jgood_archives_args['fields'] == null){
		    	require $this->jgood_archives_send_none();
    			return;
		    }

		    // build query args array
			$query_args = $this->jgood_archives_build_query($jgood_archives_args);

			// create new query
			$archive_query = new WP_Query( $query_args );

			// save old query and replace with new query
			$main_query = $wp_query;
			$wp_query = NULL;
			$wp_query = $archive_query;

			// setup pagination args from shortcode arg
			$pagination_args = array(
		    	'show_all' => $jgood_archives_args['pagination_show_all'],
				'prev_next' => $jgood_archives_args['pagination_prev_next'],
				'prev_text' => $jgood_archives_args['pagination_prev_text'],
				'next_text' => $jgood_archives_args['pagination_next_text'],
				'end_size' => $jgood_archives_args['pagination_end_size'],
				'mid_size' => $jgood_archives_args['pagination_mid_size'],
				'before_page_number' => $jgood_archives_args['pagination_before_page_number'],
				'after_page_number' => $jgood_archives_args['pagination_after_page_number'],
				'screen_reader_text' => $jgood_archives_args['pagination_screen_reader_text']
		    );

			// start output buffering
			ob_start();

			// check if query returned posts
			if ( $archive_query->have_posts() ){
				
				// loop through query of posts
				while ( $archive_query->have_posts() ) : $archive_query->the_post();
					// if sepecific content template passed through shortcode exists use that
					if(locate_template('content-jgood-archives-' . $jgood_archives_args['template'] . '.php') != ''){
						require locate_template('content-jgood-archives-' . $jgood_archives_args['template'] . '.php');
					// if general jgood archive content template exists use that
					}else if(locate_template('content-jgood-archives.php') != ''){
						require locate_template('content-jgood-archives.php');
					// if no content templates exist use default content template
					}else{
						if(file_exists(plugin_dir_path( __FILE__ ). 'partials/content-jgood-archives.php')){
					        require plugin_dir_path( __FILE__ ) . 'partials/content-jgood-archives.php';
					    }
					}
				endwhile;

				// display previous/next page navigation.
				if($jgood_archives_args['pagination'] == "true"){
					the_posts_pagination( $pagination_args );
				}

			// if query returned nothing display none content
			}else{ 
		    	require $this->jgood_archives_send_none();
    			return;
			}

			// reset query to original saved query
			$wp_query = NULL;
			$wp_query = $main_query;

			// clean output buffer and return results
			$content = ob_get_contents();
			ob_end_clean();

			return $content;
	    }
	    // display link list instead of posts
	    else if($jgood_archives_args['display'] == "list"){
	    	// if display is list but list parameter is empty display none content
	    	if($jgood_archives_args['list_params'] == ''){
		    	require $this->jgood_archives_send_none();
    			return;
		    }

		    // create list from shortcode params
		    $content = $this->display_list($jgood_archives_args);

	    	return $content;
	    }
	    // if diplay is not set correctly return nothing
	    else{
    		require $this->jgood_archives_send_none();
    		return;
	    }

	}

	/**
	* Grab content-none from templates
	*
	* @since 1.0.0
	**/
	public function jgood_archives_send_none(){
		// if content-none exists in theme use that
		if(locate_template('content-none.php') != ''){
			$content = locate_template('content-none.php');
		// if not use default content-none
		}else{
			$content = plugin_dir_path( __FILE__ ) . 'partials/content-jgood-archives-none.php';
		}
		return $content;
	}

	/**
	* Builds query from shortcode params to use with WP_Query
	*
	* @since 1.0.0
	**/
	public function jgood_archives_build_query($jgood_archives_args){
		global $wpdb;
		// grab options table
		$jgood_archives_options = $wpdb->prefix . 'archives_options';

		$query_args = array();

		// detirmine paged value for use in pagination
		if($jgood_archives_args['pagination'] == "true"){
			if ( get_query_var('paged') ) {
			    $paged = get_query_var('paged');
			} elseif ( get_query_var('page') ) {
			    $paged = get_query_var('page');
			} else {
			    $paged = 1;
			}
		}else{
			$paged = 0;
		}

		// initialize query variables
		$query_args = array(
			"post_status" => array("publish"),
			"posts_per_page" => $jgood_archives_args['pp_page'],
			"nopaging"=> false,
			'paged' => $paged
		);

		// if specific archive page is being used
		if(isset($jgood_archives_args['archive_id'])){

			// grab admin options from db
			$options_groupA = $wpdb->get_results("SELECT label FROM $jgood_archives_options WHERE option_category = 0 AND object_id = 0 AND value = 1", ARRAY_A);

			// grab archive page options from db
			$archive_id = $jgood_archives_args['archive_id'];
			$results = $wpdb->get_results("SELECT label, value FROM $jgood_archives_options WHERE object_id = $archive_id", ARRAY_A);

			// assign page options to array based on common label

			foreach ($results as $id => $option) {
				// if option is turned on in admin settings assign to args
				if(array_search($option['label'], array_column($options_groupA, 'label')) !== false){
					$archive_options[$option['label']][] = $option['value'];
				}
			}

			// replace category slug with category ID to use in query
			if(isset($archive_options['Category'])){
				foreach ($archive_options['Category'] as $key=>$value) {
					$category_object = get_category_by_slug( $value );
					$query_args['category__in'][] = $category_object->cat_ID;
				}
			}
			
			// replace author nicename with author ID to use in query
			if(isset($archive_options['Author'])){
				foreach ($archive_options['Author'] as $key=>$value) {
					$user_object = get_user_by('slug',$value);
					$query_args['author__in'][] = $user_object->ID;
				}
			}
			
			if(!isset($archive_options['Year'])){
				$archive_options['Year'] = null;
			}
			if(!isset($archive_options['Month'])){
				$archive_options['Month'] = null;
			}
			if(!isset($archive_options['Day'])){
				$archive_options['Day'] = null;
			}
			
			// create date query array with year, month and day arrays
			$query_args['date_query'] = $this->jgood_archives_build_date_query($archive_options['Year'], $archive_options['Month'], $archive_options['Day']);

			// assign options to correct query args
			if(isset($archive_options['Post-Type'])){
				$query_args['post_type'] = $archive_options['Post-Type'];
			}else{
				$query_args['post_type'] = null;
			}
			
			if(isset($archive_options['Tag'])){
				$query_args['tag_slug__in'] = $archive_options['Tag'];
			}else{
				$query_args['tag_slug__in'] = null;
			}
		
		// If using custom url archive
		}else if($jgood_archives_args['fields'] != null){

			// grab parameters from url field
			$url_fields = explode("/", $jgood_archives_args['fields']);

			// get admin options from db
			$options_groupA = $wpdb->get_results("SELECT name FROM $jgood_archives_options WHERE option_category = 0 AND object_id = 0 AND value = 1", ARRAY_A);
			
			$years = array();
			$months = array();
			$days = array();

			// loop through url parameters and check against each filter type
			foreach ($url_fields as $field) {
				$match = 0;

				// check post-type
				if(array_search('post-type', array_column($options_groupA, 'name')) !== false){
					if(get_post_type_object($field)){
						$query_args['post_type'][] = $field;
						$match++;
					}else if($field == "pages"){
						$query_args['post_type'][] = "page";
						$match++;
					}
				}

				// check category
				if(array_search('category', array_column($options_groupA, 'name')) !== false){
					if(get_category_by_slug( $field )){
						$category_object = get_category_by_slug( $field );
						$query_args['category__in'][] = $category_object->cat_ID;
						$match++;
					}
				}

				// check tag
				if(array_search('tag', array_column($options_groupA, 'name')) !== false){
					if(get_term_by('slug', $field, 'post_tag')){
						$query_args['tag_slug__in'][] = $field;
						$match++;
					}
				}

				// check year
				if(array_search('year', array_column($options_groupA, 'name')) !== false){
					if((int)$field >= 1980 && (int)$field <= date("Y")){
						$years[] = $field;
						$match++;
					}
				}

				// check month
				if(array_search('month', array_column($options_groupA, 'name')) !== false){
					global $month;
					if(in_array(ucfirst($field), $month)){
						$months[] = array_search(ucfirst($field), $month);
						$match++;
					}
				}

				// check day
				if(array_search('day', array_column($options_groupA, 'name')) !== false){
					if((int)$field >= 1 && (int)$field <= 31){
						$days[] = $field;
						$match++;
					}
				}

				// check author
				if(array_search('author', array_column($options_groupA, 'name')) !== false){
					if(get_user_by( "slug", $field )){
						$user_object = get_user_by('slug',$field);
						$query_args['author__in'][] = $user_object->ID;
						$match++;
					}
				}

				// if filter does not match and is not a useful keyword or blank assign to posttype so nothing is displayed and error is obvious
				if($match == 0 && $field != "All" && $field != "post-title" && $field != ""){
					$query_args['post_type'][] = $field;
				}
			}

			// assign date query to query variables
			$query_args['date_query'] = $this->jgood_archives_build_date_query($years, $months, $days);
		}

		return $query_args;
	}

	/**
	* build datequery array for use in WP_Query
	*
	* @since 1.0.0
	**/
	public function jgood_archives_build_date_query($years=null, $months=null, $days=null){
		$date_query = array();

		// if using multiple dates set query relation to OR
		if(count($years) > 1 || count($months) > 1 || count($days) > 1){
			$date_query['relation'] = "OR";
		}
		// assign each year value to a date query array
		if(isset($years)){
			foreach($years as $key=>$value){
				$date_query[$key]['year'] = (int)$value;
			}
		}
		//assign each month value to a date query array
		if(isset($months)){
			foreach($months as $key=>$value){
				$date_query[$key]['month'] = (int)$value;
			}
		}
		//assign each day value to a date query array
		if(isset($days)){
			foreach($days as $key=>$value){
				$date_query[$key]['day'] = (int)$value;
			}
		}

		return $date_query;
	}

	/**
	* display list of items detirmined by shortcode/widget
	*
	* @since 1.0.0
	**/
	public function display_list($jgood_archives_args){
		$list_elements = array();

		// create array of filters to check based on shortcode args
		$param_elements = explode(" ", $jgood_archives_args['list_params']);
		foreach ($param_elements as $key => $value) {
			if($value != ""){
				$value_elements = explode("?", $value);
				$list_elements[$value_elements[0]][] = $value_elements[1];
			}
		}

		$urlFields = "";

		// loop through filters and assign them to a url string for link use if not main display
		foreach($list_elements as $label=>$filter){
			if($label != "main"){
				foreach ($filter as $id=>$value) {
					if($value == "page"){
						$urlFields .= "pages/";
					}else{
						$urlFields .= $value."/";
					}
				}
			}
		}

		// add start to url string for linking
		$urlFormat = "jgood_custom_archives/" . $urlFields;

		// test main list type against options and create list elements
		switch ($list_elements['main'][0]) {
			// if post type create a list of post types
			case 'post-type':
				$namesarray = get_post_types( '', 'names' );
				foreach ($namesarray as &$name) {
					if($name == "page"){
						$name = "pages";
					}
				}
				$list_values = $namesarray;
				break;
			// if category create list of categories
			case 'category':
				$args = array(
					'orderby' => 'name',
					'order' => 'ASC'
				);
				$list_values_object = get_categories($args);
				foreach($list_values_object as $object){
					$list_values[] = $object->slug;
				}
				break;
			// if tag create list of tags
			case 'tag':
				$list_values_object = get_tags();
				foreach($list_values_object as $object){
					$list_values[] = $object->slug;
				}
				break;
			// if year create a list of years
			case 'year':
				for($i=date("Y"); $i>=1980; $i--){
					$list_values[] = $i;
				}
				break;
			// if month create a list of months
			case 'month':
				global $month;
				foreach($month as $num=>$name){
					$list_values[] = $name;
				}
				break;
			// if day create a list of days
			case 'day':
				for($i=01; $i<=31; $i++){
					$list_values[] = $i;
				}
				break;
			// if author create a list of authors
			case 'author':
				$list_values_object = get_users();
				foreach($list_values_object as $object){
					$list_values[] = $object->data->user_nicename;
				}
				break;
			// if post title grab all post tiles that match current filter params and display in list
			case 'post-title';
				$display = "";

				if($jgood_archives_args['list_display'] == 'dropdown'){
					$display .= '<select onchange="document.location.href=this.options[this.selectedIndex].value;" name="jgood-archives-dropdown">';
					$display .= '<option>Select '.ucfirst($list_elements['main'][0]).'</option>';
				}else{
					$display .= '<ul>';
				}
				
				// assign current list params to args
				$test_args = array(
					'pp_page' => $jgood_archives_args['pp_page'],
					'fields' => $urlFields.'/',
					'pagination' => 'false'
				);

				// get query args based on current list params
				$test_query_args = $this->jgood_archives_build_query($test_args);

				// create query
				$test_query = new WP_Query( $test_query_args );

				// loop through posts matching query and add the title to the list
				while ( $test_query->have_posts() ) {
					$test_query->the_post();
					if($jgood_archives_args['list_display'] == 'dropdown'){
						$display .= the_title( sprintf( '<option value="%s">', esc_url( get_permalink() ) ), '</option>' );
					}else{
						$display .= the_title( sprintf( '<li><a href="%s">', esc_url( get_permalink() ) ), '</a></li>' );
					}
				}

				if($jgood_archives_args['list_display'] == 'dropdown'){
					$display .= "</select>";
				}else{
					$display .= "</ul>";
				}

				return $display;
				break;
			// if not matching any options empty list
			default:
				$list_values = array(); 
				break;
		}

		$display = "";

		// create list for options other than post-title
		if($jgood_archives_args['list_display'] == 'dropdown'){
			$display .= '<select onchange="document.location.href=this.options[this.selectedIndex].value;" name="jgood-archives-dropdown">';
			$display .= '<option>Select '.ucfirst($list_elements['main'][0]).'</option>';
		}else{
			$display .= '<ul>';
		}

		// loop through list values and create list item
		foreach ($list_values as $key => $value) {
			// create query args to test
			$test_args = array(
				'pp_page' => $jgood_archives_args['pp_page'],
				'fields' => $urlFields.$value.'/',
				'pagination' => 'false'
			);

			// build test query from query args
			$test_query_args = $this->jgood_archives_build_query($test_args);

			// create test query
			$test_query = new WP_Query( $test_query_args );

			// if query of list item has posts display list item
			if ( $test_query->have_posts() ){
				if($jgood_archives_args['list_display'] == 'dropdown'){
					$display .= '<option value="'.home_url($urlFormat).$value.'/">'.$value.'</option>';
				}else{
					$display .= '<li><a href="'.home_url($urlFormat).$value.'/">'.$value.'</a></li>';
				}
				
			}
		}
		
		if($jgood_archives_args['list_display'] == 'dropdown'){
			$display .= "</select>";
		}else{
			$display .= "</ul>";
		}

		return $display;
	}

	/**
	* grab correct sidebar from rules detirmined in settings
	*
	* @since 1.0.0
	**/
	public function jgood_archives_get_sidebar( $before, $after="</div>" ){
		global $wpdb, $wp_query;
		$jgood_archives_options = $wpdb->prefix . 'archives_options';

		if(isset($wp_query->query['jgood_fields'])){
			$jgood_fields = $wp_query->query['jgood_fields'];
		}else{
			$jgood_fields = "";
		}

		// if before string is not set assign it to default
		if($before == ""){
			$before='<div class="sidebar">';
		}

		// grab rules from db
		$options_groupB = $wpdb->get_results("SELECT value FROM $jgood_archives_options WHERE option_category = 1 AND object_id = 0");

		$rules = array();
		// unserialize rules and assign to array
		foreach($options_groupB as $num=>$rule){
			$rules[] = unserialize($rule->value);
		}

		$sidebar = null;
		// loop through rules
		foreach($rules as $rule){
			// if rule matches current url then assign to correct sidebar
			if(strpos($jgood_fields, rtrim($rule['filters'], "/")) !== false){
				$sidebar = $rule['sidebar'];
			}
		}

		// if sidebar is found display that sidebar with before and after strings
		if($sidebar != null){
			echo $before;
			dynamic_sidebar( $sidebar ); 
			echo $after;
		// if sidebar isnt found get default sidebar
		}else{
			get_sidebar();
		}
	}

	/**
	* get title string based on current url
	*
	* @since 1.0.0
	**/
	public function jgood_archives_get_title( $title_before, $title_after="Archive" ){
		global $wp_query;

		if(isset($wp_query->query['jgood_fields'])){
			$jgood_fields = $wp_query->query['jgood_fields'];
		}else{
			$jgood_fields = "";
		}

		// explode current url params
		$fields = explode("/", $jgood_fields);

		// display title
		$title = $title_before;
		foreach ($fields as $key => $name) {
			$title .= ucfirst($name) . " ";
		}
		$title .= $title_after;

		echo $title;

	}

}
