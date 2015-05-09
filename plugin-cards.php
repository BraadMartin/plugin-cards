<?php
/**
 * Plugin Cards
 *
 * @package 			Plugin_Cards
 * @author				Braad Martin <wordpress@braadmartin.com>
 * @license 			GPL-2.0+
 *
 * @wordpress-plugin
 * Plugin Name: 		Plugin Cards
 * Plugin URI: 			https://wordpress.org/plugins/plugin-cards/
 * Description: 		Display plugin cards that match those introduced in WordPress 4.0. Uses the wordpress.org API and supports custom queries.
 * Version: 			1.0.0
 * Author:				Braad Martin
 * Author URI: 			http://braadmartin.com
 * License: 			GPL-2.0+
 * License URI: 		http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: 		plugin-cards
 * Domain Path: 		/languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Include the core functions that we need to work with the wordpress.org API on the front end.
 */
if ( ! is_admin() ) {
	if ( ! function_exists( 'plugins_api' ) ) {
		require_once( ABSPATH . 'wp-admin/includes/plugin-install.php' );
	}
}

add_action( 'init', 'pc_plugin_cards_load_translations' );
/**
 * Load translations.
 */
function pc_plugin_cards_load_translations() {

	load_plugin_textdomain( 'plugin-cards', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}

add_action( 'init', 'pc_plugin_cards_enqueue_scripts' );
/**
 * Register our CSS.
 */
function pc_plugin_cards_enqueue_scripts() {

	wp_register_style( 'open-sans-google-font', '//fonts.googleapis.com/css?family=Open+Sans:400,600' );
	wp_register_style( 'plugin-cards', plugins_url( '/css/plugin-cards.css', __FILE__ ) );
}

add_shortcode( 'plugin_cards', 'pc_plugin_cards_shortcode' );
/**
 * Shortcode for rendering the plugin cards.
 */
function pc_plugin_cards_shortcode( $atts ) {

	$atts = shortcode_atts( array(
			'slug'		=> '',
			'author'	=> '',
			'tag'		=> '',
			'browse'	=> '',
			'search'	=> '',
		), $atts );

	$slug = $atts['slug'];
	$author = $atts['author'];
	$tag = $atts['tag'];
	$browse = $atts['browse'];
	$search = $atts['search'];

	// Set up query fields
	$fields = array(
		'banners' => true,
		'icons' => true,
		'reviews' => false,
		'rating' => true,
		'num_ratings' => true,
		'downloaded' => true,
		'active_installs' => true,
		'short_description' => true,
		'sections' => false,
		'downloadlink' => true,
		'last_updated' => true,
		'homepage' => true,
	);

	if ( $slug ) {

		$plugin_info = plugins_api(
			'plugin_information',
			array(
				'slug' => $slug,
				'fields' => $fields,
			)
		);

	} elseif ( $author ) {

		$plugin_info = plugins_api(
			'query_plugins',
			array(
				'author' => $author,
				'fields' => $fields,
			)
		);

	} elseif ( $tag ) {

		$plugin_info = plugins_api(
			'query_plugins',
			array(
				'tag' => $tag,
				'fields' => $fields,
			)
		);

	} elseif ( $browse ) {

		$plugin_info = plugins_api(
			'query_plugins',
			array(
				'browse' => $browse,
				'fields' => $fields,
			)
		);

	} elseif ( $search ) {

		$plugin_info = plugins_api(
			'query_plugins',
			array(
				'search' => $search,
				'fields' => $fields,
			)
		);

	}

	// Default $output.
	$output = '';

	// Confirm the call to plugins_api worked.
	if ( is_object( $plugin_info ) ) {

		print '<pre>';
		print_r( $plugin_info );
		print '</pre>';

		$output .= '<div class="plugin-cards">';

		// Prioritize querying, first by slug, then author, then tag, then browse, then search
		if ( $slug ) {

			// We are querying by slug, so we only have one plugin to display.
			$output .= pc_render_plugin_card( $plugin_info, 'slug' );

		} else {

			// We are querying by author, tag, browse, or search, so likely have multiple results and need to loop.
			foreach( $plugin_info->plugins as $plugin ) {

				$output .= pc_render_plugin_card( $plugin );
			}

		} 

		$output .= '</div>';

	}

	return $output;
}

/**
 * Render a plugin card.
 */
function pc_render_plugin_card( $plugin ) {

	// Quick sanity check
	if ( is_object( $plugin ) ) {

		// Enqueue our CSS
		wp_enqueue_style( 'open-sans-google-font' );
		wp_enqueue_style( 'plugin-cards' );

		// Sometimes the Plugin URI hasn't been set, so let's fallback to building it manually
		$plugin_url = esc_url( $plugin->homepage );
		if ( ! $plugin_url ) {
			$plugin_url = 'https://wordpress.org/plugins/' . esc_attr( $plugin->slug ) . '/';
		}

		ob_start();

		?>
		<div class="plugin-card plugin-card-<?php echo esc_attr( $plugin->slug ) ?>">
			<div class="plugin-card-top">
				<?php 

				// Allow the plugin icon output to be overridden.
				$plugin_icon = apply_filters( 'plugin_cards_plugin_icon', '', $plugin );
	
				// Use the override if it's there, otherwise output the standard icon.
				if ( $plugin_icon ) {
					echo wp_kses_post( $plugin_icon );
				} else {
					?>
					<a href="<?php echo esc_url( $plugin_url ); ?>" class="plugin-icon">
						<?php
						$plugin_icons = $plugin->icons;
						if ( ! empty( $plugin_icons['svg'] ) ) {
							
							// We have an SVG
							$img_src = $plugin_icons['svg'];
							echo '<img src="' . $img_src . '" />';
						
						} elseif ( ! empty( $plugin_icons['2x'] ) ) {
							
							// We have a Retina icon
							$img_src = $plugin_icons['2x'];
							echo '<img src="' . $img_src . '" />';
						
						} elseif ( ! empty( $plugin_icons['1x'] ) ) {
							
							// We have a standard icon
							$img_src = $plugin_icons['1x'];
							echo '<img src="' . $img_src . '" />';
						
						} elseif ( ! empty( $plugin_icons['default'] ) ) {

							// We have a default
							$img_src = $plugin_icons['default'];
							echo '<img src="' . $img_src . '" />';
						}
						?>
					</a>
				<?php } ?>
				<div class="plugin-name">
					<h4>
						<a href="<?php echo esc_url( $plugin_url ); ?>" target="_blank"><?php echo esc_html( apply_filters( 'plugin_cards_plugin_name', $plugin->name, $plugin ) ); ?></a>
					</h4>
				</div>
				<?php /* Turning this off for now.
				<div class="action-links">
					<ul class="plugin-action-buttons">
						<li>
							<a href="<?php echo esc_url( $plugin->download_link ); ?>" class="button"><?php _e( 'Download', 'plugin-cards' ); ?></a>
						</li>
					</ul>
				</div> */ ?>
				<div class="plugin-description">
					<p>
						<?php echo wp_kses_post( apply_filters( 'plugin_cards_short_description', $plugin->short_description, $plugin ) ); ?>
					</p>
					<p class="authors">
						<cite>By <?php echo wp_kses_post( apply_filters( 'plugin_cards_plugin_author', $plugin->author, $plugin ) ); ?></cite>
					</p>
				</div>
			</div>
			<div class="plugin-card-bottom">
				<div class="column-rating">
					<div class="star-rating">
						<?php
						$rating = $plugin->rating;

						// Star 1
						if ( $rating >= 20 ) {
							echo '<span class="star fa fa-star"></span>';
						} elseif ( $rating >= 10 ) {
							echo '<span class="star fa fa-star-half-full"></span>';
						} else {
							echo '<span class="star fa fa-star-o"></span>';
						}

						// Star 2
						if ( $rating >= 40 ) {
							echo '<span class="star fa fa-star"></span>';
						} elseif ( $rating >= 30 ) {
							echo '<span class="star fa fa-star-half-full"></span>';
						} else {
							echo '<span class="star fa fa-star-o"></span>';
						}

						// Star 3
						if ( $rating >= 60 ) {
							echo '<span class="star fa fa-star"></span>';
						} elseif ( $rating >= 50 ) {
							echo '<span class="star fa fa-star-half-full"></span>';
						} else {
							echo '<span class="star fa fa-star-o"></span>';
						}

						// Star 4
						if ( $rating >= 80 ) {
							echo '<span class="star fa fa-star"></span>';
						} elseif ( $rating >= 70 ) {
							echo '<span class="star fa fa-star-half-full"></span>';
						} else {
							echo '<span class="star fa fa-star-o"></span>';
						}

						// Star 5
						if ( $rating >= 98 ) {
							echo '<span class="star fa fa-star"></span>';
						} elseif ( $rating >= 90 ) {
							echo '<span class="star fa fa-star-half-full"></span>';
						} else {
							echo '<span class="star fa fa-star-o"></span>';
						}
						?>
					</div>
					<span class="num-ratings">(<?php echo number_format_i18n( $plugin->num_ratings ); ?>)</span>
				</div>
				<div class="column-updated">
					<strong><?php _e( 'Last Updated', 'plugin-cards' ); ?>:</strong> <span>
						<?php printf( __( '%s ago', 'plugin-cards' ), human_time_diff( strtotime( $plugin->last_updated ) ) ); ?>
					</span>
				</div>
				<div class="column-downloaded">
					<?php
					if ( $plugin->active_installs >= 1000000 ) {
						$active_installs_text = _x( '1+ Million', 'Active plugin installs', 'plugin-cards' );
					} else {
						$active_installs_text = number_format_i18n( $plugin->active_installs ) . '+';
					}
					printf( __( '%s Active Installs', 'plugin-cards' ), $active_installs_text );
					?>
				</div>
				<div class="column-compatibility">
					<?php
					if ( ! empty( $plugin->tested ) ) {
						echo '<span class="compatibility-compatible"><strong>' . __( 'Compatible up to', 'plugin-cards' ) . ':</strong> ' . $plugin->tested . '</span>';
					}
					?>
				</div>
			</div>

		</div>
		<?php

		return ob_get_clean();

	}

	// Looks like $plugin wasn't an object...
	return false;

}

add_action( 'init', 'pc_plugin_cards_load_bfa' );
/**
 * Use the Better Font Awesome Library (for the star ratings).
 */
require_once ( dirname( __FILE__ ) . '/lib/better-font-awesome-library/better-font-awesome-library.php' );
function pc_plugin_cards_load_bfa() {

	// Only if we're on the front end.
	if ( ! is_admin() ) {

	    // Initialize the Better Font Awesome Library.
	    Better_Font_Awesome_Library::get_instance();
	}
}
