=== Plugin Name ===
Contributors: seorets
Donate link: 
Tags: RETS, IDX, SEORETS, Real estate, SEO
Requires at least: 3.0.1
Tested up to: 4.6
Stable tag: 3.3.69
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==

The [SEO RETS](http://seorets.com/) plugin allows you to embed a real-time MSL feed directly to your website. This residential real estate plugin is easy to set up and configure through your WordPress dashboard. The plugin pulls RETS and IDX real estate listings from over 40 MLS real estate boards nationally. SEO RETS is the QUICKEST &amp; EASIEST way to create lead generating real estate websites. No coding or programming necessary. Subscription and API Key required to use this plugin. 

[Easy to install](http://seorets.com/how-to-install-seo-rets/)

Installation and customization of SEO RETS is simple. Just install, activate and fill in the form fields in your WordPress dashboard. 

[Built-in shortcode generator](http://seorets.com/shortcode-reference/)

Use quick shortcodes to display search boxes and forms anywhere on your site without having to know complicated coding. 

[Integrated WordPress Widgets](http://seorets.com/how-to-use-widgets/)

Widgets allow you to place a MLS search in your websites sidebars and footers. We've already programmed them for you, just drag and drop. 

Serving over 40 MLS real estate board nationally; with over 1,202,000 listings. Our network is expanding. Find out if your board is included, or add your board today. 

== Installation ==

Here are the steps you'll need to take to get your plugin downloaded, installed and running.

1. Sign up at: (http://seorets.com/download/)
2. Once we process your request, you'll receive an email with your unique API Key.
3. Follow the instructions in the email to download the plugin
4. Install the plugin through your WordPress dashboard
5. Enter your custom API Key

== Frequently Asked Questions ==

Get to know the real estate terms, and read some few frequently asked questions about the SEO RETS WordPress Plugin.

RETS: Real Estate Transaction Standard
RETS is a framework that can be adopted by computer systems to receive data from the Multiple Listing Service (MLS) servers, as well as those of other real estate systems provided they also have software installed designed to communicate using the RETS framework. Source: Wikipedia

IDX: Information Data Exchange
IDX is a real estate property search site which allows the public to conduct searches of approved Multiple Listing Service properties in a certain area. Source: Wikipedia

MSL: Multiple Listing Service
MLS is a suite of services that enables real estate brokers to establish contractual offers of compensation (among brokers), facilitates cooperation with other broker participants, accumulates and disseminates information to enable appraisals, and is a facility for the orderly correlation and dissemination of listing information to better serve broker's clients, customers and the public. Source: Wikipedia

Does SEO RETS require any long term agreements or contracts?
No, there are no long term contracts.

Read our full list of FAQ at [SEORETS.com](http://seorets.com/faq-frequently-asked-questions-about-seo-rets/)

== Screenshots ==

1. Main plugin page
2. Developer Tools page
3. Feed Information

== Changelog ==

= 3.3.72 =
* Closed tags for php 7 compatibility

= 3.3.71 =
* Fixed bug with prioritized listings and agent filters introduced in Smart Search.
* Increased prioritized listing search speed.

= 3.3.70 =
* Introduced Smart Search capabilities
* Introduced Predictive Search API

= 3.3.69 =
* Fix bug.
* Fix Shortcode generator.
* Update CRM save function.
* Search speed increase. 

= 3.3.68 =
* Fix bug.
* Update CRM. 

= 3.3.67 =
* Fix default sort bug.
* Fix Script bug. 

= 3.3.66 =

* New option in shortcode new type = "all" to show all listings from different collection, coll_name field to narrow for any collection.
* New Narrow Search, activate in developer tools.
* New Narrow Shortcode, activate in developer tools.
* New narrow include Map results, narrow by featuers,  modify search.
* New sort option.
* Option open search in new window or in same window.
* Compare listings function. To activate it need add "<div data-mls="<?= $l->mls_id; ?>" data-type="<?= $type; ?>" class="addToCompare CompareButton">Add to Compare</div>" to template.
* Fix bug in template.
* New pagination option, you can activate in developer tools, pagination will be in next format First | Prew | Next | Last
* Several Minor Update.

= 3.3.65 =

* Add new shortcode to shortcode generator.
* Fix search widget bug.
* Fix sr-list bug.
* Several Minor Update.

= 3.3.64 =

* Fix search bug.

= 3.3.63 =

* Fix sr-last and sr-viewed bug.

= 3.3.62 =

* Fix Search widget. 
* Fix listings widget. 
* Fix search bug.
* Fix template. 
* Fix mapsearch default search field. For example [sr-mapsearch fields="county:Your County;city:Your City"]
* Fix splitsearch default search field. For example [sr-splitsearch fields="county:Your County;city:Your City"]


= 3.3.61 =

* Fix shortcode generator.
* More comfortable shortcode generator.
* Fix CRM bug.
* Fix search bug.
* In address search word order no matter.
* Fix template. 
* Fix alert. 


= 3.3.60 =

* Add shortcode generator to editor page.
* Fix bug.

= 3.3.59 =

* Fix critical bug.

= 3.3.58 =

* Add google sign In opportunity.
* Add new responsive templates.
* Fix facebook sign In.
* Fix lead popup.
* Fix Shortcode sr-list layout bug.
* Fix Advanced Search layout bug.
* Fix Related properties bug.
* Fix CRM bug. 

= 3.3.57 =

* Fix popup window bug.
* Fix Shortcode generator layout bug.
* Fix Advanced Search layout bug.
* Fix Compatibility bug with old php version.
* Fix getting data bug in shortcode generator. 

= 3.3.56 =
* Fix email alerts bug.
* Fix CRM search bug. 

= 3.3.55 =
* Fix bug. 

= 3.3.54 =
* Added optional CRM system to SEO RETS admin area.
* Fix bug. 

= 3.3.53 =
* Fix search widget bug.

= 3.3.52 =
* Fix advanced search bug.

= 3.3.51 =
* Fix search bug.
* Features switch on/off.

= 3.3.50 =
* New parameter in advanced search. Now you can add default parameter to search. [sr-search type="advanced" coll="Your collection name" fields="One-field:One-value,two-value,three-value;Second-field:One-value,two-value,three-value"]
* New parameter in map search. Now you can add default parameter to map search. For example you can select what city will shows by default [sr-mapsearch fields="city:Your city name"] or [sr-splitsearch fields="city:Your city name"]
* Fix makeup style.

= 3.3.49 =
* New parameter in advanced search.
* New parameter in refine search function. Now users can sort by price.
* New settings (Seo-rets->Developer Tools->Advanced Settings) "Default sort option". Now You can select in what order by default show listings.
* Fix makeup style.

= 3.3.48 =
* Fix bug in lead popup form.
* Fix makeup style.
* New parameters in advanced search.
* Advanced search in the selected collections. [sr-search type="advanced" coll="Your collection name"]

= 3.3.47 =
* On /sr-mapsearch/ page now showing listings under the map
* Add new function "Shortcode Generator(beta)", With this feature you can create shortcodes with multiple parameters and immediately see the number of listings. You will be able to save the generated shortcode or just copy to the desired page. In the page editor, we have added the button, clicking on which you will be able to choose your desired shortcode and in one click insert it into's the page.
* Fix some bug.
* Fix makeup style.
* More responsive makeup.

= 3.3.44 =
* fixed Map Geocoding bug.
* Activate/Deactivate Bootstrap (Seo-rets->Developer Tools->Advanced Settings).
* Fix makeup style.
* More responsive makeup.


= 3.3.43 =
* fixed Lead Popup bug.
* More responsive makeup.


= 3.3.42 =
* fixed bug.
* More responsive makeup.


= 3.3.41 =
* Add new function - Polygonal search, user can select area on map in which will be held search.
* fixed bug.
* More responsive makeup.

= 3.3.40 =
* fixed bug with load city from collection
* More responsive makeup

= 3.3.38 =
* fixed purchased url
* fixed Google Maps
* more clear information

= 3.3.32 =
* removed order seo content page
* opened the plugin code

= 3.3.30 =
* fixed critical bug in shortcodes parameters.

= 3.3.29 =
* recovered [sr-leadcapture] shortcode. It generates basic contact form.
* fixed search request structure.
* added new map search shortcode [sr-splitsearch].
* now you can send plugin emails in two ways through wordpress mail function and php one.

= 3.3.28 =
* fixed custom lead popup issue.

== Upgrade Notice ==

= 3.3.32 =
Upgrade to this version to get the plugin code opened.

= 3.3.30 =
Upgrade to fix critical bug in shortcodes parameters.

= 3.3.29 =
Upgrade to get new mail functionality, fix search request, new [sr-leadcapture] and [sr-splitsearch] shortcodes. 

= 3.3.28 =
Upgrade to get your custom lead popup working even better.

== Features ==

[Features of the SEO RETS Real Estate Plugin](http://seorets.com/features/)

Basic Search is a search bar which allows the user to enter in an address, zip code, MLS#, subdivision or City. This is a great search for a homepage or landing page. 

Advanced Search allows users to search by any criteria including price range, square feet, number of bedrooms and even year built. They can sort results by price, location, type and more. This search type is better suited for your internal pages since it requires form fields for the user to fill out. 

Map Search allows users to view listings on an interactive map. They can browse around a location, clicking on properties they'd like more information on. 

Target your Niche. The SEO RETS plugin also allows you to set default searches for your customers based on any of the MLS fields available. Target your niche with a preset list of waterfront properties or golf communities. Other options include: cities or communities, listings by builder, school, price range, agent ID or office ID. 

The SEO RETS listing pages have been optimized to be search engine friendly. This means that your website has a great chance of ranking highly in search engine results pages. The property address is displayed in the page URL and page title. 

Each page displays a large main image and gallery of photos. Everything is clickable keeping your users engaged in your site. On these pages you can also add virtual tours and other information. 

From here users can skip to the city or subdivision page to continue browsing listings. They can also save to their favorites, view on a map or download a pdf, which include your brand, QR code and phone number for lead generation. 

Our customizable plugin allows you to take advantage of many lead generation features throughout your site. The plugin allow you to configure either forced or requested registration from your users. Or, set how many properties a user can view before the lead capture pops up. 

Our form feature allows you to customize lead capture fields to fit your needs. On property landing pages, the inline "Property Info Request" form gets 8X more leads than sidebar forms. You can add a "Schedule a Showing" call to action anywhere on your site. 

The plugin allows you to keep track of when users use the "Save to Favorites" button, "Email Listing to a Friend" or the "New Listing Alerts" button. 

When a new lead is captured through the site you can configure the plugin to send you a new lead email or text notification right to your phone. The Captcha spam protection feature means that you won't be bombarded with irrelevant spam emails or texts. All leads are saved to a database for easy viewing via the plugin dashboard in WordPress. You can then export those leads into Excel or CSV formats. 

The SEO RETS plugin includes responsive templates that conform to your theme. The plugin has been optimized for mobile phones and tablets making it available to your customers no matter where they are. 

Customize your SEO RETS plugin to match your brand, logo and more. Show an agent roster page and pull specific agent's listings to it. Get creative, the customization of the plugin is up to you.