<?php
/**
 * norhagewebshop functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package norhagewebshop
 */

if ( ! defined( '_G_VERSION' ) ) {
	// Replace the version number of the theme on each release.
	define( '_G_VERSION', '0.0.20' );
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function norhagewebshop_setup() {
	/*
		* Make theme available for translation.
		* Translations can be filed in the /languages/ directory.
		* If you're building a theme based on norhagewebshop, use a find and replace
		* to change 'norhagewebshop' to the name of your theme in all the template files.
		*/
	load_theme_textdomain( 'norhagewebshop', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
		* Let WordPress manage the document title.
		* By adding theme support, we declare that this theme does not use a
		* hard-coded <title> tag in the document head, and expect WordPress to
		* provide it for us.
		*/
	add_theme_support( 'title-tag' );

	/*
		* Enable support for Post Thumbnails on posts and pages.
		*
		* @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		*/
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus(
		array(
			'menu-1' => esc_html__( 'Primary', 'norhagewebshop' ),
			'menu-2' => esc_html__( 'Secondary navigation', 'norhagewebshop' ),
			'menu-3' => esc_html__( 'Footer navigation', 'norhagewebshop' ),
		)
	);

	/*
		* Switch default core markup for search form, comment form, and comments
		* to output valid HTML5.
		*/
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	add_theme_support( 'align-wide' );

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'widgets' );
	add_theme_support( 'customize-selective-refresh-widgets' );

	add_theme_support('woocommerce');
}
add_action( 'after_setup_theme', 'norhagewebshop_setup' );



/**
 * Create a widget area for filters
 * */
add_action( 'widgets_init', 'norhage_register_sidebars' );
function norhage_register_sidebars() {
	register_sidebar([
		'id'            => 'before_shop_loop',
		'name'          => __( 'Above products listing' ),
		'description'   => __( 'A widget area above the products listing' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	]);
}

add_action('woocommerce_before_shop_loop', 'norhage_sidebar_before_shop_loop', 10);
function norhage_sidebar_before_shop_loop(){
	get_sidebar('before_shop_loop');
}

/**
 * We use WordPress's init hook to make sure
 * our blocks are registered early in the loading
 * process.
 *
 * @link https://developer.wordpress.org/reference/hooks/init/
 */
function norhagewebshop_register_acf_blocks() {
    /**
     * We register our block's with WordPress's handy
     * register_block_type();
     *
     * @link https://developer.wordpress.org/reference/functions/register_block_type/
     */
    register_block_type( __DIR__ . '/blocks/headerblock' );
    register_block_type( __DIR__ . '/blocks/header-slider-block' );
    register_block_type( __DIR__ . '/blocks/product-header-block' );
    register_block_type( __DIR__ . '/blocks/text-image-block' );
    register_block_type( __DIR__ . '/blocks/projects-block' );
    register_block_type( __DIR__ . '/blocks/products-slider-block' );
    register_block_type( __DIR__ . '/blocks/cta-block' );
    register_block_type( __DIR__ . '/blocks/cta-within-text-block' );
    register_block_type( __DIR__ . '/blocks/reviews-block' );
    register_block_type( __DIR__ . '/blocks/categories-block' );
    register_block_type( __DIR__ . '/blocks/products-block' );
}
// Here we call our tt3child_register_acf_block() function on init.
add_action( 'init', 'norhagewebshop_register_acf_blocks' );


/**
 * ADD THE PRODUCTS TO THE CATEGORY MENU ITEMS
 * */
function norhage_menu_add_category_posts( $output, $item, $depth, $args ) {
    // Check if the item is a Category or Custom Taxonomy
    if( $args->menu_id == 'primary-menu' && $item->type == 'taxonomy' && $depth == 1 ) {
        $posts = get_posts([
        	'post_type'		=> 'product',
        	'numberposts'	=> -1,
        	'tax_query'		=> [
        		[
	        		'taxonomy'		=> $item->object,
	        		'field'			=> 'term_id',
	        		'terms'			=> $item->object_id
	        	]
        	]
        ]);

        // Check count, if more than 0 display count
        if(count($posts) > 0){
        	$output .= '<ul class="products-sub-menu">';
        	foreach($posts as $post){
        		$thumb = get_the_post_thumbnail($post->ID, [420, 420], ['loading' => 'lazy', 'fetchpriority' => 'auto']);
        		$output .= '<li class="image-button"><a href="' . esc_url( get_permalink($post) ) . '">' . $thumb . '</a><div class="title-price"><span class="title"><a href="' . esc_url( get_permalink($post) ) . '">' . get_the_title($post) . '</a></span></div></li>' ;
        	}
        	$output .= '</ul>';
        }
    }

    if( $args->menu_id == 'primary-menu' && $item->type == 'post_type' && $item->object == 'service') {
    	$thumb = get_the_post_thumbnail($item->object_id, [420,420], ['loading' => 'lazy', 'fetchpriority' => 'auto']);
        $output = '<div class="image-button"><a href="' . esc_url( $item->url ) . '">' . $thumb . '</a><div class="title-price"><span class="title"><a href="' . esc_url( $item->url ) . '">' . $item->title . '</a></span></div></div>' ;
    }
    if( in_array($args->menu_id, ['primary-menu', 'secondary-menu']) && in_array('menu-item-has-children', $item->classes) ){
	    $output .= '<button class="expander">' . __('expand', 'norhagewebshop') . '</button>';
	}

    return $output;
}
add_action( 'walker_nav_menu_start_el', 'norhage_menu_add_category_posts', 10, 4 );

# filter_hook function to react on start_in argument
function norhage_wp_nav_menu_objects_start_in( $sorted_menu_items, $args ) {
    if(isset($args->show_submenu) && $args->show_submenu) {
        $root_id = 0;
        foreach( $sorted_menu_items as $key => $item ) {
        	if($item->current){
        		$root_id = $item->menu_item_parent?? $item->ID;
        		break;
        	}
        }

        $menu_item_parents = array();
        foreach( $sorted_menu_items as $key => $item ) {
            // init menu_item_parents
            if( $item->ID == (int)$root_id ) $menu_item_parents[] = $item->ID;

            if( in_array($item->menu_item_parent, $menu_item_parents) || in_array($item->ID, $menu_item_parents) ) {
                // part of sub-tree: keep!
                $menu_item_parents[] = $item->ID;
            } else {
                // not part of sub-tree: away with it!
                unset($sorted_menu_items[$key]);
            }
        }
        return $sorted_menu_items;
    } else {
        return $sorted_menu_items;
    }
}
add_filter("wp_nav_menu_objects",'norhage_wp_nav_menu_objects_start_in',10,2);


/**
 * Enqueue scripts and styles.
 */
function norhagewebshop_scripts() {
	wp_enqueue_style( 'slick', get_stylesheet_directory_uri() . '/js/slick/slick.css', [], _G_VERSION );
	wp_enqueue_style( 'norhagewebshop-style', get_stylesheet_uri(), [], _G_VERSION );
	wp_enqueue_script( 'slick-js', get_stylesheet_directory_uri() . '/js/slick/slick.min.js', ['jquery'], _G_VERSION, ['in_footer' => true, 'strategy' => 'defer'] );
	wp_enqueue_script('norhagewebshop-misc', get_stylesheet_directory_uri() . '/js/frontend.js', ['jquery', 'wc-settings'], _G_VERSION, ['in_footer' => true, 'strategy' => 'defer']);
	/*$myThemeParams = [
		'test1'	=> 'lalala'
	];
	wp_add_inline_script('norhagewebshop-misc', 'var myThemeParams = ' . wp_json_encode( $myThemeParams ), 'before');*/
}
add_action( 'wp_enqueue_scripts', 'norhagewebshop_scripts' );

/**
 * Ajax cart.
 */
require get_template_directory() . '/inc/ajax-cart.php';

/**
 * Custom shipping costs calculator.
 */
require get_template_directory() . '/inc/calc-shipping-costs.php';

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	require get_template_directory() . '/inc/jetpack.php';
}

add_action('admin_init', function () {
    // Redirect any user trying to access comments page
    global $pagenow;
     
    if ($pagenow === 'edit-comments.php') {
        wp_safe_redirect(admin_url());
        exit;
    }
 
    // Remove comments metabox from dashboard
    remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
 
    // Disable support for comments and trackbacks in post types
    foreach (get_post_types() as $post_type) {
        if (post_type_supports($post_type, 'comments')) {
            remove_post_type_support($post_type, 'comments');
            remove_post_type_support($post_type, 'trackbacks');
        }
    }
});


/**
 * CREATE NORHAGE BLOCKS CATEGORY
 */
add_filter( 'block_categories_all' , function( $categories ) {

    // Adding a new category.
	array_unshift( $categories, array(
		'slug'  => 'norhage',
		'title' => 'Norhage'
	));

	return $categories;
} );




/**
 * Read more link on excerpts
 * */
function wpdocs_excerpt_more( $more ) {
	return sprintf( ' <a class="read-more" href="%1$s">%2$s...</a>',
			get_permalink( get_the_ID() ),
			__( 'Read More', 'norhagewebshop' )
		);
}
add_filter( 'excerpt_more', 'wpdocs_excerpt_more' );








/**
 * REMOVE COMMENTS
 */ 
// Close comments on the front-end
add_filter('comments_open', '__return_false', 20, 2);
add_filter('pings_open', '__return_false', 20, 2);
 
// Hide existing comments
add_filter('comments_array', '__return_empty_array', 10, 2);
 
// Remove comments page in menu
add_action('admin_menu', function () {
    remove_menu_page('edit-comments.php');
});
 
// Remove comments links from admin bar
add_action('init', function () {
    if (is_admin_bar_showing()) {
        remove_action('admin_bar_menu', 'wp_admin_bar_comments_menu', 60);
    }
});



/**
 * if there's no post-thumbnail, get the first image from the content.
 * better then nothing
 */
add_filter( 'post_thumbnail_id', function($thumbnail_id, $post ){
	if(!$thumbnail_id && $post->post_type == 'product_variation'){
		$thumbnail_id = get_post_thumbnail_id($post->post_parent);
	}
	if(!$thumbnail_id){
		$content = get_post_field('post_content', $post->ID);
		preg_match('/(wp:image {"id":|"images":\[")(\d+)/', $content, $matches);
		if(isset($matches[2]) && is_numeric($matches[2]) ){
			$thumbnail_id = $matches[2];
		}
	}
	return $thumbnail_id;
	
}, 10, 2);



/**
 * Alter add-to-cart links in catalog for products with cutting fee
 * */
add_filter('woocommerce_loop_add_to_cart_link', 'norhage_woocommerce_loop_add_to_cart_link', 10, 3);
function norhage_woocommerce_loop_add_to_cart_link($link, $product, $args){
	$cutting_fee = get_field('cutting_fee', $product->ID);
	$has_cutting_fee = ($cutting_fee && $cutting_fee > 0)? true : false;

	if($product->is_type('simple') && $has_cutting_fee){
		$link = sprintf(
			'<a href="%s" data-quantity="%s" class="%s" %s>%s</a>',
			esc_url( $product->get_permalink() ),
			esc_attr( isset( $args['quantity'] ) ? $args['quantity'] : 1 ),
			'button product_type_variable add_to_cart_button',
			isset( $args['attributes'] ) ? wc_implode_html_attributes( $args['attributes'] ) : '',
			__('Customize', 'norhagewebshop')
		);
	}else if($product->is_type('variable')){
		$link = sprintf(
			'<a href="%s" data-quantity="%s" class="%s" %s>%s</a>',
			esc_url( $product->add_to_cart_url() ),
			esc_attr( isset( $args['quantity'] ) ? $args['quantity'] : 1 ),
			esc_attr( isset( $args['class'] ) ? $args['class'] : 'button' ),
			isset( $args['attributes'] ) ? wc_implode_html_attributes( $args['attributes'] ) : '',
			__('Customize', 'norhagewebshop')
		);
	}
	return $link;
}


function norhage_woocommerce_add_to_cart_validation($passed, $product_id, $quantity){
	if(isset($_POST['cutting_variables']) 
		&& (empty($_POST['cutting_variables']['width']) || empty($_POST['cutting_variables']['width']) )  
	){
		$passed = false;
		wc_add_notice( __( 'Please, enter a value for width and height.', 'norhagewebshop'), 'error' );
	}

	if(isset($_POST['cutting_variables'])){
		// maybe the product is actually a variant
		if($parent_id = wp_get_post_parent_id($product_id)){
			$product_id = $parent_id;
		}

		// check width > 0, height > 0
		$min_width = floatval(get_field('min_width', $product_id)) * 1000;
		$max_width = floatval(get_field('max_width', $product_id)) * 1000;
		$min_height = floatval(get_field('min_height', $product_id)) * 1000;
		$max_height = floatval(get_field('max_height', $product_id)) * 1000;

		if(floatval($_POST['cutting_variables']['width']) < $min_width
			|| floatval($_POST['cutting_variables']['width']) > $max_width
			|| floatval($_POST['cutting_variables']['height']) < $min_height
			|| floatval($_POST['cutting_variables']['height']) > $max_height
		){
			$passed = false;
			wc_add_notice( sprintf(__( 'Width should be any size from %s to %smm and Height should be any size from %s to %smm.', 'norhagewebshop'), $min_width, $max_width, $min_height, $max_height), 'error' );
		}
	}

	return $passed;
}
add_filter( 'woocommerce_add_to_cart_validation', 'norhage_woocommerce_add_to_cart_validation', 10, 3 );

/**
 * Function for `woocommerce_add_cart_item_data` filter-hook.
 * add the addons for variable products to the cart_item_data.
 * 
 * @param array   $cart_item_data Array of other cart item data.
 * @param integer $product_id     ID of the product added to the cart.
 * @param integer $variation_id   Variation ID of the product added to the cart.
 * @param integer $quantity       Quantity of the item added to the cart.
 *
 * @return array
 */
function norhage_add_cart_item_data( $cart_item_data, $product_id, $variation_id, $quantity ){
	if(isset($_POST['addons']) && !empty($_POST['addons'])){
		$cart_item_data['addons'] = $_POST['addons'];
	}
	if(isset($_POST['cutting_variables'])){
		$cart_item_data['cutting_variables'] = $_POST['cutting_variables'];
	}
	return $cart_item_data;
}
add_filter( 'woocommerce_add_cart_item_data', 'norhage_add_cart_item_data', 10, 4 );

/**
 * Display custom cart_item_data in the cart
 */
function norhage_get_item_data( $item_data, $cart_item_data ) {

	if(isset($cart_item_data['cutting_variables'])){
		if(!empty($cart_item_data['variation_id'])){
			$product_variation = new WC_Product_Variation($cart_item_data['variation_id']);
			$regular_price = $product_variation->get_price();
		}else{
			$product = wc_get_product($cart_item_data['product_id']);
			$regular_price = $product->get_price();
		}
		$unit_price = (floatval($cart_item_data['cutting_variables']['width']) / 1000) * (floatval($cart_item_data['cutting_variables']['height']) / 1000) * $regular_price;

		$item_data[] = [
			'key'	=> __('Width', 'norhagewebshop'),
			'value'	=> $cart_item_data['cutting_variables']['width'] . ' mm'
		];
		$item_data[] = [
			'key'	=> __('Height', 'norhagewebshop'),
			'value'	=> $cart_item_data['cutting_variables']['height'] . ' mm'
		];
		$item_data[] = [
			'key'	=> __('Unit price', 'norhagewebshop'),
			'value'	=> wc_price($unit_price)
		];
		$item_data[] = [
			'key'	=> __('Cutting fee', 'norhagewebshop'),
			'value'	=> wc_price($cart_item_data['cutting_variables']['cutting_fee'])
		];
	}

	if(isset($cart_item_data['addons']) && !empty($cart_item_data['addons'])) {
		foreach($cart_item_data['addons'] as $product_id => $quantity){
			if($quantity <= 0){
				// don't show unselected extra's
				continue;
			}
			$product = wc_get_product($product_id);
			$item_data[] = [
				'key'	=> __('Addon', 'norhagewebshop'),
				'value'	=> $quantity . ' x ' . $product->get_name() . ' (' . wc_price($product->get_price()) . ')',
			];
		}
	}
	return $item_data;
}
add_filter( 'woocommerce_get_item_data', 'norhage_get_item_data', 10, 2 );

/**
 *  calculate the price of the items in the cart
 */
function norhage_before_calculate_totals($cart_object){
	foreach ( $cart_object->get_cart() as $hash => $value ) {
		$product = wc_get_product( $value['data']->get_id() );
		$product_price = $product->get_price();
		
		if(isset($value['cutting_variables'])){
			$cutting_fee = floatval($value['cutting_variables']['cutting_fee']);
			$width = floatval($value['cutting_variables']['width']) / 1000; // width and height are in mm's not meter!
			$height = floatval($value['cutting_variables']['height']) / 1000;
			$product_price = $cutting_fee + ($width * $height * $product_price); 

			// Set the appropriate shipping class
			// Items that are cut and that have the special-small shipping class get
			// their actual shipping class assigned based on the cut size.
			$product_shipping_class = $product->get_shipping_class();
			if($product_shipping_class == 'special-small'){
				if($width <= 1.05 && $height <= 1){
					// medium
					$value['data']->set_shipping_class_id(214);	
				}elseif($width <= 1.05 && $height <= 4){
					// large
					$value['data']->set_shipping_class_id(20507);	
				}elseif($width <= 2.1 && $height <= 6){
					// extra-large
					$value['data']->set_shipping_class_id(136);	
				}else{
					// giant
					$value['data']->set_shipping_class_id(20506);	
				}
			}
			
		}

		if(isset($value['addons']) && !empty($value['addons'])){
			$extra_costs = 0;
			foreach($value['addons'] as $product_id => $quantity){
				if($quantity < 1){
					continue;
				}
				$addon_product = wc_get_product($product_id);
				$addon_price = $addon_product->get_price();
				$extra_costs += $addon_price * $quantity;
			}
			$product_price += $extra_costs;
		}

		// IDIOCY:
		// For some reason, ::set_price() uses the default currency (NKK) and then converts the price to the current currency
		// so prices need to be converted default currency before they are used in ::set_price().
		// see woocommerce-multi-currency/includes/functions.php
		if(function_exists('wmc_revert_price')) {
			$value[ 'data' ]->set_price( wmc_revert_price($product_price) );
		}else{
			$value[ 'data' ]->set_price( $product_price );
		}
	}
}
add_action( 'woocommerce_before_calculate_totals', 'norhage_before_calculate_totals', 99 );

/**
 * Add custom meta to order
 */
function norhage_checkout_create_order_line_item( $item, $cart_item_key, $values, $order ) {
	if(isset($values['cutting_variables'])){
		if($variation_id = $item->get_product_id() && $variation_id > 0){
			$variation = new WC_Product_Variation($cart_item_data['variation_id']);
			$regular_price = $variation->get_price();
		}else{
			$product_id = $item->get_product_id();
			$product = wc_get_product($product_id);
			$regular_price = $product->get_price();
		}
		$unit_price = (floatval($values['cutting_variables']['width']) / 1000) * (floatval($values['cutting_variables']['height']) / 1000) * $regular_price;
		$item->add_meta_data(
			'cutting_width',
			$values['cutting_variables']['width'] . ' mm'
		);
		$item->add_meta_data(
			'cutting_height',
			$values['cutting_variables']['height'] . ' mm'
		);
		$item->add_meta_data(
			'unit_price',
			$unit_price
		);
		$item->add_meta_data(
			'cutting_fee',
			$values['cutting_variables']['cutting_fee']
		);
	}

	if( isset( $values['addons'] ) ) {
		foreach($values['addons'] as $product_id => $quantity){
			if($quantity < 1){
				continue;
			}
			$product = wc_get_product( $product_id );
			$item->add_meta_data(
				'addon',
				$quantity . ' x ' . $product->get_name() . ' (' . strip_tags((wc_price($product->get_price()))) . ')',
				false
			);
		}
	}
}
add_action( 'woocommerce_checkout_create_order_line_item', 'norhage_checkout_create_order_line_item', 10, 4 );

// convert price values to display prices
add_filter( 'woocommerce_order_item_get_formatted_meta_data', 'norhage_woocommerce_is_attribute_in_product_name', 10, 1);
function norhage_woocommerce_is_attribute_in_product_name($formatted_meta){
	foreach($formatted_meta as $key => $meta){
		if(in_array($meta->key, ['cutting_fee', 'unit_price']) ){
			$formatted_meta[$key]->display_value = '<p>' . wc_price(strip_tags($formatted_meta[$key]->display_value)) . '</p>';
		}
	}
	return $formatted_meta;
}

// add translations
add_filter( 'woocommerce_order_item_display_meta_key', 'norhage_woocommerce_order_item_display_meta_key', 10, 3);
function norhage_woocommerce_order_item_display_meta_key($display_key, $meta, $order_item ){
	switch($display_key){
		case 'cutting_width':
			return __('Width', 'norhagewebshop');
		case 'cutting_height':
			return __('Height', 'norhagewebshop');
		case 'cutting_fee':
			return __('Cutting fee', 'norhagewebshop');
		case 'unit_price':
			return __('Unit price', 'norhagewebshop');
		case 'addon':
			return __('Addon', 'norhagewebshop');
	}
	return $display_key;
}


/**
 * Add custom cart item data to emails
 */
function norhage_order_item_name( $product_name, $item ) {

	if(isset($item['cutting_variables'])){
		$product_name .= sprintf('<br /> %s: %s', __('Cutting fee', 'norhagewebshop'), wc_price($cart_item_data['cutting_variables']['cutting_fee']));
		$product_name .= sprintf(
			'<br /> %s: %smm <br /> %s: %smm', 
			__('Width', 'norhagewebshop'), 
			$cart_item_data['cutting_variables']['width'],
			__('Height', 'norhagewebshop'), 
			$cart_item_data['cutting_variables']['height']
		);
	}

	if(isset($item['addons']) && !empty($item['addons'])){
		foreach($values['addons'] as $product_id => $quantity){
			if($quantity < 1){
				continue;
			}
			$product = wc_get_product( $product_id );
			$addon_text = sprintf('<br /> %s x %s', $quantity, $product->get_name());
			$product_name .= $addon_text;
		}
	}
	return $product_name;
}

add_filter( 'woocommerce_order_item_name', 'norhage_order_item_name', 10, 2 );




/**
 * Fix shipping tax.
 * For some reason, in the non-default currencies, the shipping tax was not calculated
 * this filter calculates and adds these taxes.
 * 
 * See also: WOOMULTI_CURRENCY_Frontend_Shipping->woocommerce_package_rates() where it goes wrong
 * 
 * */
add_filter( 'woocommerce_package_rates', 'norhage_woocommerce_package_rates', 10, 2 );
function norhage_woocommerce_package_rates($methods, $package){
	foreach($methods as $key => $method){
		if ( !count( $method->get_taxes() ) ) {
			$taxes = WC_Tax::calc_shipping_tax( $method->get_cost(), WC_Tax::get_shipping_tax_rates() );
			$methods[$key]->set_taxes( $taxes );
		}
	}

	return $methods;
}

/**
 * Filters dealing with multi-currency
 * */
add_filter( 'woocommerce_adjust_non_base_location_prices', '__return_false' );

/**
 * Remove the woocommerce template parts
 */
add_action( 'init', 'norhage_remove_wc_template_stuff' );
function norhage_remove_wc_template_stuff() {
    remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);
    remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20);
    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5);
    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10);
    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10);
    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20);
    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);
    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50);
    remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
    remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
    remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
    remove_action( 'woocommerce_shop_loop_header', 'woocommerce_product_taxonomy_archive_header' );
}

