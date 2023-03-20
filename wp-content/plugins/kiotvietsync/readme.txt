=== KiotViet Sync ===
Contributors: tuyenvv92
Tags: importer, sync, kiotviet, synchronized
Donate link: https://www.kiotviet.vn/
Requires at least: 4.9
Tested up to: 6.0
Requires PHP: 7.1
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Plugin supports data synchronization between KiotViet and Wordpress (Woocommerce). Synchronous information including products, orders and categories.

== Description ==
We support you to synchronize data from KiotViet to Wordpress website via KiotViet Sync plugin.
Make it easier for you to reach online customers.
Create a WordPress Shop website more easily.
Synchronize inventory data, prices, photos, orders without complicated operations or boring manual tasks.
This plugin will help you
* Connecting with KiotViet is easy
* Synchronize products, orders, goods automatically
* Update inventory, photos, automatic status from KiotViet
* Completely free
Thanks for using our product.
[Contact support](https://www.kiotviet.vn/lien-he/ "Contact support")
Support On Facebook: [KiotViet Sync - Support Developers](https://www.facebook.com/groups/3109767249041169/ "KiotViet Sync - Support Developers")

== Installation ==
1. Upload "kiotviet-sync.zip" to the "/wp-content/plugins/" directory.
1. Activate the plugin through the "Plugins" menu in WordPress.
1. Use the plugin via the "KiotViet Sync" menu.

You can also:

1. Navigate to "Plugins" > "Add New".
1. Browse and search for plugins "KiotViet Sync"
1. Click on "Install Now" button
1. Click "Activate" to activate the plugin.

== Frequently Asked Questions ==
= What is KiotViet? =
KiotViet is a POS, supporting users to check inventory, sales and many other utilities.

= Does the woocommerce plugin is required? =
Yes. Woocommerce is required for KiotViet Sync plugin.

= Will it be free forever? =
Yes

== Screenshots ==
1. https://www.kiotviet.vn/wp-content/uploads/2019/07/Screenshot1.png
2. https://www.kiotviet.vn/wp-content/uploads/2019/07/Screenshot2.png
3. https://www.kiotviet.vn/wp-content/uploads/2019/07/Screenshot3.png

== Changelog ==
= 1.5.0 - 11/07/2022 =
- Fix product attribute deleted
- Fix product to trash after update
= 1.4.9 - 27/06/2022 =
- Update order products
= 1.4.8 - 15/06/2022 =
- Update error message sync order
= 1.4.7 - 25/05/2022 =
- Add Webhook's Status
- Add Webhook's cUrl Testing
= 1.4.6 - 13/04/2022 =
- Update WP function
- Fix timezone sync history
- Change path of log folder
- Fix clear stock cache after update
= 1.4.5 =
- Fix webhook action
= 1.4.4 =
- Fix sync product inventory
- Add button cancel connect in loading branch screen
= 1.4.3 =
- Add option only sync new products
- Update checker WP Cron
- Add payment method to description in order
= 1.4.2 =
- Add function auto sync orders
- Fix order sync
= 1.4.1 =
- Add new sync attribute option
- Update logic check product type
- Update force delete product from webhook
- Add new function to get surcharge from KiotViet
= 1.4.0 =
- Checking php requirements
- Fix issue get image from KiotViet
- Update new style
- Update log location to kiotviet_log in wp-content
- Update checker (curl, $_SERVER[“HTTP_HOST”])
- Update logic for sync product, variation product, images, product unit, category, discount
- Update workflow to sync branch first
- Add new page to check webhook registed
- Add clear cache button
- Update readme
= 1.3.0 =
- Checking php requirements
- Fix issue get image from KiotViet
= 1.2.0 =
- Sync product unit
- Remove info sync product as short description, tag …
- Update hash image name Kiotviet
- Update sync fast with product variable
- Change data type from text to longtext
- Update hook delete product website
- Update hook delete category website
- Fix pricebook and stock with product variable
- Change prefix order code Kiotviet
- Fix pricebook expires date
- Update webhook product simple to variant and opposite
- Update webhook stock product parent
- Fix manager customer by branch
= 1.1.0 =
- Update options for sync data
- Sync data by SKU
- Update logic for stock
- Add Sync button by manually
= 1.0.0 =
- Sync products, categories, orders, images
- Auto update via webhook