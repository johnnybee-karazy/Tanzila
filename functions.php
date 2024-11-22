<?php
/**
 * Storefront engine room
 *
 * @package storefront
 */

/**
 * Assign the Storefront version to a var
 */
$theme              = wp_get_theme( 'storefront' );
$storefront_version = $theme['Version'];

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) ) {
	$content_width = 980; /* pixels */
}

$storefront = (object) array(
	'version'    => $storefront_version,

	/**
	 * Initialize all the things.
	 */
	'main'       => require 'inc/class-storefront.php',
	'customizer' => require 'inc/customizer/class-storefront-customizer.php',
);

require 'inc/storefront-functions.php';
require 'inc/storefront-template-hooks.php';
require 'inc/storefront-template-functions.php';
require 'inc/wordpress-shims.php';

if ( class_exists( 'Jetpack' ) ) {
	$storefront->jetpack = require 'inc/jetpack/class-storefront-jetpack.php';
}

if ( storefront_is_woocommerce_activated() ) {
	$storefront->woocommerce            = require 'inc/woocommerce/class-storefront-woocommerce.php';
	$storefront->woocommerce_customizer = require 'inc/woocommerce/class-storefront-woocommerce-customizer.php';

	require 'inc/woocommerce/class-storefront-woocommerce-adjacent-products.php';

	require 'inc/woocommerce/storefront-woocommerce-template-hooks.php';
	require 'inc/woocommerce/storefront-woocommerce-template-functions.php';
	require 'inc/woocommerce/storefront-woocommerce-functions.php';
}

if ( is_admin() ) {
	$storefront->admin = require 'inc/admin/class-storefront-admin.php';

	require 'inc/admin/class-storefront-plugin-install.php';
}


function create_cities_posttype() {
  
    register_post_type( 'Cities',
    // cpt options
        array(
            'labels' => array(
                'name' => __( 'Cities' ),
                'singular_name' => __( 'Cities' ),
                'menu_name'           => __( 'Cities', 'storefront' ),
        		'parent_item_colon'   => __( 'Parent City', 'storefront' ),
        		'all_items'           => __( 'All Cities', 'storefront' ),
        		'view_item'           => __( 'View City', 'storefront' ),
		        'add_new_item'        => __( 'Add New City', 'storefront' ),
		        'add_new'             => __( 'Add New', 'storefront' ),
		        'edit_item'           => __( 'Edit City', 'storefront' ),
		        'update_item'         => __( 'Update City', 'storefront' ),
		        'search_items'        => __( 'Search City', 'storefront' ),
		        'not_found'           => __( 'Not Found', 'storefront' ),
		        'not_found_in_trash'  => __( 'Not found in Trash', 'storefront' ),
            ),
            'public' => true,
            'has_archive' => true,
            'rewrite' => array('slug' => 'cities'),
            'show_in_rest' => true,
  
        )
    );
}
// Hooking up our function to theme setup
add_action( 'init', 'create_cities_posttype' );


add_action( 'init', 'create_country_hierarchical_taxonomy', 0 );
  
//create a custom taxonomy name it subjects for your posts
  
function create_country_hierarchical_taxonomy() {
  
// Add new taxonomy, make it hierarchical like categories
//first do the translations part for GUI
  
  $labels = array(
    'name' => _x( 'Countries', 'taxonomy general name' ),
    'singular_name' => _x( 'Country', 'taxonomy singular name' ),
    'search_items' =>  __( 'Search Countries' ),
    'all_items' => __( 'All Countries' ),
    'parent_item' => __( 'Parent Country' ),
    'parent_item_colon' => __( 'Parent Country:' ),
    'edit_item' => __( 'Edit Country' ), 
    'update_item' => __( 'Update Country' ),
    'add_new_item' => __( 'Add New Country' ),
    'new_item_name' => __( 'New Country Name' ),
    'menu_name' => __( 'Countries' ),
  );    
  
// Now register the taxonomy
  register_taxonomy('countries',array('cities'), array(
    'hierarchical' => true,
    'labels' => $labels,
    'show_ui' => true,
    'show_in_rest' => true,
    'show_admin_column' => true,
    'query_var' => true,
    'rewrite' => array( 'slug' => 'country' ),
  ));
  
}


/******add meta box*******/
add_action("admin_init", "admin_init");

function admin_init(){
  add_meta_box("latitude_meta", "Latitude", "latitude", "cities", "side", "low");
  add_meta_box("longitude_meta", "Longtitude", "longitude", "cities", "side", "low");
}

function latitude(){
  global $post;
  $custom = get_post_custom($post->ID);
  if(!empty($custom["latitude"][0])){
  		$latitude = $custom["latitude"][0];
  } else {
  	$latitude = '';
  }
  ?>
  <label>Latitude:</label>
  <input name="latitude" value="<?php echo $latitude; ?>" />
  <?php
}