/**
 * Allow HTML in term (category, tag) descriptions
 * FIRST remove html filters from output
 */
remove_filter( 'pre_term_description', 'wp_filter_kses' );
if ( ! current_user_can( 'unfiltered_html' ) ) {
	add_filter( 'pre_term_description', 'wp_filter_post_kses' );
}
remove_filter( 'term_description', 'wp_kses_data' );

// add extra css to display quicktags correctly
add_action( 'admin_print_styles', 'categorytinymce_admin_head' );
function categorytinymce_admin_head() { 
	global $current_screen;
	if ( $current_screen->id == 'edit-category' OR 'edit-tag' ):
		echo '<style type="text/css">.quicktags-toolbar input{float:left !important; width:auto !important;}.term-description-wrap{display:none;}</style>';
	endif;
}

// load tinymce
add_filter('product_cat_edit_form_fields', 'add_rich_category_description');
function add_rich_category_description($tag) {
	//$tag_extra_fields = get_option('Category_Description_option');
	?>
		<tr class="form-field">
			<th scope="row" valign="top"><label for="description"><?php _e('Description', 'categorytinymce'); ?></label></th>
			<td>
			<?php  
				$settings = array('wpautop' => true, 'media_buttons' => true, 'quicktags' => true, 'textarea_rows' => '15', 'textarea_name' => 'description' );	
				wp_editor(html_entity_decode($tag->description , ENT_QUOTES, 'UTF-8'), 'Category_Description_option', $settings); 
			?>
				<br />
				<span class="description"><?php _e('The description is shown on the category overview page.', 'norhagewebshop'); ?></span>
			</td>	
		</tr>
	<?php

}

