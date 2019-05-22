=== Food Menu ===
Contributors: techlabpro1
Donate link:
Tags: food, food menu, menu, cafe, coffee, cuisine, dining, drink, restaurant, restaurant menu, tlp food menu.
Requires at least: 4
Tested up to: 5.2
Stable tag: 2.2.60
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Fully responsive and mobile friendly WP food menu display plugin for restaurant, cafes, bars, coffee house, fast food.

== Description ==

[Plugin Demo](http://demo.radiustheme.com/wordpress/plugins/food-menu/) | [Documentation](https://radiustheme.com/how-to-setup-and-configure-tlp-food-menu-free-version-for-wordpress/) | [Get Pro Version](https://www.radiustheme.com/downloads/food-menu-pro-wordpress/)

TLP Food Menu is fully responsive and mobile friendly food menu display plugin for restaurant, cafes, bars, coffee house, fast food. you can call it in templates, posts, pages and widgets. From admin end you can easily create food item with name, description, Excerpt (used as short description), image and price.

It is fully HTML5 and CSS3. It has ShortCode and widget included. You can display all food item or multiple category or single category at a time.

[youtube https://www.youtube.com/watch?v=l4xLIPvj-ic]

> [Click here to get Red Chili - WordPress Theme using FoodMenu Pro plugin](https://themeforest.net/item/red-chili-restaurant-wordpress-theme/20166175?ref=RadiusTheme)

= Features =
* Fully Responsive
* Display All Food item, Multiple or Single Category in a Page/ Post
* Currency select option
* Custom meta fields
* Custom CSS option
* ShortCode
* Custom Detail Page template

= Fully translatable =
* POT files included (/languages/)

= Available fields =
* Title (Menu item name)
* Description (Post Content)
* Category (WP default)
* Order (used for ordering in menu order)
* Price (Custom field)
* Excerpt (used as short description)
* Featured image (Main image)


= ShortCode settings =

* **All Food Items:**
```
[foodmenu] or [foodmenu orderby="menu_order" order="ASC"]
```
* **Display Multi Category:**
```
[foodmenu cat="4,8" orderby="title" order="ASC"]
```
* **Display Single Category:**
```
[foodmenu cat="4" orderby="menu_order" order="ASC"]
```
* **cat** = catgory id (only integer)
* **orderby** = Orderby (title , date, menu_order)
* **order** = ASC, DESC

= For Use Template PHP File :- =
<code><?php echo do_shortcode('[foodmenu cat="4" orderby="menu_order" order="ASC"]'); ?></code>

= Pro Features =

* Fully responsive and mobile friendly.
* 11 Amazing Layouts with Grid, Masonry, Isotope & Slider.
* Even and Masonry Grid for all Grid.
* Layout by category.
* Generate Unlimited grid.
* Layout Preview in Shortcode Settings.
* Custom number of menu per page.
* Order by Id, Name, Create Date, Menu Order, Random & Price.
* Display image size (thumbnail, medium, large, full and Custom Image Size)
* Custom Image Re-size option.
* Add Visual Composer Addon.
* Search field on Isotope.
* Set Default Isotope Filter Button.
* Disable Show All Button for Isotope Filter.
* All Fields Control.
* All Text color, size and Button Color control.
* Overlay color and opacity control.
* Default image set option in Shortcode generator settings.
* Enable/Disable Pagination.
* Number Pagination Supported.
* Ajax Pagination: Load more, Load on scroll and AJAX Number Pagination
* AJAX Number Pagination (only for Grid layouts).
* Single Menu Item Popup.

For any bug or suggestion please mail us: support@radiustheme.com

== Installation ==

1. Add plugin to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Create food menu.
4. Add shortCode to display the food item.

= Requirements =

* **WordPress version:** >= 4
* **PHP version:** >= 5.2.4

== Frequently Asked Questions ==

= How to Use Food menu =

* Go to `Food menu > Add Food`
* Go to page or post editor insert shortcode.

* **All Food Items:**
```
[foodmenu] or [foodmenu orderby="menu_order" order="ASC"]
```
* **Display Multi Category:**
```
[foodmenu cat="4,8" orderby="title" order="ASC"]
```
* **Display Single Category:**
```
[foodmenu cat="4" orderby="menu_order" order="ASC"]
```

= Need Any Help? =

* Please mail us at `support@radiustheme.com`
* We provide `15 hours live support`

== Screenshots ==

01. All list view
02. Category list view
03. Specific list view
04. Widget view
05. Add New Food
06. Food Menu Settings

== Changelog ==

= 2.2.6 =
* Fix Cat loop issue

= 2.2.51 =
* Fix GutenBurg script error

= 2.2.5 =
* Add Elementor and GutenBurg support

= 2.2.4 =
* Add shortcode wrapper class

= 2.2.3 =
* Add elementor support

= 2.2.2=
* Fixed Archive page

= 2.2.1 =
* Remove admin Notice

= 2.1 =
* Hide price at detail page
* Fix deprecated issue

= 2.1 =
* Add Quick Edit price
* hide image and link issue fixed
* Add new feature at settings for hide image from details

= 2.0 =
* Shortcode Generator added
* Fixing some coding issue
* Title color added

= 1.2 =
* Pro version released
* Fixing some jquery issue

= 1.1 =
* Permalink
* Layout Grid style improvement
* Fix some bug

= 1.0 =
* Initial upload
