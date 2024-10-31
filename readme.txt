=== Plugin Name ===
Contributors: rpayne7264
Tags: pediapress, pediapress embed, pediapress lead capture,free lead capture
Requires at least: 3.0
Tested up to: 4.8
Stable tag: 1.0.5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

RDP PediaPress Embed lets you embed book content from PediaPress.

== Description ==

RDP PediaPress Embed will pull content from PediaPress book pages and embed it in pages and posts.

RDP PediaPress Embed also allows lead capture capabilities, utilizing free PediaPress ebooks as a bribe.
The default behavior is to display a pop-up light box, which will display whatever text, HTML, and/or shortcode you desire.

Your site will have an RSS feed for all books on the site, as well.


= Known Issues =

* CSS clashes with Lightbox Plus Colorbox plug-in


= Sponsor =

This plug-in brought to you through the generous funding of [Laboratory Informatics Institute, Inc.](http://www.limsinstitute.org/)


== Installation ==

= From your WordPress dashboard =

1. Visit 'Plugins > Add New'
2. Search for 'RDP PediaPress Embed'
3. Click the Install Now link.
3. Activate RDP PediaPress Embed once it is installed.


= From WordPress.org =

1. Download RDP PediaPress Embed zip file.
2. Upload the 'rdp-pediapress-embed' directory from the zip file to your '/wp-content/plug-ins/' directory, using your favorite method (ftp, sftp, scp, etc...)
3. Activate RDP PediaPress Embed from your Plugins page.


= After Activation - Go to 'Settings' > 'RDP PPE' and: =

1. Set configurations as desired.
2. Click 'Save Changes' button.
3. Go to 'Settings' > 'Permalinks' and click the 'Save Changes' button so the custom RSS feed will work.


== Usage ==

= PediaPress Book =
Use the shortcode [rdp-pediapress-embed] for embedding PediaPress book pages. The following arguments are accepted:

* url: (required) the web address of the PediaPress book that you want to embed on this page
* download_url: the web address to the ebook or the download page
* toc_show: 0 (zero) to hide table of contents (TOC) or 1 to show
* toc_links: Enabled â€” TOC links are enabled; Logged-in â€” TOC links are active only when a user is logged in; Disabled â€” TOC links are completely disabled, all the time
* image_show: 0 (zero) to hide cover image or 1 to show 
* title_show: 0 (zero) to hide book title or 1 to show 
* full_title: 1 to display book titles as combination of Title and Subtitle or 0 (zero)
* subtitle_show: 0 (zero) to hide book sub-title  or 1 to show 
* editor_show: 0 (zero) to hide book editor or 1 to show 
* language_show: 0 (zero) to hide book language or 1 to show 
* book_size_show: 0 (zero) to hide book size or 1 to show 
* add_to_cart_show: 0 (zero) to hide Add-to-Cart button or 1 to show 
* add_to_cart_text: text for Add-to-Cart button
* add_to_cart_size: size of the Add-to-Cart button - small, medium, large
* add_to_cart_color: color of the Add-to-Cart button - blue, creme, grey, orange, red
* cta_button_show: 0 (zero) to hide Call-to-Action button or 1 to show 
* cta_button_text: text for Call-to-Action button
* cta_button_size: size of the Call-to-Action button - small, medium, large
* cta_button_color: color of the Call-to-Action button - blue, creme, grey, orange, red


= Examples =

Create a lead capture popup:

[rdp-pediapress-embed url='https://pediapress.com/books/show/f64f601dd8d018e8a3b1164d847dce/' download_url='http://example.com/pdf/ebook.pdf'][gravityform id="1" title="true" description="true"][/rdp-pediapress-embed]


Create a direct download button:

[rdp-pediapress-embed url='https://pediapress.com/books/show/f64f601dd8d018e8a3b1164d847dce/' download_url='http://example.com/pdf/ebook.pdf']


Default settings will create a book page with a Table of Contents (TOC) that has disabled links. To create a book page that requires visitors to be logged in for the TOC links to work, you would set the toc_links attribute to "logged-in":

[rdp-pediapress-embed url='https://pediapress.com/books/show/f64f601dd8d018e8a3b1164d847dce/'  download_url='http://example.com/pdf/ebook.pdf' toc_links='logged-in']



= PediaPress Gallery =
Embedding a PediaPress gallery of books is implemented using the shortcode [rdp-pediapress-embed-gallery]. It accepts the following arguments:

* col: (required) number of columns to display per page
* num: (required) number of books to display per page
* size: accepted values - small, medium, large - dictate image and font size of gallery elements
* cat: comma separated list of category id numbers books must belong to
* tag: comma separated list of tag id numbers books must belong to
* sort_col: column name by which to sort books
* sort_dir: direction to sort books (ASC / DESC)
* full_title: 1 to display book titles as combination of Title and Subtitle or 0 (zero)
* image_show: 0 (zero) to hide cover images or 1 to show 
* title_show: 0 (zero) to hide book titles or 1 to show 
* subtitle_show: 0 (zero) to hide book sub-titles  or 1 to show 
* editor_show: 0 (zero) to hide book editors or 1 to show 
* language_show: 0 (zero) to hide book languages or 1 to show 
* book_size_show: 0 (zero) to hide book sizes or 1 to show 


= Examples =

[rdp-pediapress-embed-gallery col='3' num='3']

[rdp-pediapress-embed-gallery col='2' num='10' cat='5' tag='7,8' sort_col='post_date' sort_dir='DESC']


== Screenshots ==

1. Embedded PediaPress book page
2. Gallery of PediaPress books
3. Media button to launch shortcode embed helper form
4. PediaPress book shortcode embed helper form
5. PediaPress gallery shortcode embed helper form

== Change Log ==

= 1.0.5 =
* refactored code to utilize subtitle as part of cover image file name

= 1.0.4 =
* refactored code to use curl and tmp/cookies.txt to satisfy PediaPress cookie capabilities check
* bug fix
* added rel="noindex, nofollow" to TOC links

= 1.0.3 =
* Security update

= 1.0.2 =
* Added shortcode to render RSS as gallery
* Update gallery code to handle books added as both posts and pages

= 1.0.1 =
* Update TOC link options
* Update shortcode pop-up script - removed validation check of download URL

= 1.0.0 =
* Initial RC

== Upgrade Notice ==

== Other Notes ==

== External Scripts Included ==
* jQuery ColorBox Plugin v1.3.20.2 under MIT License


== Hook Reference: ==

= rdp_ppe_scripts_enqueued =

* Param: none
* Fires after enqueuing plug-in-specific scripts and styles


= rdp_ppe_book_scripts_enqueued =

* Param 1: array containing shortcode attributes
* Param 2: null or string containing content of enclosed shortcode
* Fires after enqueuing book-specific scripts and styles


= rdp_ppe_gallery_scripts_enqueued =

* Param 1: array containing shortcode attributes
* Param 2: null or string containing content of enclosed shortcode
* Fires after enqueuing gallery-specific scripts and styles


== PHP Filter Reference: ==

= rdp_ppe_allow_shortcode =

* Param 1: boolean indicating if shortcode is allowed to execute - default: true
* Param 2: array containing shortcode attributes
* Param 3: null or string containing content of enclosed shortcode
* Return:  boolean indicating if shortcode is allowed to execute


= rdp_ppe_book_main_content_classes =

* Param: String containing class names for the #rdp-ppe-main container when displaying a single book
* Return: class names for the #rdp-ppe-main container when displaying a single book


= rdp_ppe_before_meta_open =

* Param 1: array containing book content pieces
* Param 2: array containing shortcode attributes
* Return: string containing HTML / JavaScript / style block to inject before opening DIV of metadata section when displaying a single book


= rdp_ppe_after_meta_open =

* Param 1: array containing book content pieces
* Param 2: array containing shortcode attributes
* Return: string containing HTML / JavaScript / style block to inject after opening DIV of metadata section when displaying a single book


= rdp_ppe_book_atc_href =

* Param 1: string containing href value for Add-to-Cart button when displaying a single book
* Param 2: string containing URL to PediaPress.com book page
* Param 3: array containing book content pieces
* Param 4: array containing shortcode attributes
* Return: href value for Add-to-Cart button when displaying a single book


= rdp_ppe_atc_button =

* Param 1: string containing HTML for Add-to-Cart button
* Param 2: array containing book content pieces
* Param 3: array containing shortcode attributes
* Return: HTML for Add-to-Cart button


= rdp_ppe_cta_button =
* Param 1: string containing HTML for Call-to-Action button
* Param 2: array containing book content pieces
* Param 3: array containing shortcode attributes
* Return: HTML for Call-to-Action button


= rdp_ppe_before_meta_close =

* Param 1: array containing book content pieces
* Param 2: array containing shortcode attributes
* Return: string containing HTML / JavaScript / style block to inject before closing DIV of metadata section when displaying a single book


= rdp_ppe_after_meta_close =

* Param 1: array containing book content pieces
* Param 2: array containing shortcode attributes
* Return: string containing HTML / JavaScript / style block to inject after closing DIV of metadata section when displaying a single book


= rdp_ppe_toc =

* Param 1: string containing HTML for Table of Contents of a single book
* Param 2: array containing book content pieces
* Param 3: array containing shortcode attributes
* Return: HTML for Table of Contents of a single book


= rdp_ppe_render_book =

* Param 1: string containing HTML for a single book
* Param 2: array containing book content pieces
* Param 3: array containing shortcode attributes
* Return: HTML for a single book


= rdp_ppe_gallery_item =

* Param 1: String containing HTML for a single gallery item
* Param 2: Array of input values for the gallery item
* Return: HTML for a single gallery item