/**
 * Hide the sale flash
 * */
add_filter('woocommerce_sale_flash', '__return_null');


/**
 * gets the tumbnail of a (product) category
 * if no thumbnail exists, it gets the thumbnail of one of the products
 */
function norhage_get_taxo_thumbnail($cat){
	$thumbnail_id = get_term_meta( $cat->term_id, 'thumbnail_id', true );
	if(!$thumbnail_id){
        $posts = get_posts([
        	'post_type'		=> ['product', 'post'],
        	'numberposts'	=> 1,
        	'tax_query'		=> [
        		[
	        		'taxonomy'		=> $cat->taxonomy,
	        		'field'			=> 'term_id',
	        		'terms'			=> $cat->term_id
	        	]
        	]
        ]);
        if(count($posts) > 0){
        	$thumbnail_id = get_post_thumbnail_id($posts[0]->ID, [420, 420]);
        }
	}
	return $thumbnail_id;
}


/**
 * Sync currency values on product->save();
 **/
//add_action('save_post_product', 'norhage_wpmc_sync_on_product_save', 10, 3);
if(function_exists('pll_get_post_translations')){
	add_action( 'added_post_meta', 'norhage_wpmc_sync_on_product_save', 10, 4 );
	add_action( 'updated_post_meta', 'norhage_wpmc_sync_on_product_save', 10, 4 );
	function norhage_wpmc_sync_on_product_save( $meta_id, $post_id, $meta_key, $meta_value ) {
		if ( in_array($meta_key, ['_regular_price_wmcp', '_sale_price_wmcp'] )) {
			remove_action( 'added_post_meta', 'norhage_wpmc_sync_on_product_save');
			remove_action( 'updated_post_meta', 'norhage_wpmc_sync_on_product_save');
			$translations = pll_get_post_translations( $post_id );
			foreach($translations as $lang => $id){
		    	if($id == $post_id){
		    		continue;
		    	}
	    		$translation_product = wc_get_product( $id );
	    		if($translation_product){
		    		$translation_product->update_meta_data($meta_key, $meta_value );
					$translation_product->save_meta_data();
				}
	    	}
			add_action( 'added_post_meta', 'norhage_wpmc_sync_on_product_save', 10, 4 );
			add_action( 'updated_post_meta', 'norhage_wpmc_sync_on_product_save', 10, 4 );
		}
	}
}

