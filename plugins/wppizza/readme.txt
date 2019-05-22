=== WPPizza - A Restaurant Plugin ===
Contributors: ollybach
Donate link: https://www.wp-pizza.com/
Author URI: https://www.wp-pizza.com
Plugin URI: https://wordpress.org/extend/plugins/wppizza/
Tags: pizza, restaurant, pizzaria, pizzeria, restaurant menu, food ordering, ecommerce, e-commerce, commerce, wordpress ecommerce, store, shop, sales, shopping, cart, order online, cash on delivery, multilingual, checkout, configurable, variable, widgets, shipping, tax, wpml
Requires PHP: 5.3+
Requires at least: PHP 5.3+, MySql 5.5+, WP 3.3+ 
Tested up to: 5.1.1
Stable tag: 3.9.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A Restaurant Plugin (not only for Pizza). Maintain your Menu (sizes, prices, categories). Accept COD orders. Multisite, Multilingual, WPML compatible.



== Description ==

- **Conceived for Pizza Delivery Businesses, but flexible enough to serve any restaurant type.**

- Maintain your restaurant menu online and accept cash on delivery orders.

- Set categories, multiple prices per item and descriptions.

- Multilingual Frontend (just update labels in admin settings page and/or widget as required). WPML compatible.

- Multisite enabled

- Keeps track of your online orders.

- Shortcode enabled. (see <a href='https://docs.wp-pizza.com/shortcodes/'>complete shortcode list</a>)


**To see the plugin in action with different themes try it at <a href="https://demo.wp-pizza.com/">demo.wp-pizza.com</a>**

**if you wish to allow your customers to add additional ingredients to any given menu item, have a look at the premium <a href='https://www.wp-pizza.com/'>"WPPizza Add Ingredients"</a> extension**

= Current premium extensions available: =

* <a href='https://www.wp-pizza.com/'>Add Ingredients</a> - <a href='https://demo.wp-pizza.com/wppizza-add-ingredients/'>(Demo)</a>   
* <a href='https://www.wp-pizza.com/'>Delivery By Post/ZipCode</a> - <a href='https://demo.wp-pizza.com/wppizza-delivery-by-postcode/'>(Demo)</a>    
* <a href='https://www.wp-pizza.com/'>Cross-Sells</a> - <a href='https://demo.wp-pizza.com/wppizza-xsales/'>(Demo)</a> 
* <a href='https://www.wp-pizza.com/'>Pickup Prices</a> - <a href='https://demo.wp-pizza.com/wppizza-pickup-prices/'>(Demo)</a>    
* <a href='https://www.wp-pizza.com/'>Coupons and Discounts</a> - <a href='https://demo.wp-pizza.com/wppizza-coupons-and-discounts/'>(Demo)</a>    
* <a href='https://www.wp-pizza.com/'>Preorder</a> - <a href='https://demo.wp-pizza.com/wppizza-preorder/'>(Demo)</a>    
* <a href='https://www.wp-pizza.com/'>Timed Menu</a> - <a href='https://demo.wp-pizza.com/wppizza-timed-menu/our-menu/special-offers/'>(Demo)</a>    
* <a href='https://www.wp-pizza.com/'>Google Cloudprint</a> - (No demo available as it requires printer setup)     


**Additional gateways to process credit card payments instead of just cash on delivery are available at <a href='https://www.wp-pizza.com/gateways/'>www.wp-pizza.com</a>** 


== Installation ==

**Install**

1. Download the plugin and upload the entire `wppizza` folder to the `/wp-content/plugins/` directory.  
Alternatively you can download and install WPPizza using the built in WordPress plugin installer.  
2. Activate the plugin through the 'Plugins' menu in WordPress.  
3. You will find all configuration and menu options in your administration sidebar  


**Things to do on first install**

for consistency this document has now moved to the following location :   
<a href='https://docs.wp-pizza.com/getting-started/?section=setup'>https://docs.wp-pizza.com/getting-started/?section=setup</a>  
** I strongly encourage you to read it **  


**Uninstall**

Please note:  
although all options, menu items and menu categories get deleted from the database along with the table that holds any orders you may have received, you will manually have to delete any additional pages (such as the order page for example) that have been created as i have no way of knowing if you are using this page elsewhere or have changed the content/name of it.  
the same goes for the 3 example icons that come with this plugin as you might have used them elsewhere.


