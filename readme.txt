=== Plugin Cards ===
Contributors: Braad
Donate link: http://braadmartin.com/
Tags: plugin, cards, repo, search, query, wordpress.org, author, tag
Requires at least: 4.0
Tested up to: 4.3
Stable tag: 1.1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Display plugin cards that match those introduced in WordPress 4.0. Uses the wordpress.org API and supports custom queries.

== Description ==

Plugin Cards lets you display WordPress 4.0 style cards with plugin information for any plugin that lives on the wordpress.org repository. All of the custom queries that are possible using the wordpress.org API are possible with this plugin, including queries for plugins by:

* Slug
* Author
* Tag
* Search term
* Brose terms: popular, new, & beta
* A wordpress.org user's favorites list
* Any other custom query you pass in via an included filter

This plugin uses the same wordpress.org API that your wp-admin uses when you are searching for new plugins, and it uses as much of the admin CSS and core functionality as possible to bring you a purposefully stock experience.

I have also included a number of filters in the plugin that allow you to customize everything. You can use a custom query, include custom fields not shown by default, use custom icons, use custom plugin urls, add to or override the information shown in each section of the output, override the entire output with your own, and more.

This plugin is [on Github](https://github.com/BraadMartin/plugin-cards "Plugin Cards on Github") and I encourage feature requests and pull requests.

= Features =

* Query the wordpress.org plugin repo by all methods supported by the wordpress.org API, including by slug, author, tag, popular, new, beta, user's favorites, and search term
* Cards match the design introduced in WordPress 4.0+
* Uses the wordpress.org API to get plugin information
* Caches results to minimize hits on the API
* Default fields include plugin name, icon, description, author link, star rating, active install count, last updated and compatible up to
* Uses SVG plugin icon if available, then retina icon if available, then regular icon
* Uses the WP native Dashicons for the star ratings
* Intelligently Responsive
* Easily customize the look of the cards with CSS
* Easily customize the functionality of the entire plugin with provided filters

With Plugin Cards you can easily display a custom list of plugins for any purpose.

= Usage =

This plugin adds a shortcode `[plugin_cards]` that you can use to display the plugin cards anywhere on your site.

The following parameters can be used to create your query:

	[plugin_cards slug="easy-digital-downloads"]
	[plugin_cards author="markjaquith"]
	[plugin_cards user="matt"] // Displays user's favorites list
	[plugin_cards tag="slider"]
	[plugin_cards browse="popular"]
	[plugin_cards browse="new"]
	[plugin_cards browse="beta"]
	[plugin_cards search="gallery"]

**Note:** The wordpress.org API only supports querying by one parameter at a time, so currently only one parameter can be included in the shortcode to build the query. If this ever changes I will add support for querying by multiple parameters to this plugin.

You can also set the max number of results using the max_results parameter like this:

	[plugin_cards max_results="10" browse="popular"]

= Advanced Usage =

This plugin includes a number of filters that you can use to customize the display of the cards and add plugin information to the cards. I recommend reading through the code if you really want to understand how the filters can be used. Here are some examples:

**Use a custom query**

	function custom_plugin_cards_query( $custom_query_args, $atts = array(), $fields = array() ) {

		// Show the 10 most popular plugins with only certain fields returned
		$custom_query_args = array(
			'per_page' => 10,
			'browse' => 'popular',
			'fields' => array(
				'banners' => true,
				'icons' => false,
				'reviews' => true,
				'rating' => true,
				'num_ratings' => true,
				'downloaded' => false,
				'active_installs' => false,
				'short_description' => false,
				'sections' => true,
				'downloadlink' => true,
				'last_updated' => true,
				'homepage' => true,
			)
		);

		return $custom_query_args;

	}
	add_filter( 'plugin_cards_api_query_args', 'custom_plugin_cards_query', 10, 3 );

Then you'd have to use one of the output filters and some CSS to get the new fields to show on the page. There is a lot that is possible with the `plugin_cards_api_query_args` filter, too much to cover here, but the key is setting the right main query param and setting the fields that you want returned.

**Use custom URLs**

	function custom_plugin_card_urls( $plugin_url, $plugin = null ) {

		// Point URLs to a custom endpoint based on the plugin slug
	    $plugin_url = 'https://domain.com/custom-plugins/' . esc_attr( $plugin->slug );

	    // Change the URL of a specific plugin
	    if ( 'woocommerce' === $plugin->slug ) {
	        $plugin_url = 'http://www.woothemes.com/woocommerce/';
	    }

	    return $plugin_url;

	}
	add_filter( 'plugin_cards_plugin_url', 'custom_plugin_card_urls', 10, 2 );

The complete list of simple filters that work exactly like plugin_cards_plugin_urls is:

	plugin_cards_plugin_url
	plugin_cards_plugin_name
	plugin_cards_short_description
	plugin_cards_plugin_author

There are also some advanced filters that allow you to override the HTML output of entire sections, including:

	plugin_cards_plugin_icon
	plugin_cards_plugin_rating
	plugin_cards_last_updated
	plugin_cards_install_count
	plugin_cards_plugin_compatibility

These filters can be used like this:

**Use custom plugin icons**

	function custom_plugin_card_icons( $plugin_icon, $plugin = null, $plugin_url = '' ) {

		// Replace all plugin icons with kittens
		$plugin_icon = '<a href="' . esc_url( $plugin_url ) . '" class="plugin-icon"><img src="http://domain.com/kittens.jpg" /></a>';

		// Replace the icon for a specific plugin
	    if ( 'equal-height-columns' === $plugin->slug ) {
	        $plugin_icon = '<a href="' . esc_url( $plugin_url ) . '" class="plugin-icon"><img src="http://domain.com/custom-icon.jpg" /></a>';
	    }

		return $plugin_icon;

	}
	add_filter( 'plugin_cards_plugin_icon', 'custom_plugin_card_icons', 10, 3 );

There is also one special filter `plugin_cards_cache_expiration` that allows you to set how long you want the results of the API request to be cached. The default is 15 minutes (expressed as number of seconds).

If you want a hook added just let me know. Pull requests are welcome [on Github](https://github.com/BraadMartin/plugin-cards "Plugin Cards on Github").

== Installation ==

= Manual Installation =

1. Upload the entire `/plugin-cards` directory to the `/wp-content/plugins/` directory.
1. Activate Plugin Cards through the 'Plugins' menu in WordPress.

= Better Installation =

1. Go to Plugins > Add New in your WordPress admin and search for Plugin Cards.
1. Click Install.

== Frequently Asked Questions ==

= How does it work? =

When you navigate to the plugin install screen from the wp-admin dashboard on your site, a request goes out to the wordpress.org API to grab the information about plugins in the official repository. This plugin includes the core functionality used to fetch the plugin information from the API on the front end of your site and adds a shortcode for easy query building and portability.

= Can I query by multiple parameters at once? =

At this time the wordpress.org API can only be queried by one of the included query parameters at a time. If the API ever allows more complex queries, I'll be happy to add support for them to this plugin.

= How does the plugin cache results? =

This plugin uses transients to store the results from each unique API request. This minimizes hits on the API and dramatically speeds up the loading of the cards. The expiration on the transients is set to 15 minutes by default, but you can use the filter `plugin_cards_cache_expiration` to set your own cache expiration.

== Screenshots ==

1. Output of the [plugin_cards] shortcode
2. Small screen single column view

== Changelog ==

= 1.0.1 =
* Introduced some JS to ensure we are always showing the right number of columns
* Switched to using Dashicons for the star ratings

= 1.0.0 =
* First Release

== Upgrade Notice ==

= 1.0.1 =
* No breaking changes :)

= 1.0.0 =
* First Release