/**
 * Sync prices during import
 * */
add_action( 'woocommerce_product_import_inserted_product_object', 'norhage_woocommerce_product_import_inserted_product_object', 10, 2);
function norhage_woocommerce_product_import_inserted_product_object($object, $data ){
	$post_id = $object->get_id();
	$translations = pll_get_post_translations( $post_id );

	foreach($translations as $lang => $id){
		
		if($id == $post_id){
			continue;
		}

		$translation_product = wc_get_product( $id );
		$needs_saving = false;
		
		if(!empty($data['regular_price'])){
			$translation_product->set_regular_price($data['regular_price']);
			$translation_product->set_price($data['regular_price']);
			$needs_saving = true;
		}
		
		if(!empty($data['sale_price'])){
			$translation_product->set_sale_price($data['sale_price']);
			$translation_product->set_price($data['sale_price']);
			$needs_saving = true;
		}
		
		if($needs_saving){
			$translation_product->save();
		}
	}
}





/**
 * !! SECURITY !!
 * Disable /users rest routes
 */
add_filter('rest_endpoints', function( $endpoints ) {
    if ( isset( $endpoints['/wp/v2/users'] ) ) {
        unset( $endpoints['/wp/v2/users'] );
    }
    if ( isset( $endpoints['/wp/v2/users/(?P<id>[\d]+)'] ) ) {
        unset( $endpoints['/wp/v2/users/(?P<id>[\d]+)'] );
    }
    return $endpoints;
});