function longitude() {
  global $post;
  $custom = get_post_custom($post->ID);
  if(!empty($custom["longitude"][0])){
  	$longitude = $custom["longitude"][0];
  } else {
  	$longitude = '';
  }
  ?>
  <label>Longtitude:</label>
  <input name="longitude" value="<?php echo $longitude; ?>" />
  <?php
}

add_action('save_post', 'save_post_meta_details');

function save_post_meta_details(){
  global $post;

  update_post_meta($post->ID, "latitude", $_POST["latitude"]);
  update_post_meta($post->ID, "longitude", $_POST["longitude"]);

}


/*********cpt widget***************/
class Cities_Widget extends WP_Widget{
	function __construct() {
		parent::__construct(
			'cities_widget', // Base ID
			'Cities Widget', // Name
			array('description' => __( 'Displays your latest listings. Outputs the Name, longtitude and latitude'))
		   );
	}
	function widget($args, $instance) { //output
		extract( $args );
		// these are the widget options
		$title = apply_filters('widget_title', $instance['title']);
		$numberOfListings = $instance['numberOfListings'];
		echo $before_widget;
		// Check if title is set
		if ( $title ) {
			echo $before_title . $title . $after_title;
		}
		$this->getCitiesListings($numberOfListings);
		echo $after_widget;
	}

	// widget form creation
	function form($instance) {

	// Check values
	if( $instance) {
		$title = esc_attr($instance['title']);
		$numberOfListings = esc_attr($instance['numberOfListings']);
	} else {
		$title = '';
		$numberOfListings = '';
	}
	?>
		<p>
		<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'cities_widget'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		</p>
		<p>
		<label for="<?php echo $this->get_field_id('numberOfListings'); ?>"><?php _e('Number of Listings:', 'realty_widget'); ?></label>		
		<select id="<?php echo $this->get_field_id('numberOfListings'); ?>"  name="<?php echo $this->get_field_name('numberOfListings'); ?>">
			<?php for($x=1;$x<=10;$x++): ?>
			<option <?php echo $x == $numberOfListings ? 'selected="selected"' : '';?> value="<?php echo $x;?>"><?php echo $x; ?></option>
			<?php endfor;?>
		</select>
		</p>		 
	<?php
	}

	// function update($new_instance, $old_instance) {
	// 	$instance = $old_instance;
	// 	$instance['title'] = strip_tags($new_instance['title']);
	// 	$instance['numberOfListings'] = strip_tags($new_instance['numberOfListings']);
	// 	return $instance;
	// }


	// showing html here
	function getCitiesListings($numberOfListings) { 
	global $post;
	$citieslistings = new WP_Query();
	$citieslistings->query('post_type=cities&posts_per_page=' . $numberOfListings );
	$appid = 'e2a5c33637f0db180522053d2f3654e6';
		if($citieslistings->found_posts > 0) {
			echo '<ul class="realty_widget">';
				while ($citieslistings->have_posts()) {
					$maintemp = 'https://api.openweathermap.org/data/2.5/weather?lat='.get_post_meta($post->ID, 'latitude', true).'&lon='.get_post_meta($post->ID, 'longitude', true).'&appid='.$appid;
					$response = wp_remote_get( $maintemp );
					$bd = $response['body'];
					$mt = json_decode($bd);
					//var_dump($mt);
					if(!empty($mt->main->temp)){
						$maintmp = number_format($mt->main->temp,2);
					}else{
						$maintmp = $mt->message;
					}
					$citieslistings->the_post();
					$listItem = '<li>';// . $image; 
					$listItem .= '<a href="' . get_permalink() . '">';
					$listItem .= get_the_title() . '</a>';
					$listItem .= '<br>Latitude: '. get_post_meta($post->ID, 'latitude', true);
					$listItem .= '<br>Longtitude: '. get_post_meta($post->ID, 'longitude', true);
					$listItem .= '<br>Current Temperature: '.$maintmp.' <br>';
					$listItem .= '<span>Added ' . get_the_date() . '</span></li>'; 
					echo $listItem; 
				}
			echo '</ul>';
			wp_reset_postdata(); 
		}else{
			echo '<p style="padding:25px;">No listing found</p>';
		} 
	}
} //end class Cities_Widget
register_widget('Cities_Widget');


/*********** start ajax search*******************/



/*************end ajax search****************************/

//add_filter('use_block_editor_for_post', '__return_false', 10);


/**
 * NUX
 * Only load if wp version is 4.7.3 or above because of this issue;
 * https://core.trac.wordpress.org/ticket/39610?cversion=1&cnum_hist=2
 */
if ( version_compare( get_bloginfo( 'version' ), '4.7.3', '>=' ) && ( is_admin() || is_customize_preview() ) ) {
	require 'inc/nux/class-storefront-nux-admin.php';
	require 'inc/nux/class-storefront-nux-guided-tour.php';
	require 'inc/nux/class-storefront-nux-starter-content.php';
}

/**
 * Note: Do not add any custom code here. Please use a custom plugin so that your customizations aren't lost during updates.
 * https://github.com/woocommerce/theme-customisations
 */
