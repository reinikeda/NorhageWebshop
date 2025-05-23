<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-single-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woo.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.6.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( post_password_required() ) {
	echo get_the_password_form(); // WPCS: XSS ok.
	return;
}

$max_shown = 4;

?>
<div id="product-<?php the_ID(); ?>" <?php wc_product_class( '', $product ); ?>>

	<div class="entry-content">
		<div class="productHeaderBlock norhage-block alignfull">
			<div class="text-col">
				<?php
					if ( function_exists('yoast_breadcrumb') ) {
						yoast_breadcrumb( '<p id="breadcrumbs">','</p>' );
					}
				?>
				<?php
					/**
					 * Hook: woocommerce_before_single_product.
					 *
					 * @hooked woocommerce_output_all_notices - 10
					 */
					do_action( 'woocommerce_before_single_product' );
				?>
				<h1><?php the_title(); ?></h1>
				
				<?php $product->is_type('simple') ? FALSE : wc_get_template( 'single-product/price.php' ); ?>

				<div class="short-desc"><?php echo wpautop($product->get_short_description()); ?></div>

				<?php
					$cutting_fee = get_field('cutting_fee');
					$has_cutting_fee = ($cutting_fee && $cutting_fee > 0)? true : false;
					$extras = get_field('product_extras');
					$has_extras = $extras ? true : false;
				?>

				<?php if($product->is_type('simple') && !$has_cutting_fee && !$has_extras && $product->get_price()): ?>
					<div class="summary entry-summary alignfull">
						<?php do_action( 'woocommerce_single_product_summary' ); ?>
					</div>
				<?php endif; ?>

			</div>
			<div class="image-col">
				<?php
					$images = $product->get_gallery_image_ids();

					if(!in_array(get_post_thumbnail_id(), $images)){
						array_unshift($images, get_post_thumbnail_id());
					}
					$counter = 0;
					foreach($images as $image_id): ?>
						<?php $counter++; ?>
						<figure class="header-image">
							<?php 
								echo wp_get_attachment_image( $image_id, 'full', '', array( 'class' => 'header-image__img' ) ); 
							?>
							<?php if($counter == $max_shown && count($images) > $max_shown): ?>
								<span class="click-for-more-images">+<?php echo count($images) - $max_shown; ?></span>
							<?php endif; ?>
						</figure>
					<?php endforeach;?>
				<?php
				/**
				 * Hook: woocommerce_before_single_product_summary.
				 *
				 * @hooked woocommerce_show_product_sale_flash - 10
				 * @hooked woocommerce_show_product_images - 20
				 */
				do_action( 'woocommerce_before_single_product_summary' );
				?>
			</div>
		</div>

		<?php if(!$product->is_type('simple') || $has_cutting_fee || $has_extras): ?>
			<div class="summary entry-summary alignfull" id="product-config">
				<?php
				/**
				 * Hook: woocommerce_single_product_summary.
				 *
				 * @hooked woocommerce_template_single_title - 5
				 * @hooked woocommerce_template_single_rating - 10
				 * @hooked woocommerce_template_single_price - 10
				 * @hooked woocommerce_template_single_excerpt - 20
				 * @hooked woocommerce_template_single_add_to_cart - 30
				 * @hooked woocommerce_template_single_meta - 40
				 * @hooked woocommerce_template_single_sharing - 50
				 * @hooked WC_Structured_Data::generate_product_data() - 60
				 */
				// the price is already show elsewhere
				remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10);
				do_action( 'woocommerce_single_product_summary' );
				?>
			</div>
		<?php endif; ?>

		<div class="product-description">
			<?php the_content(); ?>
		</div>

		<?php
			$extra_content = get_field('extra_content');
			if( have_rows('extra_content')):
				while ( have_rows('extra_content') ) : the_row();

					if(get_row_layout() == 'text_image_block'):
						get_template_part('blocks/text-image-block/textImageBlock');
					endif;

					if(get_row_layout() == 'related_products'):
						get_template_part('blocks/products-block/productsBlock');
					endif;

					if(get_row_layout() == 'projects_slider'):
						get_template_part('blocks/projects-block/projectsBlock');
					endif;

					if(get_row_layout() == 'cta'):
						get_template_part('blocks/cta-block/ctaBlock');
					endif;

				endwhile;
			endif;
		?>

		<?php
		/**
		 * Hook: woocommerce_after_single_product_summary.
		 *
		 * @hooked woocommerce_output_product_data_tabs - 10
		 * @hooked woocommerce_upsell_display - 15
		 * @hooked woocommerce_output_related_products - 20
		 */
		do_action( 'woocommerce_after_single_product_summary' );
		?>
	</div>
</div><!-- #product-<?php the_ID(); ?> -->

<?php do_action( 'woocommerce_after_single_product' ); ?>