/**
 * Change the default state and country on the checkout page
 */
add_filter( 'default_checkout_billing_country', 'change_default_checkout_country' );
add_filter( 'woocommerce_customer_default_location', 'norhage_customer_default_location', 9999, 1 );
add_filter('woocommerce_countries_allowed_countries', 'norhage_countries_allowed_countries', 10, 1);

function norhage_customer_default_location($default){
	if(!function_exists('pll_current_language')){
		return 'NO';
	}
	switch(pll_current_language()){
		case 'sv':
			return 'SE';
			break;
		case 'fi':
			return 'FI';
			break;
		case 'de':
			return 'DE';
			break;
		case 'da':
			return 'DK';
			break;
		case 'nb':
		default:
			return 'NO';
	}
}
//add_filter( 'default_checkout_billing_state', 'change_default_checkout_state' );

function change_default_checkout_country() {
	if(!function_exists('pll_current_language')){
		return 'NO';
	}
	switch(pll_current_language()){
		case 'sv':
			return 'SE';
			break;
		case 'fi':
			return 'FI';
			break;
		case 'de':
			return 'DE';
			break;
		case 'da':
			return 'DK';
			break;
		case 'nb':
		default:
			return 'NO';
	}
}

function norhage_countries_allowed_countries($countries){
	$country = change_default_checkout_country();
	return [$country => $countries[$country]];
}


