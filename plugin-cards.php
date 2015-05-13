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
if ( ! defined( 'ABSPATH' ) ) {
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

add_action( 'wp_enqueue_scripts', 'pc_plugin_cards_enqueue_scripts' );
/**
 * Register our CSS and include Dashicons as a dependency.
 */
function pc_plugin_cards_enqueue_scripts() {

	// Only on the front end.
	if ( ! is_admin() ) {
		wp_register_style( 'open-sans-google-font', '//fonts.googleapis.com/css?family=Open+Sans:400,600' );
		wp_register_style( 'plugin-cards', plugins_url( '/css/plugin-cards.css', __FILE__ ), array( 'dashicons' ), '1.0.0' );
	}
}

add_shortcode( 'plugin_cards', 'pc_plugin_cards_shortcode' );
/**
 * Main [plugin_cards] shortcode function.
 */
function pc_plugin_cards_shortcode( $atts ) {

	$atts = shortcode_atts( array(
			'max_results'	=> 50,
			'slug'			=> false,
			'author'		=> false,
			'tag'			=> false,
			'browse'		=> false,
			'user'			=> false,
			'search'		=> false,
		), $atts );

	$per_page = (int)$atts['max_results'];
	$slug = esc_attr( $atts['slug'] );
	$author = esc_attr( $atts['author'] );
	$tag = esc_attr( $atts['tag'] );
	$browse = esc_attr( $atts['browse'] );
	$user = esc_attr( $atts['user'] );
	$search = esc_attr( $atts['search'] );

	// Set up our query fields.
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

	// Set how long to cache results.
	$expiration = 15 * MINUTE_IN_SECONDS;

	// Allow expiration to be filtered.
	$expiration = apply_filters( 'plugin_cards_cache_expiration', $expiration, $atts );

	// Allow the use of custom query args.
	$custom_query_args = apply_filters( 'plugin_cards_api_query_args', false, $atts, $fields );

	/**
	 * Do query using passed in params.
	 */
	if ( $custom_query_args ) {

		$plugin_info = plugins_api(
			'query_plugins',
			$custom_query_args
		);

	} elseif ( $slug ) {

		// Look in the cache.
		$plugin_info = get_transient( 'plugin_cards_{$slug}' );

		// If it's not in the cache or it's expired, do it live
		// and store it in the cache for next time.
		if ( ! $plugin_info ) {
			$plugin_info = plugins_api(
				'plugin_information',
				array(
					'slug' => $slug,
					'fields' => $fields,
				)
			);
			if ( is_object( $plugin_info ) && ! is_wp_error( $plugin_info ) ) {
				set_transient( 'plugin_cards_{$slug}', $plugin_info, $expiration );
			}
		}

	} elseif ( $author ) {

		$plugin_info = get_transient( 'plugin_cards_{$author}' );

		if ( ! $plugin_info ) {
			$plugin_info = plugins_api(
				'query_plugins',
				array(
					'per_page' => $per_page,
					'author' => $author,
					'fields' => $fields,
				)
			);
			if ( is_object( $plugin_info ) && ! is_wp_error( $plugin_info ) ) {
				set_transient( 'plugin_cards_{$author}', $plugin_info, $expiration );
			}
		}

	} elseif ( $tag ) {

		$plugin_info = get_transient( 'plugin_cards_{$tag}' );

		if ( ! $plugin_info ) {
			$plugin_info = plugins_api(
				'query_plugins',
				array(
					'per_page' => $per_page,
					'tag' => $tag,
					'fields' => $fields,
				)
			);
			if ( is_object( $plugin_info ) && ! is_wp_error( $plugin_info ) ) {
				set_transient( 'plugin_cards_{$tag}', $plugin_info, $expiration );
			}
		}

	} elseif ( $user ) {

		$plugin_info = get_transient( 'plugin_cards_{$user}' );

		if ( ! $plugin_info ) {
			$plugin_info = plugins_api(
				'query_plugins',
				array(
					'per_page' => $per_page,
					'user'	 => $user,
					'fields' => $fields,
				)
			);
			if ( is_object( $plugin_info ) && ! is_wp_error( $plugin_info ) ) {
				set_transient( 'plugin_cards_{$user}', $plugin_info, $expiration );
			}
		}

	} elseif ( $browse ) {

		$plugin_info = get_transient( 'plugin_cards_{$browse}' );

		if ( ! $plugin_info ) {
			$plugin_info = plugins_api(
				'query_plugins',
				array(
					'per_page' => $per_page,
					'browse' => $browse,
					'fields' => $fields,
				)
			);
			if ( is_object( $plugin_info ) && ! is_wp_error( $plugin_info ) ) {
				set_transient( 'plugin_cards_{$browse}', $plugin_info, $expiration );
			}
		}

	} elseif ( $search ) {

		$plugin_info = get_transient( 'plugin_cards_{$search}' );

		if ( ! $plugin_info ) {
			$plugin_info = plugins_api(
				'query_plugins',
				array(
					'per_page' => $per_page,
					'search' => $search,
					'fields' => $fields,
				)
			);
			if ( is_object( $plugin_info ) && ! is_wp_error( $plugin_info ) ) {
				set_transient( 'plugin_cards_{$search}', $plugin_info, $expiration );
			}
		}
	}

	// Default $output.
	$output = '';

	// Confirm the call to plugins_api worked.
	if ( is_object( $plugin_info ) && ! is_wp_error( $plugin_info ) ) {

		/* Debug Info */
		//print '<pre>';
		//print_r( $plugin_info );
		//print '</pre>';

		// Check whether we have a single result or multiple results.
		if ( isset( $plugin_info->plugins ) ) {

			// We have multiple results.
			$output .= '<div class="plugin-cards multiple-plugins">';

			foreach( $plugin_info->plugins as $plugin ) {

				$output .= pc_render_plugin_card( $plugin );
			}

		} else {

			// We have a single result.
			$output .= '<div class="plugin-cards single-plugin">';

			$output .= pc_render_plugin_card( $plugin_info );

		}

		$output .= '</div>';

	}

	return $output;
}

/**
 * Render a plugin card.
 */
function pc_render_plugin_card( $plugin ) {

	// Quick sanity check.
	if ( is_object( $plugin ) ) {

		// Enqueue our CSS.
		wp_enqueue_style( 'open-sans-google-font' );
		wp_enqueue_style( 'plugin-cards' );

		// Sometimes the Plugin URI hasn't been set, so let's fallback to building it manually.
		$plugin_url = esc_url( $plugin->homepage );
		if ( ! $plugin_url ) {
			$plugin_url = 'https://wordpress.org/plugins/' . esc_attr( $plugin->slug ) . '/';
		}

		// And allow it to be filtered.
		$plugin_url = apply_filters( 'plugin_cards_plugin_url', $plugin_url, $plugin );

		ob_start();

		?>
		<div class="plugin-card plugin-card-<?php echo esc_attr( $plugin->slug ) ?>">
			<div class="plugin-card-top">
				<?php

				// Allow this whole section to be overridden.
				$plugin_icon = apply_filters( 'plugin_cards_plugin_icon', '', $plugin, $plugin_url );

				// Use the override if it's there, otherwise output the standard icon.
				if ( $plugin_icon ) {
					echo wp_kses_post( $plugin_icon );
				} else {
					?>
					<a href="<?php echo esc_url( $plugin_url ); ?>" class="plugin-icon">
						<?php
						$plugin_icons = $plugin->icons;
						if ( ! empty( $plugin_icons['svg'] ) ) {

							// We have an SVG.
							// TODO: Figure out how to escape the SVG.
							$img_src = $plugin_icons['svg'];
							echo '<img src="' . $img_src . '" />';

						} elseif ( ! empty( $plugin_icons['2x'] ) ) {

							// We have a Retina icon.
							$img_src = $plugin_icons['2x'];
							echo '<img src="' . esc_url( $img_src ) . '" />';

						} elseif ( ! empty( $plugin_icons['1x'] ) ) {

							// We have a standard icon.
							$img_src = $plugin_icons['1x'];
							echo '<img src="' . esc_url( $img_src ) . '" />';

						} elseif ( ! empty( $plugin_icons['default'] ) ) {

							// We have a default.
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
						<cite><?php _e( 'By', 'plugin-cards' );?> <?php echo wp_kses_post( apply_filters( 'plugin_cards_plugin_author', $plugin->author, $plugin ) ); ?></cite>
					</p>
				</div>
			</div>
			<div class="plugin-card-bottom">
				<div class="column-rating">
					<?php
					// Allow this whole section to be overridden.
					$plugin_rating = apply_filters( 'plugin_cards_plugin_rating', '', $plugin );

					if ( $plugin_rating ) {
						echo wp_kses_post( $plugin_rating );
					} else {
						?>
						<div class="star-rating">
							<?php
							$rating = (int)$plugin->rating;

							// Star 1
							if ( $rating >= 20 ) {
								echo '<span class="star star-full"></span>';
							} elseif ( $rating >= 10 ) {
								echo '<span class="star star-half"></span>';
							} else {
								echo '<span class="star star-empty"></span>';
							}

							// Star 2
							if ( $rating >= 40 ) {
								echo '<span class="star star-full"></span>';
							} elseif ( $rating >= 30 ) {
								echo '<span class="star star-half"></span>';
							} else {
								echo '<span class="star star-empty"></span>';
							}

							// Star 3
							if ( $rating >= 60 ) {
								echo '<span class="star star-full"></span>';
							} elseif ( $rating >= 50 ) {
								echo '<span class="star star-half"></span>';
							} else {
								echo '<span class="star star-empty"></span>';
							}

							// Star 4
							if ( $rating >= 80 ) {
								echo '<span class="star star-full"></span>';
							} elseif ( $rating >= 70 ) {
								echo '<span class="star star-half"></span>';
							} else {
								echo '<span class="star star-empty"></span>';
							}

							// Star 5
							if ( $rating >= 98 ) {
								echo '<span class="star star-full"></span>';
							} elseif ( $rating >= 90 ) {
								echo '<span class="star star-half"></span>';
							} else {
								echo '<span class="star star-empty"></span>';
							}
							?>
						</div>
						<span class="num-ratings">(<?php echo '<a href="https://wordpress.org/support/view/plugin-reviews/' . esc_attr( $plugin->slug ) . '" target="_blank">' . number_format_i18n( $plugin->num_ratings ) . '</a>'; ?>)</span>
						<?php
					} ?>
				</div>
				<div class="column-updated">
					<?php
					// Allow this whole section to be overridden.
					$last_updated = apply_filters( 'plugin_cards_last_updated', '', $plugin );

					if ( $last_updated ) {
						echo wp_kses_post( $last_updated );
					} else {
						?>
						<strong><?php _e( 'Last Updated', 'plugin-cards' ); ?>:</strong> <span>
							<?php printf( __( '%s ago', 'plugin-cards' ), human_time_diff( strtotime( $plugin->last_updated ) ) ); ?>
						</span>
						<?php
					} ?>
				</div>
				<div class="column-downloaded">
					<?php
					// Allow this whole section to be overridden.
					$install_count = apply_filters( 'plugin_cards_install_count', '', $plugin );

					if ( $install_count ) {
						echo wp_kses_post( $install_count );
					} else {

						if ( $plugin->active_installs >= 1000000 ) {
							$active_installs_text = _x( '1+ Million', 'Active plugin installs', 'plugin-cards' );
						} else {
							$active_installs_text = number_format_i18n( $plugin->active_installs ) . '+';
						}
						printf( __( '%s Active Installs', 'plugin-cards' ), $active_installs_text );
					}
					?>
				</div>
				<div class="column-compatibility">
					<?php
					// Allow this whole section to be overridden.
					$compatibility = apply_filters( 'plugin_cards_plugin_compatibility', '', $plugin );

					if ( $compatibility ) {
						echo wp_kses_post( $compatibility );
					} else {

						if ( ! empty( $plugin->tested ) ) {
							echo '<span class="compatibility-compatible"><strong>' . __( 'Compatible up to', 'plugin-cards' ) . ':</strong> ' . esc_attr( $plugin->tested ) . '</span>';
						}
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
