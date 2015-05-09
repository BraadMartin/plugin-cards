=== Plugin Cards ===
Contributors: Braad
Donate link: http://braadmartin.com/
Tags: plugin, cards, repo, search, query, wordpress.org, author, tag
Requires at least: 4.0
Tested up to: 4.3
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Display plugin cards that match those introduced in WordPress 4.0. Uses the wordpress.org API and supports custom queries.

== Description ==

Plugin Cards lets you display WordPress 4.0 style cards with plugin information for any plugin that lives on the wordpress.org repository. You can select any specific plugin by slug, or you can query the repository by author, tag, popularity, or search term.

This plugin uses the same wordpress.org API that your wp-admin uses when you're searching for new plugins, and it reuses as much of the admin CSS and core functionality as possible.

= Features =

* Query the wordpress.org plugin repo by all methods supported by the wordpress.org API, including by slug, author, tag, popularity, and search term
* Cards match the design introduced in WordPress 4.0+
* Uses the wordpress.org API to get plugin information
* Displayed fields include plugin name, icon, description, author link, star rating, active install count, last updated and compatible up to
* Uses SVG plugin icon if available, then retina icon if available, then regular icon
* Fully Responsive
* Easily customize the look of the cards with CSS

With Plugin Cards you can easily display a custom list of plugins for any purpose!

= Usage =

This plugin adds a shortcode [plugin_cards] that you can use to display the plugin cards anywhere on your site.

The following parameters can be used to create a custom query:

`
[plugin_cards slug="easy-digital-downloads"]
`

`
[plugin_cards author="markjaquith"]
`

`
[plugin_cards tag="slider"]
`

`
[plugin_cards browse="popular"]
`

`
[plugin_cards search="gallery"]
`

**Note:** Using the shortcode with a slug will return a single result and will take precendence over all other query parameters. 

**Note:** At this time the wordpress.org API only supports querying by one parameter at a time, so currently only one parameter can be included in the shortcode to build the query.

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

== Screenshots ==

1. Output of the [plugin_cards] shortcode
2. Small screen single column view

== Changelog ==

= 1.0 =
* A change since the previous version.
* Another change.

= 0.5 =
* List versions from most recent at top to oldest at bottom.

== Upgrade Notice ==

= 1.0 =
Upgrade notices describe the reason a user should upgrade.  No more than 300 characters.

= 0.5 =
This version fixes a security related bug.  Upgrade immediately.