add_filter( 'wpseo_breadcrumb_single_link' ,'norhage_remove_breadcrumb_link', 10 ,2);
function norhage_remove_breadcrumb_link( $link_output , $link ){
	$text_to_remove = ['shop', 'butikk', 'butik', 'kauppa'];

	if( in_array(strtolower($link['text']), $text_to_remove) ) {
		$link_output = '';
	}

	return $link_output;
}


/**
 * Remove annoying admin notices from brocket
 * */
remove_action('admin_notices', array('berocket_admin_notices', 'display_admin_notice'));
remove_action('admin_notices', array('berocket_admin_notices_rate_stars', 'admin_notices'));
remove_action('admin_notices', array( 'VillaTheme_Support_Pro', 'notice' ) );
remove_action('admin_notices', array( 'VillaTheme_Support_Pro', 'form_ads' ) );


/**
 * EMAIL CUSTOMIZATIONS
 * */
// - fix email domain
add_filter( 'woocommerce_email_from_name', 'norhage_woocommerce_email_from_name', 10, 3);
function norhage_woocommerce_email_from_name($default_from_name, $email, $from_name){
	if(!function_exists('pll_get_post_language')){
		return $default_from_name;
	}

	$lang = pll_get_post_language($email->object->get_id());
	switch($lang){
		case 'sv':
			return 'Norhage.se';
		case 'fi':
			return 'Norhage.fi';
		case 'de':
			return 'Norhage.de';
		case 'da':
			return 'Norhage.dk';
		case 'nb':
		default:
			return 'Norhage.no';
	}
}

