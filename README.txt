=== Constellation Client Portal ===
Author: ARS
Contributors: arstudios
Tags: client portal, private files, private pages, private posts, customer portal, business portal, invoicing, business managemenet, client management
Requires at least: 6.0.0
Tested up to: 6.6
Stable tag: 1.9.0
Requires PHP: 7.4
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

== Description ==

Turn your WordPress site into a professional client portal. Create private pages and posts, and attach private files (example: pdf, jpg, docx, xlsx, etc.) for secure collaboration and document management.

Upgrade to the [Pro version](https://adrianrodriguezstudios.com/constellation-client-portal/?utm_source=wporg "Get Constellation Client Portal Pro") and integrate with WooCommerce to accept payments for invoices and services.

Streamline your operation and improve client satisfaction with Constellation Client Portal.

== Features ==

* Create private pages for your clients, customers, and team members.
* Create private invoice and file posts for your clients and display them on private client pages, with simple to use shortcodes.
* Attach private files (example: pdf, jpg, docx, xlsx, etc) to invoice and file posts.
* Prevent direct access to client files by users, search engines, and bots.
* Assign WordPress users to Companies.
* Assign users (example: consultants) to multiple companies to allow them to access files that are assigned to different companies.
* Easily add curated lists of invoices and documents to client pages via simple to use shortcodes.

== Pro ==

* Automatically redirect clients to their private client page at login (optional login redirect setting).
* Integrate your invoices with WooCommerce and add a pay button to your unpaid invoices, and accept payments from your customers and clients (requires WooCommerce).
* Change the Client Page URL base name from "accp-client-page" to a name of your choosing.
* Automatically send email notifications to clients when a new File or Invoice post is created.
* Automatically send reminder email notifications on a schedule.
* Easily customize the look of client-facing lists.
* Display client-facing lists in list or grid layout.
* Easily generate, save, and edit file and invoice shortcodes within the plugin settings.
* Add due dates and past due notices to invoices and files.
* Further restrict file and invoice access within a company by user and role.
* Restrict file and invoice category access by user and role.
* Add internal notes to File and Invoice posts.
* Export file and invoice lists to CSV.
* Create [global client pages](https://adrianrodriguezstudios.com/2023/05/16/how-to-utilize-global-pages/ "Client Portal Global Pages").
* Create global client files that can be accessed by more than one company.

== Use Cases ==

Constellation Client Portal is your portal for everything, and helps you interface with clients, customers, teams, and groups.  It's professional, extendable, versatile, and is designed to sit at the heart of your organization to save time and lower costs.


**Example Use Case Areas**

* Businesses and Professionals - Interface with clients, customers, employees, contractors, and vendors.
* Freelancers - Interface with clients, customers, contractors, and vendors.
* Project Managers / Teams - Interface with stake holders, contractors, project members, and vendors.
* Teams - Interface with team members, staff, affiliates, vendors, and contractors.
* Groups and Organizations - Interface with group members, and other affiliates.

== Screenshots ==

1. Client-Facing - Current Invoice List
2. Client-Facing - Paid Invoice List
3. Client-Facing - Grid Style Document/File List
4. Admin Settings - Pro General Settings
5. Admin Settings - Pro Invoice Settings
6. Admin Settings - Pro File Settings
7. Admin Settings - Pro Client Page Settings
8. Admin Settings - Pro Company Settings
9. Admin Settings - Pro Email Settings
10. Admin Settings - Invoice Post Edit
11. Admin Settings - File Post Edit
12. Admin Settings - Company Page Edit
13. Admin Settings - Client Page Edit
14. Admin Settings - Pro Invoice Bulk Create
15. Admin Settings - Pro List/Shortcode Settings
16. Admin Settings - Pro List/Shortcode Edit Settings
17. Admin Settings - Pro List/Shortcode Theme Settings

== Shortcodes (Core) ==

The shortcode parameters, below, are for use with the core version of the plugin.  The pro version allows for file and invoice shortcodes to be easily generated, saved, and edited within the plugin settings.

= Unpaid Invoice List =

`[accp_clientinvoices invoice_status="unpaid"]`

= Paid Invoice List =

`[accp_clientinvoices invoice_status="paid"]`

= Invoice Shortcode Parameters =

* **invoice_status** this is the payment status of the invoice. Accepted Values: “paid” or “unpaid”. Default: “unpaid”.
* **display_number** this is the number of posts per page. Accepted Values: any positive whole number. Default: -1 (which displays all posts returned in a query).
* **order_by** this allows for lists to be sorted by post title instead of date. Accepted Values: “title” or “date”. Default: “date”.
* **order** this allows the sort order to be changed. Accepted Values: “ASC” or “DESC”. Default: “DESC”.
* **show_excerpt** this allows for a post excerpt to be displayed with each list item. Accepted Values: “true” or “false”. Default: “false”.
* **excerpt_length** this allows you to constrain the number of words, “show_excerpt” is set to “true”. Accepted Values: any positive whole number. Default: null.
* **show_thumbnail** this allows the featured image to be displayed with each list item (if a featured image is set). Accepted Values: “true” or “false”. Default: null.
* **thumbnail_size** this allows you to choose an image size if the “show_thumbnail” attribute is set to “true”. Accepted Values: any valid thumbnail slug that is available in your theme (ex. “full”). Default: null.
* **align_thumbnail** this allows the thumbnail image alignment to be set if the “show_thumbnail” attribute is set to “true”. Accepted Values: “center”, “left”, “right”, “float-left”, or “float-right”. Default: null.
* **show_post_date** this allows for the WordPress post date to be displayed with each item in a list. Accepted Values: “true” or “false”. Default: “false”.
* **categories** Accepted Values: this can be entered as an Invoice Category Slug or ID, or a combination of those. Separate multiple values with a comma (ex. “21, category-a”). Default: null.
* **link_title** Accepted Values: “nolink”.  Adding this parameter and setting the value to "nolink" will remove the href from post titles in the list and make them unclickable. Default: null.
* **class** Accepted Values: any valid HTML class attribute name or names (separate multiple class names with a space). Default: null.


= Document/File List =

`[accp_clientfiles]`

= File Shortcode Parameters =

* **categories** Accepted Values: this can be entered as a File Category Slug or ID, or a combination of those. Separate multiple values with a comma (ex. “21, category-a”). Default: null.
* **display_number** this is the number of posts per page. Accepted Values: any positive whole number. Default: -1 (which displays all posts returned in a query).
* **order_by** this allows for lists to be sorted by post title instead of date. Accepted Values: “title” or “date”. Default: “date”.
* **order** this allows the sort order to be changed. Accepted Values: “ASC” or “DESC”. Default: “DESC”.
* **show_excerpt** this allows for a post excerpt to be displayed with each list item. Accepted Values: “true” or “false”. Default: “false”.
* **excerpt_length** this allows you to constrain the number of words, “show_excerpt” is set to “true”. Accepted Values: any positive whole number. Default: null.
* **show_thumbnail** this allows the featured image to be displayed with each list item (if a featured image is set). Accepted Values: “true” or “false”. Default: null.
* **thumbnail_size** this allows you to choose an image size if the “show_thumbnail” attribute is set to “true”. Accepted Values: any valid thumbnail slug that is available in your theme (ex. “full”). Default: null.
* **align_thumbnail** this allows the thumbnail image alignment to be set if the “show_thumbnail” attribute is set to “true”. Accepted Values: “center”, “left”, “right”, “float-left”, or “float-right”. Default: null.
* **show_post_date** this allows for the WordPress post date to be displayed with each item in a list. Accepted Values: “true” or “false”. Default: “false”.
* **link_title** Accepted Values: “nolink”.  Adding this parameter and setting the value to "nolink" will remove the href from post titles in the list and make them unclickable. Default: null.
* **class** Accepted Values: any valid HTML class attribute name or names (separate multiple class names with a space). Default: null.

= Global Files (Pro) =

`[accp_global_files]`

= Company Menu (Pro) =

`[accp_company_menu]`

= Company Menu Shortcode Parameters =

* **excluded_page_ids** - this allows for pages that are assigned to a given company to be excluded from the company menu.  Accepts a comma separated list of Client Page ID's. Default = null.
* **list_style** - this allows the UL orientation to be changed.  Accepts 'vertical' or 'horizontal.' Default = horizontal.
* **align** - this allows for the menu alignment to be set.  Accepts 'left,' 'right,' or 'center.' Default = left.


== Documentation ==

* [Quick Start Guide](https://adrianrodriguezstudios.com/documentation-constellation-client-portal/#quick-start) - Follow the quick start guide to quickly set up the initial foundation for your client portal.
* [Plugin Documentation](https://adrianrodriguezstudios.com/documentation-constellation-client-portal/) - View the plugin documentation for other helpful information.

== Support ==

Email technical support is provided for active Pro licenses.

If you have installed the free version, or do not currently have an active Pro license, you can still view the [support forum](https://adrianrodriguezstudios.com/support-forum/), and/or post to the forum by registering and logging in.

== FAQ ==

= Can I provide private files and documents for my clients to access and download? =
Yes.  Constellation Client Portal is designed to allow you to attach files to private posts.  This allows you to present private  private files and/or private posts to clients.

= Can I provide private posts and information to my clients? =
Yes.  The client files and invoices allow you to upload/attach files (optional), as well as post freeform content via a WYSIWYG post editor.

= Can I only display client pages to a specific client when they are logged into my site? =
Yes.  By default only users that are logged in have access to client pages and files.  Further, only users that have been added to a specific company, are able to view pages and files for that company.

= Can I automatically redirect clients to their client page when they log into my site? =
Yes. The Pro version allows you to enable automatic login redirection and choose to redirect clients to their respective client page when they log in.  This option should only be enabled if you do not already have login redirection enabled through another plugin.

= Can I automatically change an invoice status to paid when an order is paid in WooCommerce? =
Yes. The Pro version allows you to enable functionality that automatically changes an invoice status to paid when it is paid in WooCommerce, or when the WooCommerce order status changes to "commpleted" (depending on your preference).

= Can I restrict access to specific documents and pages for specific users or roles? =
Yes.  The Pro version allows for more granular access restriction by specific user or role, as well as by category.

= Can I add file and invoice shortcodes to any WordPress page or post? =
No.  In order to provide better access restriction, file and invoice shortcodes can only be added to Client Pages, which are in turn assigned to specific companies/users.

= Can I customize the layout and design of client pages? = 
Yes.  Client pages can be laid out and designed like any other page in WordPress.

= Can the client page slug be changed to a custom slug name? = 
Yes. The [pro version](https://adrianrodriguezstudios.com/constellation-client-portal/) allows the client page, client file, and client invoice, post type slugs to be changed to a custom name of your choice.


= Can I use this plugin without installing WooCommerce? =
Yes. WooCommerce is not required, but the Pro version of the plugin does integrate with WooCommerce to allow invoice payments to be processed (optional setting).

= Will my theme's page builder work with client pages? =
This is dependent on the theme, and Constellation Client Portal has no control over this functionality.  However, some themes may work out of the box with client pages.  If your theme's page builder does not work with client pages, check with the theme developer to see if they have a method for extending the page builder to custom post types (the client page post type is "accp_client_pages").

= Can I customize the look of post lists displayed on the front-end? =
Yes.  The Pro version allows you to customize the look of each list independently.  The pro version also allows you to display post lists in list or grid layouts.

== Installation ==

= Automatic Install =

1. In the WordPress Admin section, navigate to Plugins.
2. Click Add New and select your plugin zip package.
3. Click Install.
4. Finally, click Activate when the plugin is installed.

= Manual Install =

1. Extract your plugin zip file.
2. Using an ftp program, open your wp-content/plugins directory.
3. Upload the uncompressed plugin folder to the wp-content/plugins directory.
4. In the WordPress Admin section, navigate to Plugins.
5. Locate the Constellation Client Portal plugin and click Activate.

== Changelog ==
= 1.9.0 (Pro) - 2024-10-1 =
* Update: Updated the syntax to check if wp_cron is disabled in the automated email settings.
* Fix: Fixed issue preventing global file lists from displaying multiple statuses (ex. "in progress" and "completed," etc.).
* Feature: Added functionality to allow companies to be created via CSV import.

= 1.9.0 (Core) - 2024-10-1 =
* Fix: Fixed issue preventing lists from displaying multiple statuses (ex. "in progress" and "completed," etc.).
* Update: Added additional tooltips to the user profile fields.

= 1.8.11 (Pro) - 2024-9-2 =
* Update: Made the company status row settings responsive.
* Update: Updated the company status row settings to make the default status rows read only.
* Update: Updated the company status functionality to restore the default statuses (active, pending, and inactive) on option save.

= 1.8.11 (Core) - 2024-9-2 =
* Update: Created new method for generating admin tooltips.
* Update: Code formatting updates.
* Improvement: Improved validation and checks in the assign-existing and generate-new company primary user AJAX functions.

= 1.8.10 (Pro) - 2024-8-1 =
* Update: Updated the csv import functionality to properly handle commas within cell data.
* Update: Updated the Redirect Public Home Page to Client Home Page settings instructions for better clarity.
* Improvement: Updated the redirect-away-from-home-page functionality to exit in cases where a user status is inactive or pending to prevent redirect loops.

= 1.8.10 (Core) - 2024-8-1 =
* Compatibility: Tested the plugin with WP 6.6.

= 1.8.9 (Pro) - 2024-7-1 =
* Improvement: Improved validation in the accp_initialize_wp_editor JS function.
* Fix: Fixed issue where a past due notice could be incorrectly displayed if no due date was set and past due notices were enabled in a shortcode list.
* Update: Made updates to the license activation/deactivation and plugin update functionality.
* Fix: Fixed issue that could cause new post notification emails to be sent on post save after the initial email has already been sent.
* Update: Updated deprecated WP get_terms syntax to be inline with current WP get_terms syntax in single shortcode settings.
* Update: Integrated slug sanitization with the global file slug override update functionality.
* Update: Improved nonce verification in the email class.
* Improvement: Improved the CSV export publish date filter.
* Fix: Fixed issue preventing the CSV due date filters from properly filtering the export rows.
* Update: Code formatting updates.
* Update: Improved escaping in the admin note output.

= 1.8.9 (Core) - 2024-7-1 =
* Update: Minor code formatting updates.

= 1.8.8 (Pro) - 2024-6-11 =
* Update: Updated the settings title function to properly display saved custom list titles.
* Fix: Fixed issue preventing some list settings from being saved correctly.
* Update: Updated the admin add new list button class param name.
* Update: Code formatting updates.
* Update: Updated the ars-constellation-client-portal-list-shortcode-styles-pro.css enqueue method to ensure that the proper version number is added.
* Update: Removed unneeded $content param from the company menu shortcode function.
* Update: Updated the accp-pro-public-style.css enqueue method to ensure that the proper version number is added.

= 1.8.8 (Core) - 2024-6-11 =
* Update: Cleared shortcode notice that surfaced in cases where “url” was not present in the attached file array.
* Update: Code formatting updates.
* Update: Added nonce field and check to user edit fields.
* Update: Converted is_writable check on the Site Info settings page to WP_Filesystem method.
* Update: Updated the get settings page title sanitization.
* Update: Added nonce verification to the allowed file type settings.

= 1.8.7 (Pro) - 2024-6-3 =
* Update: Code formatting updates.
* Improvement: Added nonce checks to the taxonomy add and edit forms for custom fields.
* Update: Fixed issue where excluded company column content was missing on a newly added global file category row after an AJAX add, and until a page refresh.
* Update: Updated the pro admin style enqueue function to properly set the plugin version as the style sheet version.
* Update: Corrected typo in company status repeater sanitize function.

= 1.8.7 (Core) - 2024-6-3 =
* Update: Code formatting updates.
* Update: Corrected typo in comment.

= 1.8.6 (Pro) - 2024-5-18 =
* Update: Updated the company status repeater row settings to allow spaces in the status label.
* Update: Removed redundant, legacy association of the company status setting repeater field with the general settings group to fix issues with company statuses being unexpectedly removed.
* Update: Updated the company status column functionality to account for no company statuses being provisioned.
* Update: Updated the link displayed in company posts when no company statuses are provisioned to the new company settings page URL (from the legacy settings URL).
* Fix: Fixed issue preventing company status rows from being saved after moving away from deprecated FILTER_SANITIZE_STRING PHP method that was used with filter_var_array.
* Fix: Fixed typo in client page save function.
* Update: Corrected typo in user field description.
* Fix: Updated the file upload function visibility to fix issue preventing files from being uploaded via the Quick Create UI.
* Update: Removed unneeded company WP list filter function from the global file class.
* Update: Fixed issue preventing selected WP list table status filter from being displayed as selected.

= 1.8.6 (Core) - 2024-5-18 =
* Update: Updated the functionality that adds default company statuses on plugin install to prevent an undefined array variable.
* Update: Code formatting updates.

= 1.8.5 (Pro) - 2024-5-10 =
* Update: Updated the multi_company_nav company menu shortcode feature to work with global client pages.
* Update: Updated date functionality in the CSV import class to use gmdate.
* Update: Updated the get contents method in the CSV import class to use WP filesystem method.
* Fix: Fixed issue preventing posts from being added via CSV import in certain versions of PHP.
* Update: Code formatting updates.

= 1.8.5 (Core) - 2024-5-10 =
* Update: Code formatting updates.

= 1.8.4 (Pro) - 2024-5-6 =
* Update: Code formatting updates.

= 1.8.4 (Core) - 2024-5-6 =
* Update: Updated full file path composition method in the post reassign UI to work better with PHP versions prior to PHP 8, and on sites that may be installed in a sub dir.
* Update: Updated the client page access check for better clarity.

= 1.8.3 (Pro) - 2024-5-1 =
* Update: Updated product meta field include functionality to clear preg_replace PHP 8.1+ deprecation warning.
* Update: Updated the front-end file download functionality to work with global files.
* Improvement: Updated the file post company reassign functionality to add a note to the post with details of the company reassignment and file attachment move/copy.

= 1.8.3 (Core) - 2024-5-1 =
* Update: Updated the settings add_sub_menu page functionality for compatibility with PHP 8.1+.
* Update: Improved admin post list company column validation.
* Update: Updated use of str_replace throughout for PHP 8.1+ compatibility.
* Update: Updated use of strpos throughout for PHP 8.1+ compatibility.
* Update: Converted instances of FILTER_SANITIZE_STRING to PHP 8.1+ compatible method throughout.
* Update: Added missing class vars throughout for PHP 8.1+ compatibility.
* Fix: Fixed issue preventing a primary user from being assigned to a company if the user did not have additional companies assigned.
* Improvement: Added functionality that gives users the option to overwrite duplicate files when specifying a new company dir and moving files.
* Update: Updated the assign and specify company dir contextual instructions.
* Update: Converted instances of mkdir to wp_mkdir_p in the core admin class.
* Update: Update: Deprecated the functionality that disabled zlib.output_compression.  This was meant for IE which has reached EOL, and downloads are working properly in Edge.
* Update: Added a nonce param to front-end file download links.
* Update: Added a new nonce field to all accp post type edit forms, for use in validation.
* Improvement: Created better validation for the post company reassign functionality to prevent the action from firing if the old company matches the new company.
* Improvement: Updated the post company reassign UI to provide better user feedback.

= 1.8.2 (Pro) - 2024-4-9 =
* Improvement: Constrained the Quick Create initialize editor function to only fire on necessary admin pages.
* Fix: Fixed save_post issue preventing pages and posts from being saved.

= 1.8.2 (Core) - 2024-4-9 =
* Compatibility: WordPress 6.5 compatibility check.
* Update: Began code formatting updates to conform code to WordPress coding standards.

= 1.8.1 (Pro) - 2024-4-1 =
* Update: Made the bulk create and export sections responsive.
* Feature: Created new post quick create feature that allows invoice, file, and global file posts to be batch created via a Quick Create UI.
* Improvement: Added better date format validation to the due date fields, along with user feedback for invalid date formats.
* Update: Added new parameters to the accp_company_menu shortcode to extend the functionality.
* Update: Corrected typo in client page company menu setting section.
* Update: Fixed broken link in the bulk create section description.
* Update: Reorganized the functionality that adds the Bulk Create section to WP list tables for better maintainability.
* Fix: Updated the csv export product total function to account for the new product quantity value to produce accurate product totals for quantities greater than one.
* Update: Created new csv export class and relocated related functions into the new class for better organization and maintainability.
* Update: Updated the functionality that adds pro WP list table columns for better organization and maintainability.
* Update: Fixed typo in reminder email button class, and in corresponding JS.
* Update: Deleted legacy, deprecated file access php file.
* Update: Added the accp_after_global_file_list_pagination action hook.
* Improvement: Improved sanitization and validation of the accp_update_allowed_post_types_for_global_files filter.

= 1.8.1 (Core) - 2024-4-1 =
* Update: Reworked the file upload functionality to work with new features.
* Update: Created new plugin dir path constant.
* Update: Updated the user profile field functionality for better organization and maintainability.
* Update: Updated the functionality that adds core WP list table columns for better organization and maintainability.
* Update: Code formatting updates.
* Update: Deprecated legacy settings function.

= 1.8.0 (Pro) - 2024-3-1 =
* Update: Updated the CSV import functionality to add warnings to import logs if company id or file attachment data is missing or malformed.
* Update: Added new Global Files section to the plugin settings.
* Update: Updated the list shortcode settings to account for the new global files.
* Feature: Added functionality that allows for global file posts to be created and displayed via a new accp_global_files shortcode.
* Update: Updated the global page functionality to deny access if any published company that a user is assigned to contains a status other than "active."
* Fix: Fixed issue preventing notes from being deleted.
* Update: Updated the WP list table delete page permanent delete admin notice text.
* Update: Updated the functionality that generates taxonomy list column content to improve organization and maintainability.
* Update: Updated the WP list table filter query functionality for better organization and maintainability.
* Update: Updated the functionality that generates the post WP list table Category column content to improve organization and maintainability.
* Update: Updated the functionality that generates the post WP list table Company column content to improve organization and maintainability.
* Update: Updated the excluded user and excluded role WP list table column content functionality for better organization and maintainability.
* Update: Added new global file post type and associated taxonomies.
* Improvement: Improved the client page, client invoice, and client file meta save functionality.
* Improvement: Improved capability checks and sanitization for the excluded user and excluded role sections for pages and posts.
* Improvement: Updated the functionality that saves the accp_client_file_categories, accp_client_invoice_categories, and accp_client_page_categories exclude user and exclude role taxonomy meta data for better organization and maintainability.
* Improvement: Updated the functionality that adds the exclude user and exclude role sections to the accp_client_file_categories, accp_client_invoice_categories, and accp_client_page_categories edit taxonomy page for better organization and maintainability.
* Improvement: Updated the functionality that adds the exclude user and exclude role sections to the accp_client_file_categories, accp_client_invoice_categories, and accp_client_page_categories add taxonomy form for better organization and maintainability.

= 1.8.0 (Core) - 2024-3-1 =
* Improvement: Improved the assign company directory settings instructions.
* Update: Updated the assign company dir functionality to prevent assigning the reserved "global-files" dir.
* Update: Updated the WP list table delete page permanent delete admin notice UI.
* Improvement: Updated the post bulk edit UI to provide better user feedback on button click.
* Fix: Removed functionality that automatically saves an 'unpaid' status to file posts if no value is saved.

= 1.7.6 (Pro) - 2024-2-1 =
* Update: Minor code formatting updates.

= 1.7.6 (Core) - 2024-2-1 =
* Update: Minor code formatting updates.
* Update: Wrapped all unwrapped shortcode examples in the readme file with backticks to prevent smart quotes from causing errors when cutting/pasting into WP.

= 1.7.5 (Pro) - 2024-1-8 =
* Feature: Added functionality that allows a pay-all total to be displayed in invoice lists on the front-end.
* Feature: Added functionality that allows itemized product lines to be displayed in invoice items on the front-end.
* Feature: Added functionality that allows invoice item totals to be displayed on the front-end.
* Update: Updated the CSV export functionality to include new assigned_product_qty and product_title_override columns and values.
* Update: Updated the CSV import functionality to include functionality for importing data to the new product quantity and title override invoice fields.
* Feature: Added new functionality that allows a product quantity to be specified in invoices.
* Feature: Added functionality to allow file and invoice post statuses to be displayed in front-end lists.

= 1.7.5 (Core) - 2024-1-8 =
* Update: Wrapped shortcode examples in the readme file with backticks to prevent smart quotes from causing errors when cutting/pasting into WP.

= 1.7.4 (Pro) - 2023-12-1 =
* Update: Updated shortcode list settings field labels to match labels referenced in documentation.
* Update: Updated the exclude user and exclude role select fields to not check for saved values on the add taxonomy pages.

= 1.7.4 (Core) - 2023-12-1 =
* Fix: Fixed incorrect settings page link in the WP plugin list item.
* Update: Updated the front-end file download functionality to provide better user feedback.
* Update: Updated the user profile settings addition function to verify that the user object exists to clear warnings on the WP new user creation form.
* Update: Updated the client page, client file, and client invoice taxonomy role capability requirements.
* Update: Disabled legacy settings JS tab handler functions.
* Fix: Updated the WP Admin plugin settings logo URL to fix display issue.

= 1.7.3 (Pro) - 2023-11-14 =
* Update: All core updates.

= 1.7.3 (Core) - 2023-11-14 =
* Update: Updated company, client page, file, and invoice post type capabilities to fix conflict with WordPress.com hosted single site instances (not multisite).
* Update: Updated the user profile tooltips to only display for admins.

= 1.7.2 (Pro) - 2023-11-8 =
* Compatibility: Tested with WP 6.4.
* Update: Updated the company status select functionality in the company status metabox.

= 1.7.2 (Core) - 2023-11-8 =
* Compatibility: Tested with WP 6.4.
* Update: Removed the status column in the core company WP list table, as company statuses are a pro feature.
* Update: Updated the status display functionality in the WP Admin company list.

= 1.7.1 (Pro) - 2023-11-1 =
* Update: Updated the documentation page content within settings.
* Fix: Updated the font-awesome fa-close to fa-times to fix missing close icons.
* Update: Added a header with instructions to the shortcode list settings page.
* Update: Updated plugin update PHP notice functionality.
* Fix: Updated the new post email notification function to fix undefined vars.
* Update: Updated the past due notice styling to fix inconsistent vertical alignment of the text.
* Fix: Fixed issue in which the thumbnail alignment class was not properly composed from the pro shortcode settings value.
* Update: Moved list shortcode specific CSS from the default pro public CSS file to a new dedicated pro list shortcode CSS file.
* Feature: Created functionality that allows front-end lists to be displayed in a grid layout.
* Feature: Created functionality that allows for styling of front-end file and invoice lists.
* Update: Created new theme settings page that allows for list shortcode theme settings to be saved and retrieved.
* Update: Added functionality to dynamically add the pro list shortcode CSS to the page to allow for list UI styles to be updated in the shortcode settings.

= 1.7.1 (Core) - 2023-11-1 =
* Update: Updated the add default company statuses on activation functionality to clear duplicate status message on plugin activation.
* Update: Updated the list shortcode thumbnail float-left styling.
* Fix: Fixed typo in file center thumb alignment CSS rule name.
* Update: Moved list shortcode specific CSS from the default public CSS file to a new dedicated list shortcode CSS file.
* Update: Added functionality to dynamically add the default list shortcode CSS to the page.

= 1.7.0 (Pro) - 2023-10-2 =
* Update: Updated the invoice pay all functionality to leave the button disabled on AJAX success (through redirection to the cart).
* Update: Deprecated accp-file-list-shortcode-atts.php.
* Update: Deprecated accp-file-list-shortcode-vars.php.
* Update: Deprecated accp-invoice-list-shortcode-atts.php.
* Update: Deprecated ars-constellation-invoice-list-shortcode-vars.php.
* Update: Deprecated accp-file-list-due-date-past-due.php.
* Update: Deprecated accp-file-list-loop-vars.php.
* Update: Deprecated accp-file-list-pay-button.php.
* Update: Deprecated accp-invoice-list-due-date-past-due.php.
* Update: Deprecated accp-invoice-list-loop-vars.php.
* Update: Deprecated accp-invoice-list-pay-all-button.php.
* Update: Deprecated accp-invoice-list-pay-button.php.
* Update: Deprecated the accp-invoice-list-shortcode-includes dir.
* Update: Deprecated the accp-file-list-shortcode-includes dir.
* Update: Deprecated the premium admin dir as this will not be used going forward.
* Feature: Added functionality to allow shortcodes to be genarated and saved as settings for easier implementation and editing.
* Update: Overhauled the pro file and invoice list shortcode framework for better maintainability and scalability, and for forthcoming features.

= 1.7.0 (Core) - 2023-10-2 =
* Improvement: Updated the sanitize strings in array function to account for incorrect and null values.
* Update: Overhauled the core file and invoice list shortcode framework for better maintainability and scalability.
* Update: Deprecated the “list_id” shortcode param.  This now uses the auto-generated “data-list-id" value.  This is also not needed in the new pro saved shortcode framework.
* Update: Updated select2 to select2.full.
* Update: Updated Font Awesome to enqueue from a local plugin dir instead of via the CDN.  Also updated the Font Awesome version.
* Update: Updated the add settings sub page function to exit early for non-admin users to prevent notices on WP Admin for non-admin users.

= 1.6.0 (Pro) - 2023-9-1 =
* Feature: Added new settings and functionality to allow the public home page to be redirected to the client home page.
* Improvement: Updated the pay button spinner css to prevent themes and plugins from overriding the border colors.
* Update: Overhauled the pro settings framework for better scalability and maintainability.

= 1.6.0 (Core) - 2023-9-1 =
* Improvement: Updated the shortcode download link construction to work better with WP instances that are installed in a sub directory.
* Improvement: Created new internal register setting option for use in cleaning saved options on plugin deletion.
* Update: Overhauled the core settings framework for better scalability and maintainability.

= 1.5.10 (Pro) - 2023-8-1 =
* Improvement: Improved the company status settings new status row functionality.
* Update: Deprecated unused function in pro settings.
* Feature: Added new filter to allow users to change the default client file and invoice URL base name.
* Feature: Added new settings and functionality to allow users to change the default client file and invoice URL base name.
* Update: Fixed issue with the duplicate post type slug check for better accuracy.

= 1.5.10 (Core) - 2023-8-1 =
* Update: Made updates to the install/uninstall file.
* Update: Made updates to the client invoice and client file post type args, and the client invoice category, client invoice tag, client file category, client file tag custom taxonomy args.
* Update: Minor css updates for the settings UI.
* Update: Made updates to the client page category and client page tag custom taxonomy args.

= 1.5.9 (Pro) - 2023-7-3 =
* Fix: Updated the quick edit functionality related to the Reminder Email column in file and invoice WP list tables to prevent loss of the column after a quick edit (AJAX) update.
* Feature: Added new filter to allow users to change the default client page URL base name.
* Feature: Added new settings and functionality to allows users to change the default client page URL base name.

= 1.5.9 (Core) - 2023-7-3 =
* Fix: Fixed issue preventing the new user generation section within WP admin company pages from generating new users.
* Improvement: Created better method for displaying admin notices.
* Update: Made minor updates to the file and invoice shortcodes.

= 1.5.8 (Pro) - 2023-6-1 =
* Fix: Updated the pro public facing functions to check for WooCommerce to prevent errors if WooCommerce is not installed/active.
* Feature: Added global page functionality that allows client pages to be accessed by multiple companies.
* Update: Updated add client page functionality to allow global pages to be created in the client page quick-create section within company edit pages.
* Update: Updated the client page post meta save functionality to exit early on new, unsaved posts to clear notices related to the post object not yet existing.

= 1.5.8 (Core) - 2023-6-1 =
* Update: Relocated select2 script related to the company select field within client pages from the pro admin js file to the core admin js file for UI consistency.
* Improvement: Improved the client page quick-create functionality within company edit pages.
* Improvement: Added functionality to remove assigned companies from user profiles when the company posts are permanently deleted in WP.
* Fix: Fixed issue preventing client company posts from being permanently deleted via the WP list table bulk actions.
* Improvement: Updated the company and additional company select fields/functionality in user profile edit pages to include all post statuses in the select fields (not just published companies) to accommodate more customer workflows.
* Improvement: Added a new Assigned Companies column to the WP user list table to allow users to quickly see company assignments at a glance.
* Improvement: Updated the client information section within WP Admin user profile edit pages. Also added tooltips to provide better contextual information.
* Update: Made minor update to improve the new user creation process when creating a new company.

= 1.5.7 (Pro) - 2023-5-1 =
* Update: Fixed visibility issue with the due date datepicker month navigation elements.
* Improvement: Updated the manual reminder email functionality to return a message in the AJAX response if the company does not have a primary user assigned.
* Improvement: Updated the new post email functionality to add a note to the post if the company does not have a primary user assigned to provide better guidance to the user.
* Fix: Updated the accp_update_automated_email_cron_on_option_change function to not pass any vars, as the function is used for both the add_option and update_option WP hooks. This fixes fatal errors thrown when saving email settings for the first time (where no options exist), which triggered the add_option_ hooks with the incorrect vars.
* Improvement: Updated pro authorization functionality for better organization, performance, and maintainability.
* Fix: Fixed issue that intermittently prevented non-admin users from accessing files directly in cases where excluded users and roles were empty for a given post.
* Update: Deprecated accp-file-list-loop-vars.php and accp-invoice-list-loop-vars.php.
* Update: Made minor improvements to the settings license tab.
* Update: Removed target _blank in the license key reminder message in the WP plugin list.
* Update: Updated the license activation, deactivation, and plugin update functionality to verify that the user is both logged in and has plugin update capabilities.

= 1.5.7 (Core) - 2023-5-1 =
* Update: Increased the the max number of characters to 30 in the new user generation password field within company edit pages for improved password strength.
* Update: Updated the primary user select and new user creation functionality within new company post edit pages to work properly with Gutenberg even if the new post has not yet been saved.
* Improvement: Updated core authorization functionality for better organization, performance, and maintainability.
* Improvement: Added functionality to check user capabilities and enable show_in_rest for accp_client_page_categories and accp_client_page_tags taxonomies for admins (to enable Gutenberg support), but disable public access to these taxonomies via the REST API.
* Improvement: Updated the core authorization checks to evaluate the client_status user meta and allow/deny access based on the value.
* Improvement: Created new core and pro authorization classes for better organization and maintainability, and integrated the functionality with the file and invoice shortcodes.
* Update: Updated the shortcodes to explicitly exit if is_admin to ensure that shortcode functionality is only executed on the front-end.
* Improvement: Updated the accp_align_thumbnail_var function to improve organization and maintainability.

= 1.5.6 (Pro) - 2023-4-3 =
* Update: Minor update to a plugin update notice that is returned if plugin data is not returned via api.
* Improvement: Updated the code associated with the excluded users and excluded roles sections within Client File, Client Invoice, and Client Page edit screens for better organization and maintainability.
* Fix: Fixed issue preventing saved excluded role values from being displayed correctly within the Select2 select field within Client File, Client Invoice, and Client Page edit screens.
* Update: Added note to the Email Notifications settings page.
* Update: Added functionality to display a UI prompt on the license tab if the license key is not entered.
* Update: Added functionality to check if the license key has been entered and display a reminder in the plugin list if not.
* Update: Updated the license settings functionality to clear notices in cases where no license_data is present.
* Fix: Updated the accp_change_invoice_status_on_woocommerce_payment_complete function to ensure that the invoice post link referenced in order notes works as expected in environments where get_edit_post_link does not work as expected.

= 1.5.6 (Core) - 2023-4-3 =
* Fix: Updated the accp_generate_invoice_query_args and accp_generate_file_query_args tax_query field values to "id" instead of "slug" as the $categories var is passed in as an array of category ID's rather than category slugs in each of those functions.
* Fix: Updated the accp_categories_var function to properly convert the $atts['categories'] value to an array if it is set in the shortcode.
* Update: Created a new function to generate the client file upload dir rewrite regex path for better organization and easier use within the main admin file and within the deactivate function.
* Update: Updated the plugin activation rewrite addition and flush_rewrite_rules functionality for better reliability.
* Update: Updated the plugin deactivation flush_rewrite_rules functionality for better reliability.
* Update: Updated the functionality that adds the client role on plugin activation to ensure that the role is added properly if it does not already exist.
* Improvement: Updated the activation process to check for another active version of Constellation Client Portal before activating the plugin to improve the upgrade process. Also added functionality to display a nicer message indicating that the duplicate plugin needs to be deactivated before proceeding.

= 1.5.5 (Pro) - 2023-3-1 =
* Improvement: Updated the plugin activation functionality to check for active scheduled emails and reschedule the accp_automated_email_cron cron job on plugin activation if any are enabled.
* Improvement: Updated the plugin deactivation accp_automated_email_cron cron unschedule functionality.

= 1.5.5 (Core) - 2023-3-1 =
* Improvement: Updated the plugin deactivation flush rewrite functionality.
* Improvement: Updated the accp_client_pages post type capabilities to require admin capabilities to view, edit, and delete in WP Admin.
* Improvement: Added functionality to check user capabilities and enable show_in_rest for accp_clientcompany and accp_client_pages post types for admins (to enable Gutenberg support), but disable public access to these post types via the REST API.

= 1.5.4 (Pro) - 2023-2-9 =
* Update: Corrected typo in the pro email settings section.
* Improvement: Updated the CSV import functionality to verify that the assigned company ID is associated with a valid company and return a warning if not.
* Improvement: Updated the CSV import functionality to assign a default post status of draft if none was provided and post a warning to the import log.

= 1.5.4 (Core) - 2023-2-9 =
* Improvement: Added new id param to the file and invoice shortcodes to allow an ID attribute to be added to the list container div.
* Improvement: Added new class param to the file and invoice shortcodes to allow additional classes to be added to the list container div.
* Update: Updated the parameter order in the accp_update_dir_name_in_file_attachments function to clear optional parameter warnings in PHP 8.
* Update: Updated the parameter order in the accp_load_requested_file function to clear optional parameter warnings in PHP 8.
* Update: Updated the file shortcode to accept the file_status param and adjust the output accordingly.

= 1.5.3 (Pro) - 2023-1-9 =
* Update: Updated the deactivate function to clear the accp_automated_email_cron cron job if it is scheduled on plugin deactivation.
* Fix: Fixed notice relating to undefined company_id variable in class-ars-constellation-client-portal-pro-public.php.
* Fix: Fixed issue that prevented Published, Drafts, and Trash folders from displaying in File and Invoice posts lists.
* Feature: Added new functionality that allows file and invoice posts to be created in bulk via a CSV import.
* Improvement: Extended login and WP capability checks for admin settings pages.
* Improvement: Updated the note metabox functionality to only display the note form after the post has been saved to prevent confusion.
* Update: Improved the Send Reminder admin list button UI to wrap at smaller window widths.
* Improvement: Added functionality to display a product_total column within invoice CSV exports.
* Update: Relocated accp_save_invoice_cart_item_meta_as_order_item_meta, accp_change_invoice_status_on_woocommerce_payment_complete, accp_change_invoice_status_on_woocommerce_order_status_complete, and accp_change_invoice_status_to_completed functions and hooks from public to admin files.
* Improvement: Added functionality to add the Woocommerce order number as attribution within invoice notes when they are automatically marked as paid by Woocommerce.

= 1.5.3 (Core) - 2023-1-9 =
* Update: Made updates to the admin settings upgrade tab.
* Improvement: Added new link_title file and invoice shortcode attribute that removes the link from post titles in the loop if the attribute is set to nolink.
* Improvement: Added a role select field to the new primary user create form in the Company UI and updated the AJAX function to assign the role on user creation.
* Improvement: Added a "Company ID" column to the Company WP list table for easy access to the Company/post ID.
* Improvement: Extended login and WP capability checks for admin settings pages.

= 1.5.2 (Pro) - 2022-10-13 =
* All Core 1.5.2 updates.

= 1.5.2 (Core) - 2022-10-13 =
* Update: Moved the Select2 script related to the client-add-company-select user field to the core admin js file from the pro admin js file so that the Select2 UI works as expected in the core plugin.
* Update: Updated the functionality that checks for saved additional assigned companies in the extra_user_profile_fields function to ensure that an array is returned if no companies are assigned, to prevent errors on user edit pages in PHP 8.

= 1.5.1 (Pro) - 2022-10-10 =
* Improvement: Added new accp_update_login_redirect_url filter to allow developers to change the login redirect URL.
* Improvement: Updated the login redirection functionality to verify that a company has a home page saved before determining the login redirect destination.

= 1.5.1 (Core) - 2022-10-10 =
* Update: Minor code formatting updates.
* Update: Updated the logo displayed on the settings page.

= 1.5.0 (Pro) - 2022-8-22 =
* Update: Updated the new post notification email functionality to not add a note to the post if the email is disabled globally.  Prevents needless "there was a problem sending the post notification email" notes in posts.
* Feature: Added new email template hooks for the new automated client emails.
* Feature: Added functionality to allow automated reminder emails to be sent via WP cron for file and invoice posts.
* Feature: Added functionality to allow file list exports to be filtered by status.
* Feature: Added functionality to allow the file list to be filtered by file status.

= 1.5.0 (Core) - 2022-8-22 =
* Feature: Added a new accp_define_file_statuses filter to allow file statuses to be edited.
* Fix: Fixed issue preventing Invoice statuses from being updated via Bulk Edit in the WP list table.
* Feature:  Added functionality to allow file statuses to be updated via Quick Edit and Bulk Edit in the WP list table.
* Feature: Added a Status column to the Client File WP list table.
* Feature: Added functionality that allows statuses to be assigned to Client Files.

= 1.4.0 (Pro) - 2022-6-7 =
* Feature: Added new "accp_update_invoice_prohibited_product_types" filter to allow the prohibited product type list to be edited.
* Feature: Added new "accp_past_due_text" filter to allow past due text to be changed in file and invoice lists.
* Feature: Added new "accp_invoice_already_added_link_text" filter to allow "Proceed to payment" text to be edited in invoice lists.
* Feature: Added new "accp_invoice_already_added_text" filter to allow "Item already added" text to be edited in invoice lists.
* Feature: Added new "accp_before_pay_all_button" hook to allow content to be added before the pay all button in invoice lists.
* Feature: Added new "accp_after_pay_all_button" hook to allow content to be added after pay all button in invoice lists.
* Feature: Added new "accp_before_single_pay_button" hook to allow content to be added before single pay buttons in invoice lists.
* Feature: Added new "accp_after_single_pay_button" hook to allow content to be added after single pay buttons in invoice lists.

= 1.4.0 (Core) - 2022-6-7 =
* Update: Added a "No Change" option to the invoice status Quick Edit select field since the post id is not accessible via the quick_edit_custom_box hook without a script.  This prevents confusion about the saved invoice status in the Quick Edit form.
* Feature: Added functionality to allow invoice statuses to be updated via the bulk edit form.

= 1.3.0 (Pro) - 2022-5-11 =
* Feature: Added functionality to allow file and invoice lists to be exported to CSV.

= 1.3.0 (Core) - 2022-5-11 =
* Feature: Added new accp_before_file_list_item hook to allow content to be added before the file list item.
* Feature: Added new accp_before_invoice_list_item hook to allow content to be added before the invoice list item.
* Feature: Added new accp_after_file_list_item hook to allow content to be added after the file list item.
* Feature: Added new accp_after_invoice_list_item hook to allow content to be added after the invoice list item.
* Feature: Added new accp_file_list_item_top_inside hook to allow content to be added to the top of the file list item.
* Feature: Added new accp_invoice_list_item_top_inside hook to allow content to be added to the top of the invoice list item.
* Feature: Added new accp_file_list_item_bottom_inside hook to allow content to be added to the bottome of the file list item.
* Feature: Added new accp_invoice_list_item_bottom_inside hook to allow content to be added to the bottom of the invoice list item.
* Feature: Added new accp_after_file_list_pagination hook to allow content to be added after file list pagination.
* Feature: Added new accp_after_invoice_list_pagination hook to allow content to be added after invoice list pagination.
* Feature: Added new accp_before_file_list hook to allow content to be added before file lists.
* Feature: Added new accp_after_file_list hook to allow content to be added after file lists.
* Feature: Added new accp_before_invoice_list hook to allow content to be added before invoice lists.
* Feature: Added new accp_after_invoice_list hook to allow content to be added after invoice lists.

= 1.2.0 (Pro) - 2022-4-8 =
* Update: Updated the email notification note functionality to add the recipient's address to the note instead of the current_user's address.
* Improvement: Added functionality to post company notes when changes are made to the upload directory.
* Update: Updated the file and invoice list category filter to display the select field even when no categories are provisioned (to prevent the select field from collapsing).

= 1.2.0 (Core) - 2022-4-8 =
* Feature: Added functionality to allow a primary user to be created and assigned within new Company posts to improve onboarding workflow.
* Feature: Added functionality to allow a specific (existing) company upload directory name to be set.
* Feature: Added functionality to allow the company upload directory to be updated after the directory has been set.
* Feature: Added functionality to allow the upload directory names to be changed or specified.
* Improvement: Reworked the duplicate upload directory assignment functionality to display a notice within Companies.

= 1.1.0 (Pro) - 2022-3-21 =
* Update: Updated the reminder email column and metabox functionality to only display if the respective reminder emails are enabled.
* Improvement: Added functionality to add a transactional note to the post when an invoice is automatically marked as paid when paid in WooCommerce.
* Improvement: Added functionality to add a transactional note to the post when a new or reminder email notification is sent.
* Improvement: Added functionality to remove post notes when the associated post is permanently deleted.
* Improvement: Added pagination functionality to the admin post note lists.
* Feature: Added functionality to allow notes to be added to Company, Invoice, and File post types.
* Feature: Added added new hooks to the email notification templates.
* Fix: Adjusted the File and Invoice category list filter to display even if no posts are assigned to categories (fixes collapsed select field when empty).
* Feature: Added functionality to allow email reminders to be manually sent within File and Invoice post lists.
* Feature: Added functionality to allow email reminders to be manually sent within File and Invoice posts.
* Feature: Added client email notification template functionality to allow email customization.
* Feature: Added new Email setting page to allow new and reminder email options to be set.
* Feature: Added functionality that allows global email notification settings to be overridden within individual File and Invoice posts.
* Update: Added new ARS_Constellation_Client_Portal_Pro_Email class to handle email notification functionality.

= 1.1.0 (Core) - 2022-3-21 =
* Feature: Added functionality to allow a Primary User to be assigned within a Company.

= 1.0.9 (Pro) - 2022-3-3 =
* Feature: Added functionality to allow the File WP Admin list table to be filtered by File Category.
* Feature: Added functionality to allow the Invoice WP Admin list table to be filtered by Invoice Category.
* Feature: Added functionality to allow the Invoice WP Admin list to be filtered by invoice status.
* Update: Updated the View Details link on the WP Admin Updates Dashboard.

= 1.0.9 (Core) - 2022-3-3 =
* Improvement: Added functionality to allow the Invoice status to be changed via the Quick Edit menu in the Invoice list table.
* Improvement:  Added an upload directory check on new Company post meta add/update to prevent the same upload dir from being assigned to more than one company if a post duplication process (i.e. plugin) is used to duplicate a Company post.
* Feature: Added functionality to allow File and Invoice lists to be filtered by company in WP Admin.
* Improvement: Created accp_get_defined_invoice_statuses function to allow for invoice statuses to be dynamically populated.
* Feature: Added a new accp_define_invoice_statuses filter to allow for the default invoice statuses to be edited.

= 1.0.8 (Pro) - 2022-2-1 =
* Feature: Added a new [accp_company_menu] shortcode that allows all pages assigned to a company to be conditionally displayed in a menu on Client pages.
* Update: Updated the plugin update functionality to properly escape the package url.

= 1.0.8 (Core) - 2022-2-1 =
* Feature: Added the ability to generate and assign a new Client Page from a Company page to improve onboarding workflow.
* Update: Added a Quick Start menu item to the plugin row action menu.
* Update: Added a Documentation item to the plugin row meta.
* Update: Added Quick Start and Documentation links to the plugin settings page.

= 1.0.7 (Pro) - 2022-1-22 =
* Update: Updated the invoice pay-all functionality to only add invoice ID's to the payable list that have a WooCommerce product ID saved to the post.

= 1.0.7 (Core) - 2022-1-22 =
* Update: Changed the my_company_page shortcode name to accp_my_company_page ensure compatibility.
* Update: Changed the clientinvoices shortcode name to accp_clientinvoices ensure compatibility.
* Update: Changed the clientfiles shortcode name to accp_clientfiles ensure compatibility.
* Update: Changed the client_page_tags taxonomy to accp_client_page_tags to ensure compatibility.
* Update: Changed the client_page_categories taxonomy to accp_client_page_categories to ensure compatibility.
* Update: Changed the client_company_categories taxonomy to accp_client_company_categories to ensure compatibility.
* Update: Changed the file_tags taxonomy to accp_file_tags to ensure compatibility.
* Update: Changed the clientfile post type slug to accp_clientfile to ensure compatibility.
* Update: Changed the client_pages post type slug to accp_client_pages to ensure compatibility.
* Update: Changed the clientcompany post type slug to accp_clientcompany to ensure compatbility.
* Update: Changed the file_categories taxonomy to accp_file_categories to ensure compatibility.

= 1.0.6 (Core) - 2022-1-19 =
* Update: Added basic styling to the list pagination element.
* Fix: Fixed issue preventing download of new files in the list after the recent code updates caused malformed paths.
* Update: Added functionality to flush the rewrite rules on plugin upgrade.
* Feature: Added excerpt support and functionality to the clientfile and accp_clientinvoice post types.
* Update: Updated the the file and invoice shortcodes to pull from the excerpt field, instead of the content field, if excerpts are enabled.
* Improvement: Updated static references to WP dirs and updated functionality to get this data dynamically.
* Change: Relocated the clientfiles upload directory from 'wp-content/accp-clientfiles' to 'wp-content/uploads/accp-clientfiles', and updated all associated functionality.
* Fix: Fixed issue preventing admin file uploads caused by malformed form tags in accp_update_post_edit_form_tag, caused after escaping output.
* Change: Removed class-ars-constellation-client-portal-client-list-table.php as this is not in use.
* Update: Added new ACCP_Utility_Functions class.

= 1.0.5 (Core) - 2022-1-6 =
* Fix: Cleared php warnings associated with the invoice and file lists that were thrown when the user additional_assigned_company array is null.
* Change: Reworked the direct access file protection framework to work within the native WP framework in order to remove the need to include core WP files in an external file-check file.
* Improvement: Reviewed all files and sanitized input and get data.
* Improvement: Reviewed all files and escaped output data.

= 1.0.4 (Core) - 2022-1-3 =
* Change: Updated the file output functionality.
* Change: Updated the file and invoice list shortcode CSS classes.
* Feature: Updated the invoice add-to-cart functionality to support simple and variable subscription products.
* Improvement: Updated the admin invoice product select options to exclude variable products and subscriptions (not variations) since top-level variable products cannot be added to the cart.
* Improvement: Added additional options to the invoice status select field.
* Improvement: Added a discount code to the base plugin upgrade tab.
* Update: Added an upgrade link to the base plugin list item in the WP plugin list.
* Update: Added an Upgrade tab to the settings page in the base plugin.

= 1.0.3 (Core) - 2021-12-20 =
* Improvement: Converted the invoice product ID field to a WooCommmerce product select field for easier product selection and designation within invoices.
* Improvement: Updated the pagination functionality in both the Invoice and File shortcodes to account for multiple lists on the same page.

= 1.0.2 (Core) - 2021-10-25 =
* Change: Updated the pay-all functionality to only display on shortcodes that are set to display "unpaid" invoices.
* Change: Made minor updates to the update functionality.
* Change: Disabled file management functionality.
* Change: Disabled the Client List admin page.
* Fix: Fixed issue with the auto mark invoices as paid and pay all admin settings being intermittently reset to default.

= 1.0.1 (Core) - 2021-10-21 =
* Feature: Added functionality to allow all invoices to be added to the cart at once when muliple invoices are returned by a shortcode.
* Feature: Added functionality to allow invoices to be automatically marked as paid when paid in Woocommerce, or when the Woocommerce order status is set to completed.
* Imporovement: Adjusted the invoice payment css to improve clickability.
* Change: Updated core functionality to support new pro and premium pay-all and auto-mark-as-paid functionality.

= 1.0.0 (Core) - 2021-9-1 =
* Feature: Added functionality to further resrict file and invoice access within a company by user and role.
* Feature: Added functionality to restrict file and invoice category access by user and role.
* Feature: Added functionality to allow for adding due dates to client files and invoices.
* Feature: Added functionality to display a past due notice on client files and invoices that have a due date.
* Improvement: Added additional shortcode parameters for more flexibility in displaying client files and invoices.
* Feature: Added functionality to add statuses to companies.
* Feature: Added functionality to redirect users to their company page at login.
* Feature: Added functionality to allow a client invoice to be assigned to a WooCommerce product (to allow an add-to-cart button to be displayed on the front-end).
* Feature: Added functionality to allow Companies to be created and to allow users to be assigned to companies.
* Feature: Added functionality to allow Client Pages to be created and assigned to companies.
* Feature: Added functionality to allow Client Files to be created and assigned to a Company.
* Feature: Added functionality to allow Client Invoices to be created and assigned to a Company.
* Feature: Added functionality to restrict direct access to files in the client file directory if the user is not assigned to a company that has access to the file.
* Feature: Added functionality to display client file lists via shortcode.
* Feature: Added functionality to display client invoice lists via shortcode.
* Feature: Added functionality enable/disable allowed file types for upload.
* Improvement: Added functionality to the settings page to verify that the accp-clientfiles directory is writable.
* Improvement: Added functionality to allow files and invoices to be reassigned to another company.