== Screenshots ==

1. frontend 
2. frontend (different theme) - minicart only
3. frontend - grid layout
4. frontend - order page
5. frontend - thank you page
6. frontend - purchase history (registered user)
7. admin - menu items quick edit
8. admin - global settings (excerpt)
9. admin - order settings (excerpt)
10. admin - opening times
11. admin - additives
12. admin - layout (excerpt)
13. admin - localization (excerpt)
14. admin - registered customers
15. admin - tools
16. admin - tools (GDPR)
17. admin - access rights
18. admin - order form
19. admin - sales reports
20. admin - widget
  

== Other Notes ==

= Translations provided by: =

* Italien:  Silvia Palandri  
* Hebrew:  Yair10 [&#1492;&#1500;&#1489;&#32;&#1489;&#1504;&#1497;&#1497;&#1514;&#32;&#1488;&#1514;&#1512;&#1497;&#1501;&#32;]  
* Dutch:  Jelmer  
* Spanish:  Andrew Kurtis at <a href="http://www.webhostinghub.com/">WebHostingHub</a>  
* German:  Franz Rufnak, Witali Opfer 

Many, many thanks guys and girls.  

Note: As the plugin gets updated over time and has some other strings and features added, the translations above (and future ones) will probably have a few strings not yet translated. If you wish, feel free to provide any of those missing and I will update the translations accordingly.  

If you want to contribute your own translation, feel free to send me your files and I will be more than happy to include them.  


= Demo Icons: =
Please note that the icons used in the demo installation are <a href="http://www.iconarchive.com/show/desktop-buffet-icons-by-aha-soft.html">iconarchive.com</a> icons and not for commercial use.  
If you do wish to use any icon from this set commercially, please follow <a href="http://www.desktop-icon.com/stock-icons/desktop-buffet-icons.htm">this link</a> to purchase it.  


== Changelog ==

3.9.6  
* Added: Allow for small thumbnails to be displayed on order pages (Layout->Menu Item Images->Thumbnail on checkout)   
* Fix: Category display (if enabled in WPPizza->Layout) not shown after updating item quantities on checkout page.   
5th May 2019  

3.9.5  
* Fix: Categories displayed multiple times above items in the same category in Emails/Order History (if enabled in WPPizza->Layout).    
* Fix: Bulk delete of orders (Order History) might have deleted more than selected   
* Fix: possible php notices in non-multisite setup in some edge cases  
* Tweak: made edit/preview etc icons in admin -> templates more easily fiterable adding 'wppizza_filter_template_icons_emails', 'wppizza_filter_template_icons_print' filters   
* Tweak: passing on full order details (incl localization etc) as additional 4th parameter to order execute action hook  
3rd May 2019  

3.9.4  
* Tweak: Minor admin css additions  
* Fix: missing category id when using add_item_to_cart button shortcode  
* Fix: Showing categories in cart (if enabled) not working correctly in cart used on users purchase history page  
* Fix/Tweak: unnecessarily getting results of more than one order for email/print template previews  
16th March 2019  

3.9.3  
* Fix: some helper functions passed on fixed instead of dynamic parameters    
* Addedd: some globally available helper functions to retrieve selected email/print template markup    
09th March 2019  

3.9.2  
* Fix: wrong column count in cart when displaying category headers  
* Tweak: more reliable way of determining REQUEST_SCHEME for different servers  
* Tweak: Minor spelling and information text highlight tweaks 
* Added: Allow for 'category' element in elements attribute shortcode 
* Added: added 'wppizza_filter_pages_purchasehistory_after_orders' and 'wppizza_filter_pages_purchasehistory_before_orders' filters to be used in users purchase history
* Added: 'wppizza_skeleton_template_emails' and 'wppizza_skeleton_template_print' to get selected templates including header/footer with replaceable content 
* Dev-Change: Admin->WPPizza->Customers , change of filter parameters for consitancy and future ease of use and flexibility. If you are using any wppizza filters that alter the customer rows/columns on this page in any way, get in contact. (Also some minor html markup adjustments)
* Dev: Added globally available 'wppizza_get_blog_dateformat' function
* Dev: Some Tweaks (possible phpnotices, ensuring any plugin version numbers get updated in db) in plugin dev helper functions
27th February 2019  

3.9.1  
* Fix: Orderpage widget - if placed on pages that are not set to be the orderpage - did not fully work   
* Tweak: set .wppizza-ignore, .ignore, :hidden, :disabled as validator ignore rules   
* Tweak: set default template styles to be html on new installs  
* Tweak: added report_range from/to to reports dataset array  
21st February 2019  


3.9  
* Tested upto WP 5.1  
* New: Detailed (per order) Report Export (filterable columns/data)  
* New: Easier addition of custom export/report data (see documentation)  
* Fix: Cart details were displayed out of view when minicart displayed at bottom of page  
* Fix: Admin Dashboard Widget not always displaying accurate details for todays orders (depending on timezones set)  
* Fix: Some more possible edge cases php notices/warning eliminated  (checking for currentbusinessday, user_level)  
* Fix: Some user metadata of the plugin might have remained on uninstall  
* Fix: Not all 3rd Party, EDD enabled plugins, allowed activation of license  
* Tweak: Note added in WPPizza->localization that some gateways might truncate the textstring passed on as order description   
* Tweak: Minor output formatting adjustments for multi checkboxes in WPPiza->Gateways administration  
* Tweak: Removed unused "class.wppizza.filter.orders.php"  
* Dev/New: Added wppizza_filter_email_settings and wppizza_filter_mail_recipients filter hooks  
* Dev/New: Added convenience function wppizza_get_active_gateways() 
* Dev/New: added 'wppizza_on_cart_update' action hook when update cart was triggered (runs before calculating cart values)  
* Dev/New: Added some more globally available helper/validation functions  
* Dev/Tweak: Using whole URL when determining if admin shortcode exists on page using url_to_postid  
* Dev: Allow globally available wppizza_is_orderpage/wppizza_is_checkout functions to determine result even before post object is available  
* Dev: Some housekeeping (renaming of internally used filter functions (filter_send_emails)
* Dev: Removed superflous parameters passsed to email and template functions  
* Dev: Removed all instances of fetch_order_details() function (deprecated since 3.5) in favour of get_orders() for more consistency throughout and eliminate a couple unneeded db queries  
19th February 2019  


3.8.6  
* Tested upto WP 5.0.3  
* Fix: eliminated some more possible php notices  
* Added/Tweak: Allow reset of default templates (WPPizza->Tools)  
* DEV - Tweak: Adding 2 more additional parameters to filters that return boxes in reports page  
* DEV - Added: filterable ('wppizza_filter_session_add_item_to_cart') "custom_data" key for each order item when adding item to cart and saved to db when order is submitted.  
09th January 2019  


3.8.5  
* Fix: eliminated some possible php notices  
* Fix/Teeak: reduced a couple of varchars db columns from a somewhat needlessly excessive 255 to 190 chars to allow install even with very restrictive/old db setups (avoiding: "Index column size too large. The maximum column size is 767 bytes." errors)    
06th January 2019  


3.8.4  
* Fix: email was not always prefilled on orderpage (if prefill enabled and logged in )  
* Fix: php notice if user was deleteed from table but order still exists  
* Fix: removed double surrounding brackets when using [wppizza type=additives] shortcode  
* Added: shortcode 'notags' attribute to strip menu item content of all tags  
* Added: shortcode 'price_id' attribute added with type='single' to only display specific sizes  
* Dev - Added: wppizza_get_orders() added more order_id query options/clauses (order_id_lt, order_id_lte, order_id_gt, order_id_gte, order_id_in)  
* Dev - Added: wppizza_get_orders() added $args['sort']['sortorder'] attribute  
* Dev - Added: wppizza_delete_order_meta_by_key and wppizza_get_order_id_by_meta_key functions  
* Dev - Added: filter  'wppizza_filter_shortcode_registered_styles'  
* Dev - Tweak: style and post_id paramters to wppizza_filter_post_arcticle_class  
* Dev - Tweak: added some dedicated filters on order page after each element for easier markup manipulation ('wppizza_filter_orderpage_before_login_form', ' ..._before_orderform', '..._after_order_details', '..._after_personal_details', '..._after_payment_details', '..._after_orderform')  
21st December 2018  


3.8.3  
* Fix: sanity "class_exist" check added when determining if a gateway support refunds as it might cause fatal errors if an installed gateway was subsequently de-activated  
2nd December 2018  


3.8.2  
* Tweak: added current page/post id to wppizza js localized parameters   
* Tweak: allow for 0 in orders meta table order_id to store parameters unrelated to any specific order id    
* Added: (WP 5+ only) Disable Gutenberg editor by default (for now) for WPPizza Menu Items (but can be enabled in WPPizza->Settings->General)
* Tweak: made WPPizza register_post_type 'supports' arguments an indexed array  
* Fix: wppizza_menu-[termslug] and wppizza_menu-term-[termid] classes always had current term missing (if WP version < 5 only it seems)  
* Tweak: Added minicart position (top/bottom) option  
* Fix: some possible 'illegal offset size' warning eliminated in class.wppizza.sales_data.php  
* Fix: using 'WPPizza -> Tools -> Maintenance : Update order table' to create possibly missing db tables did not create the correct table prefixes  
* Fix: inclusion of order parameter to indicate if a selected gateway for an order supports refunds (introduced in 3.8) was wrong / did not work   
27th November 2018  


3.8.1  
* Fix: using shortcode [wppizza type='orderhistory'] on a page with any other shortcode before resulted in not loading validation functions   
2nd November 2018  


3.8  
* Added: wppizza_menu-[termslug] and wppizza_menu-term-[termid] classes to article element for all terms an item belongs to  
* Added: shortcode to display dashboard widget in frontend [wppizza_admin type='admin_dashboard_widget']  
* Added: frontend templates for confirmed (page.confirmed.php), refunded (page.refunded.php), rejected (page.rejected.php) orders  
* Tweak: sanitised grid.css parameters to (perhaps) eliminate false positives some security plugins might throw regarding xss vulnerability  
* Fix: &amp; not decoded in userdata in emails and subject lines  
* Fix: typo in get_orders function in class.wppizza.db.php ($no_argumens_passed -> $no_arguments_passed)  
* Fix: value of formatted orders 'payment_method' changed to cod | prepaid (instead of using changeable value_formatted)  
* Fix: various bugs in (3rd Party) plugin helper functions (suboption checkboxes were not allowed to be all de-selected again for example)  
* Dev - Added: wppizza_orders_meta table and associated function, query options and hooks to allow arbitrary data to be stored with and retrieved for an order (for 3rd party plugins)  
* Dev - Added: formatted orders parameters now include if gateway used supports refunds  
* Dev - Added: wppizza_execute_update_order_to_captured, wppizza_execute_update_order_to_unconfirmed, wppizza_execute_update_order_to_confirmed filter hooks to allow for interception of order execution  
* Dev - Tweak: id in order table now "BIGINT(20) UNSIGNED" instead of "INT(10)"  
* Dev - Tweak: dedicated class to return sales data (as array)  
* Dev - Tweak: Allow for additional payment status 'CONFIRMED' to be queried when getting orders  
* Dev - Deprecated: disabled (to be removed) class.wppizza.filter.orders.php as it's not in use anywhere  
24th October 2018  


3.7.1  
* Tweak: made default plaintext email linelength 70 characters (from 74) to try to acommodate some more small screen device email clients    
* Fix: Missing Parameters when using "WPPizza -> Tools -> Maintenance -> Update order table"  
* Fix: User registration emails (when registering account at checkout) to admin and user might contain order details instead of registration details if using plaintext wppizza email templates  
* Fix: phpnotice of undefined AltBody removed  
20th September 2018  


3.7  
* Fix: wppizza smtp settings - if enabled - were not always applied  
* Fix: added missing "personal information" and "order details" localization strings in use on "thank you" page that erroneously used strings reserved for confirmation page  
* Added: class "wppizza-totals-no-items" |"wppizza-totals-has-items" to minicart parent div if (no) item in cart  
* Added: "count_only" attribute for [wppizza type='totals'] shortcode    
* Added: set of filters to enable customisation of default install sizes/categories/items/prices/additives (wppizza_filter_install_default_sizes, wppizza_filter_install_default_additives, wppizza_filter_install_default_categories, wppizza_filter_install_default_items)  
* Tweak: on new installations default pickup toggle to be enabled on order page too (as opposed to just under cart)  
* Tweak: added _SERVER vars to some potential error messages to aid debugging  
* Tweak: added formatted order  parameter to 'wppizza_filter_email_subject' filter  
* Tweak: display full path of all customised wppizza templates in wppizza->tools->system info (if any)  
* Tweak: added wppizza custom header to emails send by wppizza for identificationa and debug purposes and to more reliably use smtp for wppizza mails only  
* Tweak: allow gateway classnames to have underscores after 'WPPIZZA_GATEWAY_' Prefix  
* Dev/Tweak: added generic - empty - wppizza-submit-error div right before pay buttons to allow custom js to write errors into if required  
* Dev/Tweak: added 'PAYMENT_PENDING' db payment_status column for payment methods that perhaps only pay hours or even days later  
* Dev: removed superflous argument from static wppizza_selected_gateway function  
* Dev: several action, filter hooks and helper functions added to allow for inline payment gateway development  
28th August 2018  


3.2 - 3.6.6  
* changelogs for versions 3.2 to 3.6.6 moved to /wppizza/changelogs/  


3.0 - 3.1.7  
* changelogs for versions 3.0 to 3.1.7 moved to /wppizza/changelogs/  


1.0 - 2.16.11.28  
* changelogs for versions up to 3.0 can be found in /wppizza/changelogs/  


== Frequently Asked Questions ==

= General Faq's =

for consistency and manageability the faq's have been moved to <a href='https://docs.wp-pizza.com/faqs/'>https://docs.wp-pizza.com/faqs/</a>

= Shortcodes = 

please refer to <a href='https://docs.wp-pizza.com/shortcodes/'>https://docs.wp-pizza.com/shortcodes/</a>


= How can I submit a bug, ask for help or request a new feature? =

- leave a message on the <a href="https://wordpress.org/support/plugin/wppizza">wordpress forum</a> and I'll respond asap.  
- send an email to dev[at]wp-pizza.com with as much info as you can give me or 
- use the <a href="https://www.wp-pizza.com/contact/">"contact form"</a>, <a href="https://www.wp-pizza.com/forum/feature-requests/">"feature request" page </a> or <a href="https://www.wp-pizza.com/support/">support forum</a> on <a href="https://www.wp-pizza.com/">www.wp-pizza.com</a>


	
	>**additional premium add-ons can be found at <a href="https://www.wp-pizza.com/">www.wp-pizza.com</a>**  
	>


== Upgrade Notice ==

= 3.0 =
<b>WARNING : DO NOT SIMPLY CLICK ON "UPDATE". YOU *MUST* READ THE DOCUMENTATION OF <a href="https://docs.wp-pizza.com/getting-started/?section=upgrade-v2-x-v3-x" target="_blank" style="color:#6ce414;text-decoration:underline">HOW TO UPGRADE</a> FROM WPPIZZA VERSION 2.x to VERSION 3.X </b>
&nbsp;
You are <b>STRONGLY</b> advised to also make a <b>FULL BACKUP OF YOUR SITE</b> first. Please refer to the <a href="https://docs.wp-pizza.com/getting-started/?section=upgrade-v2-x-v3-x" target="_blank" style="color:#6ce414;text-decoration:underline">upgrade documentation</a>.
&nbsp;
<b>Developers/Sites that have customised the WPPizza v2.x plugin:</b> 
If you have edited templates, are using some actions/filters, have amended the css etc, you should read the <a href="https://docs.wp-pizza.com/getting-started/?section=migrating-2-x-to-3-x-customisations" style="color:#6ce414;text-decoration:underline">Migrating Customisations</a> documents. Many things have changed !!
Do NOT just update and hope your customisation(s) will work. They probably will not and things will break. Again, whatever you do, make a complete backup of your site first !!!
&nbsp;
<b>IF YOU ARE NOT SURE ABOUT SOMETHING PLEASE ASK <a href="https://www.wp-pizza.com/support/" target="_blank" style="color:#6ce414;text-decoration:underline">IN THE FORUM</a> OR USE THE <a href="https://www.wp-pizza.com/contact/" target="_blank" style="color:#6ce414;text-decoration:underline">CONTACT FORM</a></b>
&nbsp;