// - fix email from address
add_filter( 'woocommerce_email_from_address', 'norhage_woocommerce_email_from_address', 10, 3);
function norhage_woocommerce_email_from_address($default_from_address, $email, $from_address ){
	if(!function_exists('pll_get_post_language')){
		return $default_from_address;
	}

	$lang = pll_get_post_language($email->object->get_id());
	switch($lang){
		case 'sv':
			return 'info@norhage.se';
		case 'fi':
			return 'info@norhage.fi';
		case 'de':
			return 'info@norhage.de';
		case 'da':
			return 'info@norhage.dk';
		case 'nb':
		default:
			return 'info@norhage.no';
	}
}

// fix email recipients of order notifications
add_filter( 'woocommerce_email_recipient_new_order', 'norhage_woocommerce_email_recipient', 10, 3);
add_filter( 'woocommerce_email_recipient_cancelled_order', 'norhage_woocommerce_email_recipient', 10, 3);
add_filter( 'woocommerce_email_recipient_failed_order', 'norhage_woocommerce_email_recipient', 10, 3);
function norhage_woocommerce_email_recipient($recipient, $object, $email ){
	if(!function_exists('pll_get_post_language')){
		return $recipient;
	}
	if(!isset($email->object) || !method_exists($email->object, 'get_id')){
		return $recipient;
	}
	
	$lang = pll_get_post_language($email->object->get_id());
	switch($lang){
		case 'sv':
			return 'info@norhage.se, bramdeleeuw@gmail.com';
		case 'fi':
			return 'info@norhage.fi';
		case 'de':
			return 'info@norhage.de, bramdeleeuw@gmail.com';
		case 'da':
			return 'info@norhage.dk';
		case 'nb':
		default:
			return 'sales@norhage.no, bramdeleeuw@gmail.com';
	}
}


add_filter('wt_order_number_sequence_prefix', 'norhage_wt_order_number_sequence_prefix', 10, 2); 
function norhage_wt_order_number_sequence_prefix($prefix,$order_id){
	if(!function_exists('pll_get_post_language')){
		return $prefix;
	}

	$lang = pll_get_post_language($order_id);
	switch($lang){
		case 'sv':
			return 'SE-';
		case 'fi':
			return 'FI-';
		case 'de':
			return 'DE-';
		case 'da':
			return 'DK-';
		case 'nb':
		default:
			return 'NO-';
	}
	
	return 'XX-';
}


/**
 * remove ordering by rating from catalogue pages
 * */
add_filter('woocommerce_catalog_orderby', 'norhage_woocommerce_catalog_orderby', 10, 1);
function norhage_woocommerce_catalog_orderby($opts){
	unset($opts['rating']);
	return $opts;
}

add_filter('BeRocket_AAPF_template_full_element_content', 'norhage_BeRocket_AAPF_template_full_element_content', 10, 2);
function norhage_BeRocket_AAPF_template_full_element_content($template_content, $berocket_query_var_title){
	$template_content['template']['content']['header']['content']['title']['content']['title'] = __('Selected filters', 'norhagewebshop');
	return $template_content;
}

/**
 * translate strings from filter plugin
 * */
add_filter('aapf_localize_widget_script', 'norhage_aapf_localize_widget_script', 10, 1);
function norhage_aapf_localize_widget_script($strings){
	$strings['translate']['show_value'] = __('Show value(s)', 'norhagewebshop');
	$strings['translate']['hide_value'] = __('Hide value(s)', 'norhagewebshop');
	$strings['translate']['unselect_all'] = __('Unselect all', 'norhagewebshop');
	$strings['translate']['nothing_selected'] = __('Nothing is selected', 'norhagewebshop');
	$strings['translate']['products'] = __('products', 'norhagewebshop');
	return $strings;
}

/**
 * Disable/enable payment methods based on country
 * */
add_filter('woocommerce_available_payment_gateways', 'norhage_woocommerce_available_payment_gateways', 10, 1);
function norhage_woocommerce_available_payment_gateways( $available_gateways ) {
    // Not in backend (admin)
    if( is_admin() || !function_exists('pll_current_language') ) {
        return $available_gateways;
    }

	switch(pll_current_language()){
		case 'sv':
			unset($available_gateways['kco']);
			break;
		case 'fi':
			unset($available_gateways['kco']);
			break;
		case 'de':
			unset($available_gateways['svea_checkout']);
			break;
		case 'da':
			unset($available_gateways['kco']);
			break;
		case 'nb':
		default:
			unset($available_gateways['kco']);
	}

    return $available_gateways;
}

/**
 * fix SVEA bug where the wrong domain is used for webhook uri
 * */
add_filter( 'woocommerce_sco_create_order', 'norhage_svea_change_push_uri', 20, 1 );
function norhage_svea_change_push_uri($data){
	if ( ! empty( $data['MerchantSettings'] ) ) {
		$home_url = 'https://norhage.no/';
		$data['MerchantSettings']['PushUri'] = str_replace( $home_url, pll_home_url(), $data['MerchantSettings']['PushUri'] );
		$data['MerchantSettings']['CheckoutValidationCallBackUri'] = str_replace( $home_url, pll_home_url(), $data['MerchantSettings']['CheckoutValidationCallBackUri'] );
		$data['MerchantSettings']['WebhookUri'] = str_replace( $home_url, pll_home_url(), $data['MerchantSettings']['WebhookUri'] );
	}
	return $data;
}

/**
 * fix spam problems where envelope-from is the account on directadmin
 * */
add_action( 'phpmailer_init', 'norhage_phpmailer_init' );
function norhage_phpmailer_init($phpmailer){
	// https://phpmailer.github.io/PHPMailer/classes/PHPMailer-PHPMailer-PHPMailer.html#method_setFrom
	$phpmailer->SetFrom($phpmailer->From, $phpmailer->FromName, TRUE);
	return;
}

/** 
 * Increase Woocommerce Variation Threshold 
 * see: https://stackoverflow.com/questions/39343244/sorry-no-products-matched-your-selection-please-choose-a-different-combination
 * */
function norhage_ajax_variation_threshold( $threshold, $product ){
	$threshold = '1111';
	return  $threshold;
}
add_filter( 'woocommerce_ajax_variation_threshold','norhage_ajax_variation_threshold', 10, 2 );


/**
 * Fix issue with woo multi currency
 * WMC formats prices wrongly in the flat fee shipping costs in input fields.
 * These wrongly formatted prices are then saved which causes problems when calculating shipping costs.
 * This function removes the formatting.
 */
function norhage_shipping_flat_rate_instance_settings_values($instance_settings){
	foreach($instance_settings as $key => $setting){
		$instance_settings[$key] = str_replace('.', '', $setting);
	}
	return $instance_settings;
}
add_filter( 'woocommerce_shipping_flat_rate_instance_settings_values', 'norhage_shipping_flat_rate_instance_settings_values', 20, 1 );



/*
// CORS HOT FIX BY NB:
add_filter( 'script_loader_src', 'wpse47206_src' );
add_filter( 'style_loader_src', 'wpse47206_src' );
function wpse47206_src( $url )
{
    if(!isset($_SERVER['SERVER_NAME'])) return $url;
    if (strpos($_SERVER['SERVER_NAME'],'norhagewebshop.no') !== false) {
        return str_replace('norhagewebshop.com', 'norhagewebshop.no', $url);
    }
    return $url;
}

function check_for_src($attr, $attachment){
    if(!isset($_SERVER['SERVER_NAME'])) return $attr;
    if (strpos($_SERVER['SERVER_NAME'],'norhagewebshop.no') !== false) {
        $find_and_replace = str_replace("norhagewebshop.com","norhagewebshop.no", $attr["src"]);
        $attr["src"] = $find_and_replace;
        $attr["srcset"] = $find_and_replace;
        return $attr;
    }
    return $attr;
}
add_filter("wp_get_attachment_image_attributes","check_for_src",10,2);


function modify_adminy_url_for_ajax( $url, $path, $blog_id ) {
    if(!isset($_SERVER['SERVER_NAME'])) return $url;
    if ( "admin-ajax.php" == $path ) {
        if (strpos($_SERVER['SERVER_NAME'],'norhagewebshop.no') !== false) {
            return "https://norhagewebshop.no/wp-admin/admin-ajax.php";
        }
        return $url;
    }
    return $url;
}
add_filter("admin_url", "modify_adminy_url_for_ajax", 10, 3 );

add_filter('allowed_http_origins', 'add_allowed_origins');
function add_allowed_origins($origins) {
    $origins[] = 'https://norhagewebshop.no';
    $origins[] = 'https://norhagewebshop.com';
    return $origins;
}

*/
