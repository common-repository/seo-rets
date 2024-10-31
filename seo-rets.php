<?php
/*
Plugin Name: SEO RETS
Plugin URI: http://seorets.com
Description: Convert your RETS/IDX feed into an SEO friendly real estate portal
Version: 3.3.73
Author: SEO RETS, LLC
Author URI: http://seorets.com
*/

error_reporting(E_ERROR | E_CORE_ERROR | E_COMPILE_ERROR | E_USER_ERROR);

//error_reporting( E_ALL);

class SEO_RETS_Plugin
{

    public function __construct()
    {

        $this->api_host = 'api.seorets.com';
        $this->feed = get_option('sr_feed');
        $this->oinw = get_option('sr_open_in_new_window');
        $this->boot = get_option('sr_boot');
        $this->srRewDB = get_option('srRewDB');
        $this->sr_users = get_option('sr_users');
        $this->crm = get_option('sr_crm');
        $this->ucp = get_option('sr_use_custom_pagi');
        $this->boot_refine = get_option('bootstrap_refine');
        $this->sr_fq = get_option('sr_fq');
        $this->boot_refine_sc = get_option('bootstrap_refine_sc');
        $this->sortO = get_option('sr_listingsOrder');
        $this->listingsViewStatistic = get_option('sr_listingsViewStatistic');
        $this->show_features = get_option('sr_show_features');
        $this->mlsid_cache_array = array();
        $this->api_version = '2.3.10';
        $this->api_url = "http://" . $this->api_host . "/v" . $this->api_version;
        $this->api_key = isset($this->feed->key) ? $this->feed->key : false;
        $this->plugin_dir = plugin_dir_url(__FILE__);
//        echo $this->plugin_dir;
        $this->css_resources_dir = $this->plugin_dir . 'resources/css/';
        $this->js_resources_dir = $this->plugin_dir . 'resources/js/';
        $this->image_resources_dir = $this->plugin_dir . 'resources/images/';
        $this->server_plugin_dir = dirname(__FILE__);
        $this->tinymce_dir = $this->server_plugin_dir . '/tinymce/';
        $this->tinymce_url = $this->plugin_dir . '/tinymce/';
        $this->plugin_version = file_get_contents("{$this->server_plugin_dir}/version.ini");
        $this->cachetime = 2; // time in hours
        $this->metadata = get_option('sr_metadata');
        $this->cookie_name = 'sr-session';
        $this->nonce = get_option("sr_nonce");
        $this->session = null;
        $this->session_expiration = (60 * 60 * 24 * 365);
        $this->new_session = false;
        $this->baseurl = get_option("sr_baseurl");
        $this->results_template = "{$this->server_plugin_dir}/resources/defaults/template-responsive-result.php";
        $this->details_template = "{$this->server_plugin_dir}/resources/defaults/template-responsive-details.php";
        $this->resp_css = "{$this->server_plugin_dir}/resources/defaults/template-responsive-css-js.php";
        $this->shortcodes = array(
            "sr-listings",
            "sr-js",
            "sr-viewed",
            "sr-slider",
            "sr-search",
            "sr-subscribe",
            "sr-featured",
            "sr-map",
            "sr-listing",
            "sr-srp",
            "sr-mapsearch",
            "sr-splitsearch",
            "sr-last",
            "sr-leadcapture",
            "sr-list",
            "sr-market"
        );
        global $wpdb;

        $this->wpdb_sr_users = $wpdb->prefix . 'sr_users';
        $this->wpdb_sr_favorites = $wpdb->prefix . 'sr_favorites';
        $this->wpdb_sr_savesearch = $wpdb->prefix . 'sr_savesearch';
        $this->wpdb_sr_other = $wpdb->prefix . 'sr_other';
        $this->wpdb_sr_stat_mls = $wpdb->prefix . 'sr_stat_mls';
        $this->wpdb_sr_stat_option = $wpdb->prefix . 'sr_stat_option';
        $this->wpdb_sr_user_note = $wpdb->prefix . 'sr_user_note';

        register_activation_hook(__FILE__, 'activate');
        add_action('plugins_loaded', 'activate');

        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
        register_uninstall_hook(__FILE__, array("SEO_RETS_Plugin", 'uninstall')); //This has to be static
        if ($this->srRewDB != "first") {
            $this->my_plugin_create_db();
            $this->rewriteUsers();
            update_option('srRewDB', 'first');
            $this->refresh_feed();
        }

        require "{$this->server_plugin_dir}/resources/defaults/generic-defaults.php";

        if (get_option("sr_cacheoverride")) {
            $this->nocache = true;
        }
        if ($this->crm == "") {
            update_option("sr_crm", 'true');
        }
        if ($this->sr_fq == "") {
            update_option("sr_fq", 'true');
        }
        if ($this->boot == "") {
            update_option("sr_boot", 'true');
        }
        if ($this->show_features == "") {
            update_option("sr_show_features", 'false');
        }
        if ($this->boot_refine == "") {
            update_option("bootstrap_refine", 'false');
        }
        if ($this->boot_refine_sc == "") {
            update_option("bootstrap_refine_sc", 'false');
        }
        if ($this->ucp == "") {
            update_option("sr_use_custom_pagi", 'false');
        }
        if (empty($this->sortO)) {
            update_option('sr_listingsOrder', 'none');
        }
        if ($this->oinw == "") {
            update_option("sr_open_in_new_window", 'true');

        } elseif ($this->oinw == "true") {
            add_filter('body_class', 'multisite_body_classes');
        }
        function multisite_body_classes($classes)
        {
            $id = get_current_blog_id();
            $slug = strtolower(str_replace(' ', '-', trim(get_bloginfo('name'))));
            $classes[] = $slug;
            $classes[] = 'site-id-' . $id;
            $classes[] = 'sr-search-oinw';

            return $classes;
        }

        $this->start_session();
        if (get_option('sr-shortcode') != "") {
            add_action('media_buttons_context', 'add_my_custom_button');
            add_action('admin_footer', 'add_inline_popup_content');
        }


//action to add a custom button to the content editor
        function add_my_custom_button($context)
        {

            //path to my icon
//            $img = plugins_url('penguin.png', __FILE__);

            //the id of the container I want to show in the popup
            $container_id = 'popup_container';

            //our popup's title
            $title = 'Insert SEO RETS shortcode';
            $ico = plugins_url('resources/images/icon.png', __FILE__);
//            $ico = "{$this->plugin_dir}resources/images/icon.png";

            //append the icon
//            $context .= "<a class='thickbox' title='{$title}' href='#TB_inline?width=400&inlineId={$container_id}'> <img src='{$img}' /></a>";
            $context .= "<a class='thickbox button' title='{$title}' href='#TB_inline?width=400&inlineId={$container_id}' id='insert-my-media'><img src='{$ico}' alt=''>Insert SEO RETS shortcode</a>";
//            $context .= "<button class='button' title='{$title}' data-editor='popup_container' id='insert-my-media'><img src='{$ico}' alt=''>Insert SEO RETS shortcode</button>";


            return $context;
        }

        function add_inline_popup_content()
        {
            ?>

            <div id="popup_container" style="display:none;">
                <h2>Chose Shortcode to insert in the post</h2>

                <script>
                    jQuery(document).ready(function () {
                        jQuery('.clickToInertA').click(function (e) {
                            e.preventDefault();
                            wp.media.editor.insert(jQuery(this).attr('href'));
                        });
                    });
                </script>
                <?
                $shortcode = get_option('sr-shortcode');

                $response = array_reduce($shortcode, function ($a, $b) {
                    static $stored = array();

                    $hash = md5(serialize($b));

                    if (!in_array($hash, $stored)) {
                        $stored[] = $hash;
                        $a[] = $b;
                    }

                    return $a;
                }, array());
                echo '<table>';
                foreach ($response as $val) {
                    echo "<tr style='margin-top: 10px' ><td style='padding: 10px; border-bottom: 1px solid #cccccc'><span style='float: left'>" . base64_decode($val) . "</span></td><td style='padding: 10px; width: 100px;border-bottom: 1px solid #cccccc'><a style='float:right' href='" . base64_decode($val) . "' class='clickToInertA'>Click To Inert</a></td></tr>";
                }
                echo '</table>';

                ?>

            </div>
            <?php
        }

        add_action("sr_purge_transients", array("SEO_RETS_Plugin", "purge_expired_transients"));

        add_action('init', array($this, 'add_upload_resources'));
//		add_action('posts_selection', array($this, 'temp'));

        if (!empty($this->feed) && $this->feed->elite) {
            add_action('init', array($this, 'create_post_type'));
        }
        wp_register_style('sr-crm', $this->plugin_dir . 'resources/css/crm.css');
        $screen_id = "page";
        add_action('add_meta_boxes_' . $screen_id, 'wptuts_add_my_meta_box');
        function wptuts_add_my_meta_box()
        {
            add_meta_box(
                'seo_rets_metabox', //Meta box ID
                __('SEO RETS', 'seo-rets'), //Meta box Title
                array('SEO_RETS_Plugin', 'wp_seorets_meta_box_callback'), //Callback defining the plugin's innards
                $screen_id, // Screen to which to add the meta box
                'normal', // Context
                'high'
            );
        }

        if (is_admin()) {
// Things that happen if the requested page is the Admin dashboard
            wp_enqueue_style('sr-admin', $this->css_resources_dir . 'admin.css');
            wp_enqueue_script('my_custom_script', $this->plugin_dir . 'resources/js/seorets.min.js');

            add_action('admin_menu', array($this, 'setup_menu'));
            add_action('admin_enqueue_scripts', create_function('', "wp_enqueue_script('jquery-ui-tabs');"));
            $this->loadTinyMce();

        } else {
// Things that happen if the requested page is not the dashboard
            $this->setup_shortcodes();

            add_action('admin_bar_menu', array($this, 'setup_menu_bar'), 999);
//add_filter("posts_request", array($this, "stop_request"));
// Adds handler to code that run
            add_filter("the_posts", array($this, "posts_filter"));
        }

// Things that will happen regardless of the type of page that was requested
        $enqueue_scripts = create_function('', "
// This css contains all of the rules for any html the plugin outputs
wp_register_style('sr-css', '/sr-css');
wp_enqueue_style('sr-css');
wp_register_style('sr-predictive', '{$this->plugin_dir}resources/css/seorets.css');
wp_enqueue_style('sr-predictive');
wp_register_style('sr-contact', '{$this->plugin_dir}resources/css/contact.css');
wp_enqueue_style('sr-contact');

wp_register_style('sr-mp-css', '{$this->plugin_dir}resources/css/mp-style.css');
wp_enqueue_style('sr-mp-css');

wp_register_style('sr-fontawesome', '{$this->plugin_dir}resources/fontawesome/css/font-awesome.css');
wp_enqueue_style('sr-fontawesome');


wp_register_style('sr-magnific-popup', '{$this->plugin_dir}resources/css/magnific-popup.css');
wp_register_script('sr-magnific-popup', '{$this->plugin_dir}resources/js/jquery.magnific-popup.min.js', array('jquery'));
wp_enqueue_style('sr-magnific-popup');
wp_enqueue_script('sr-magnific-popup');

wp_register_script('sr-lazyload', '{$this->plugin_dir}resources/js/jquery.lazyload.min.js', array('jquery'));
wp_enqueue_script('sr-lazyload');

wp_enqueue_script('jquery');
// wp_enqueue_script('sr-main-script', '{$this->plugin_dir}resources/js/seorets.min.js');
");
        $enqueue_scripts_admin = create_function('', "
// This css contains all of the rules for any html the plugin outputs
wp_register_style('sr-css', '/sr-css');
wp_enqueue_style('sr-css');

wp_register_style('sr-contact', '{$this->plugin_dir}resources/css/contact.css');
wp_enqueue_style('sr-contact');

wp_register_style('sr-fontawesome', '{$this->plugin_dir}resources/fontawesome/css/font-awesome.css');
wp_enqueue_style('sr-fontawesome');

wp_register_style('sr-magnific-popup', '{$this->plugin_dir}resources/css/magnific-popup.css');
wp_register_script('sr-magnific-popup', '{$this->plugin_dir}resources/js/jquery.magnific-popup.min.js', array('jquery'));
wp_enqueue_style('sr-magnific-popup');
wp_enqueue_script('sr-magnific-popup');

wp_enqueue_script('thickbox');
wp_enqueue_script('jquery');
");
        $enqueue_scripts_boot = create_function('', "
// This css contains all of the rules for any html the plugin outputs
wp_register_style('sr-bootstrap-css', '{$this->plugin_dir}resources/bootstrap/css/bootstrap.min.css');
wp_register_style('sr-bootstrap-theme-css', '{$this->plugin_dir}resources/bootstrap/css/bootstrap-theme.min.css');
wp_register_script('sr-bootstrap-js', '{$this->plugin_dir}resources/bootstrap/js/bootstrap.min.js', array('jquery'));

wp_enqueue_style('sr-bootstrap-css');
wp_enqueue_style('sr-bootstrap-theme-css');
wp_enqueue_script('sr-bootstrap-js');
");
        if ($this->boot == "true") {
//            print_r($this->boot);
            add_action('wp_enqueue_scripts', $enqueue_scripts_boot);
        }
        add_action('wp_enqueue_scripts', $enqueue_scripts);
        add_action('admin_enqueue_scripts', $enqueue_scripts_admin);

        include('widgets/listings.php');
        include('widgets/search.php');
//        include('widgets/onebox.php');

        add_action('widgets_init', create_function('', "register_widget('SEO_Rets_Search_Widget');register_widget('SEO_Rets_Widget');"));
//        add_action('widgets_init', create_function('', "register_widget('SEO_Rets_Search_Widget');register_widget('SEO_Rets_Widget');register_widget('SEO_Rets_One_Box');"));

        add_filter("rewrite_rules_array", array($this, 'rewrite_rules_array_handler'));
        add_filter("query_vars", array($this, "add_query_vars"));

//        print_r($_SERVER['REDIRECT_URL']);

        add_action("wp_loaded", create_function('', '

global $wp_rewrite, $seo_rets_plugin;

$rules    = get_option(\'rewrite_rules\');
$rewrites = $seo_rets_plugin->get_rewrites();


foreach ($rewrites as $key => $value) {
if (!(isset($rules[$key]) && $rules[$key] == $value)) {
$wp_rewrite->flush_rules();
}
}
'));

        if ($this->baseurl != home_url()) { //Site moved, better refresh metadata and send new URL
            $this->refresh_feed();
        }
        add_action('seo_rets_unfound_page', array($this, 'unfound_page_holder'), 10, 2);

        if (!empty($this->feed) && $this->feed->powered_by == null) {
            $this->admin_id = 'seo-rets';
            $this->admin_title = 'SEO RETS';
        } elseif (!empty($this->feed) && $this->feed->powered_by !== '') {
            $this->admin_id = str_replace(" ", "-", strtolower($this->feed->powered_by));
            $this->admin_title = $this->feed->powered_by;
        } else {
            $this->admin_id = 'seo-rets';
            $this->admin_title = 'SEO RETS';
        }
        wp_register_script('sr_seorets-min', $this->js_resources_dir . 'seorets.min.js');
        wp_register_script('sr_tinyMC', $this->js_resources_dir . 'tinymce.min.js');
        add_action('sr-template-redirect', array(__CLASS__, 'postFilterHook'));
//
//

    }

    public function my_plugin_create_db()
    {

        global $wpdb;
        $version = get_option('my_plugin_version', '1.0');
        $charset_collate = $wpdb->get_charset_collate();
        $table_name = $wpdb->prefix . 'sr_users';
        $table_fav = $wpdb->prefix . 'sr_favorites';
        $table_save = $wpdb->prefix . 'sr_savesearch';
        $table_other = $wpdb->prefix . 'sr_other';
        $table_stat_mls = $wpdb->prefix . 'sr_stat_mls';
        $table_stat_option = $wpdb->prefix . 'sr_stat_option';
        $table_user_note = $wpdb->prefix . 'sr_user_note';

        $sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		sign_up VARCHAR (100) NOT NULL,
		u_name VARCHAR (100) NOT NULL,
		full_name VARCHAR (100) NOT NULL,
		u_phone VARCHAR (100) NOT NULL,
		u_city VARCHAR (100) NOT NULL,
		u_email VARCHAR (100) NOT NULL,
		password VARCHAR (100) NOT NULL,
		salt VARCHAR (100) NOT NULL,
		verify VARCHAR (100) NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        $sql_um = "CREATE TABLE $table_fav (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		user_id VARCHAR (100) NOT NULL,
		mls VARCHAR (100) NOT NULL,
		mtype VARCHAR (100) NOT NULL,
		add_times VARCHAR (100) NOT NULL,
		last_views VARCHAR (100) NOT NULL,
		views VARCHAR (100) NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql_um);

        $sql_oth = "CREATE TABLE $table_other (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		user_id VARCHAR (100) NOT NULL,
		mls VARCHAR (100) NOT NULL,
		mtype VARCHAR (100) NOT NULL,
		last_views VARCHAR (100) NOT NULL,
		views VARCHAR (100) NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql_oth);

        $sql_ss = "CREATE TABLE $table_save (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		user_id VARCHAR (100) NOT NULL,
		base64link VARCHAR (5000) NOT NULL,
		add_times VARCHAR (100) NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql_ss);

        $sql_stat_mls = "CREATE TABLE $table_stat_mls (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		mls VARCHAR (100) NOT NULL,
		mtype VARCHAR (5000) NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql_stat_mls);

        $sql_stat_option = "CREATE TABLE $table_stat_option (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		stat_id VARCHAR (100) NOT NULL,
		mtype VARCHAR (5) NOT NULL,
		time DATETIME NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql_stat_option);

        $sql_user_note = "CREATE TABLE $table_user_note (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		user_id VARCHAR (100) NOT NULL,
		note VARCHAR (5000) NOT NULL,
		time VARCHAR (100) NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql_user_note);
    }

    public static function wpdbInsertRow($table, $array)
    {
        global $wpdb;
        $wpdb->insert($table, $array);
        return $wpdb->insert_id;

    }

    public static function wpdbSelectResults($query)
    {
        global $wpdb;
        $res = $wpdb->get_results($query);
        return $res;
    }

    public static function wpdbSelectResultsCount($query)
    {
        global $wpdb;
        $wpdb->get_results($query);
        return $wpdb->num_rows;
    }

    public static function wpdbUpdateRow($table, $data, $where)
    {
        global $wpdb;
        $wpdb->update($table, $data, $where);
        return $wpdb->insert_id;
    }

    public static function wpdbDeleteRow($table, $data)
    {
        global $wpdb;
        $wpdb->delete($table, $data);
        return $wpdb->insert_id;
    }

    public function rewriteUsers()
    {
        global $wpdb;
        foreach ($this->sr_users as $usr) {
            $wpdb->insert($this->wpdb_sr_users, array(
                'id' => '',
                'u_name' => empty($usr['name']) ? ' ' : $usr['name'],
                'u_email' => empty($usr['email']) ? ' ' : $usr['email'],
                'u_phone' => empty($usr['u_phone']) ? ' ' : $usr['u_phone'],
                'u_city' => empty($usr['u_city']) ? ' ' : $usr['u_city'],
                'full_name' => empty($usr['full_name']) ? ' ' : $usr['full_name'],
                'sign_up' => empty($usr['last_login']) ? ' ' : $usr['last_login'],
                'password' => empty($usr['password']) ? ' ' : $usr['password'],
                'salt' => empty($usr['salt']) ? ' ' : $usr['salt'],
                'verify' => empty($usr['verify']) ? ' ' : $usr['verify']
            ));
            $last_id = $wpdb->insert_id;
            foreach ($usr['favorites'] as $f) {
                $wpdb->insert($this->wpdb_sr_favorites, array(
                    'id' => '',
                    'user_id' => $last_id,
                    'mls' => empty($f['mls']) ? ' ' : $f['mls'],
                    'mtype' => empty($f['type']) ? ' ' : $f['type'],
                    'add_times' => empty($f['add_time']) ? ' ' : $f['add_time'],
                    'last_views' => empty($f['last_views']) ? ' ' : $f['last_views'],
                    'views' => empty($f['views']) ? ' ' : $f['views']
                ));
            }
            foreach ($usr['other'] as $f) {
                $wpdb->insert($this->wpdb_sr_other, array(
                    'id' => '',
                    'user_id' => $last_id,
                    'mls' => empty($f['mls']) ? ' ' : $f['mls'],
                    'mtype' => empty($f['type']) ? ' ' : $f['type'],
                    'last_views' => empty($f['last_views']) ? ' ' : $f['last_views'],
                    'views' => empty($f['views']) ? ' ' : $f['views']
                ));
            }
            foreach ($usr['savesearch'] as $f) {
                $wpdb->insert($this->wpdb_sr_savesearch, array(
                    'id' => '',
                    'user_id' => $last_id,
                    'base64link' => empty($f['base64link']) ? ' ' : $f['base64link'],
                    'add_times' => empty($f['add_time']) ? ' ' : $f['add_time']
                ));
            }


        }
    }

    public function wp_seorets_meta_box_callback()
    {

//        error_reporting(E_ALL);

//        $sr = $this;
//        $plugin_title = $sr->admin_title;
//        $plugin_id = $sr->admin_id;
//
        ?>
        <!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>-->
        <style>
            .col-1 {
                width: 100%;
                /*float: left;*/
                position: relative;
                min-height: 1px;
                padding-right: 15px;
                padding-left: 15px;
                box-sizing: border-box;
            }

            .col-2 {
                width: 50%;
                /*float: left;*/
                position: relative;
                min-height: 1px;
                padding-right: 15px;
                padding-left: 15px;
                box-sizing: border-box;
            }

            .col-2-left {
                width: 50%;
                float: left;
                position: relative;
                min-height: 1px;
                padding-right: 15px;
                padding-left: 15px;
                box-sizing: border-box;
            }

            .col-3-left {
                width: 33.333333%;
                float: left;
                position: relative;
                min-height: 1px;
                padding-right: 15px;
                padding-left: 15px;
                box-sizing: border-box;
            }

            .col-3-left {
                width: 33.333333%;
                float: left;
                position: relative;
                min-height: 1px;
                padding-right: 15px;
                padding-left: 15px;
                box-sizing: border-box;
            }

            .col-4-left {
                width: 25%;
                float: left;
                position: relative;
                min-height: 1px;
                padding-right: 15px;
                padding-left: 15px;
                box-sizing: border-box;
            }

            .col-5-left {
                width: 75%;
                float: left;
                position: relative;
                min-height: 1px;
                padding-right: 15px;
                padding-left: 15px;
                box-sizing: border-box;
            }

            .col-85-left {
                width: 84.7%;
                float: left;
                position: relative;
                min-height: 1px;
                padding-right: 15px;
                padding-left: 15px;
                box-sizing: border-box;
                border-left: 1px solid #e2dddb;
                background-color: #fff;
            }

            /*.col-85-left .row, .col-85-left .selectField{*/
            /*margin: 0;*/
            /*}*/
            .col-15-left {
                width: 15%;
                float: left;
                position: relative;
                min-height: 1px;
                /*padding-right: 15px;*/
                padding-left: 3px;
                box-sizing: border-box;
            }

            .col-6-left {
                width: 66.66666667%;
                float: left;
                position: relative;
                min-height: 1px;
                padding-right: 15px;
                padding-left: 15px;
                box-sizing: border-box;
            }

            .listscode {
                margin-top: 5px;
                padding: 10px;
                border-bottom: 1px solid #cfdadd;
            }

            .addButton {
                width: 100px;
                height: 30px;
                float: left;
            }

            .select_fields__DIV {
                /*margin-left: 100px;*/
            }

            .hide {
                display: none;
            }

            select.sr-formelement {
                display: none;
            }

            .value {
                padding: 5px 10px;
                background: #ffffff;
                border-radius: 3px;
                border: 1px solid #cfdadd;
                position: relative;
                float: left;
                margin-right: 5px;
                margin-top: 5px;
            }

            .value span {
                padding-left: 10px;
                margin-left: 5px;
                border-left: 1px solid #cfdadd;
                cursor: pointer;
            }

            .value span:hover {
                color: #f05050;
            }

            label {
                font-weight: bold;
                cursor: default !important;
            }

            .showAllFields:before, .selectField:before, .row:before {
                display: table;
                content: " ";
            }

            .showAllFields:after, .selectField:after, .row:after {
                display: table;
                content: " ";
                clear: both;
            }

            .showAllFields, .selectField, .row {
                /*width: 100%;*/
                min-height: 1px;
                margin-right: -15px;
                margin-left: -15px;
                box-sizing: border-box;
            }

            .counter_div {
                background: #fff;
                text-align: center;
                padding: 5px;
                border: 1px solid #cfdadd;
                margin-top: 20px;
            }

            .short {
                background: #fff;
                padding: 5px;
                border: 1px solid #cfdadd;
                /*margin-top: 20px;*/
            }

            #srSubscribeForm, #searchADVShortcode, #customSearchForm, #searchSplitMAPhortcode, #searchFMAPhortcode, #srListShortcode {
                background: #fff;
                padding: 5px;
                border: 1px solid #cfdadd;
                /*margin-top: 20px;*/
            }

            .ShortCodeHint {
                color: green;
                /*display: none;*/
            }

            .deleteFiled {
                text-decoration: underline;
                color: #00a0d2;
                cursor: pointer;
            }

            .sr_shortcode_generaot-menu a {
                display: block;
                font-size: 13px;
                color: #222222;
                text-decoration: none;
                padding: 13px;
                border-bottom: 1px solid #e2dddb;
                background-color: #f5f5f5;
                -webkit-transition: all 0.5s;
                -moz-transition: all 0.5s;
                -ms-transition: all 0.5s;
                -o-transition: all 0.5s;
                transition: all 0.5s;
            }

            .active {
                background-color: #fff !important;
            }

            .sr_shortcode_generaot-menu li {
                margin: 0;
            }

            .sr_shortcode_generaot-menu {
                margin: 0;
            }

            #seo_rets_metabox .inside {
                margin: 0;
                background-color: #f5f5f5;
                padding-bottom: 0px;
            }

            .nav-item-gen {
                display: none;
            }

            .activeItem {
                display: block !important;
            }
        </style>

        <script>
            function SelectText(element, hint) {
                var doc = document,
                    text = doc.getElementById(element),
                    range,
                    selection;
                if (doc.body.createTextRange) {
                    range = document.body.createTextRange();
                    range.moveToElementText(text);
                    range.select();
                } else if (window.getSelection) {
                    selection = window.getSelection();
                    range = document.createRange();
                    range.selectNodeContents(text);
                    selection.removeAllRanges();
                    selection.addRange(range);
                }
                try {
                    var successful = document.execCommand('copy');
                    var msg = successful ? 'successful' : 'unsuccessful';
                    if (msg = 'successful') {
                        jQuery(hint).html('Now your shortcode is copied to clipboard and you can put it on any wordpress page where you want show this lisitngs.');
                        jQuery(hint).fadeIn();
                        jQuery(hint).delay(10000).fadeOut("slow");

                    }
                    console.log('Copying text command was ' + msg);
                } catch (err) {
                    console.log('Oops, unable to copy');
                }
            }
            jQuery(document).ready(function () {
                jQuery('#searchADVShortcode').html('[sr-search type="advanced"]');
                jQuery('#customSearchForm').html('[sr-search type="customform"]');
                jQuery('#searchFMAPhortcode').html('[sr-mapsearch]');
                jQuery('#searchSplitMAPhortcode').html('[sr-splitsearch]');
                jQuery('#srListShortcode').html('[sr-list]');
                jQuery('#srSubscribeForm').html('[sr-subscribe]');

                jQuery('.short').click(function () {
                    SelectText('shortCodeSelect', '.ShortCodeHint')
                });
                jQuery('#searchADVShortcode').click(function () {
                    SelectText('searchADVShortcode', '.ShortCodeHint')
                });
                jQuery('#customSearchForm').click(function () {
                    SelectText('customSearchForm', '.ShortCodeHint2')
                });
                jQuery('#searchSplitMAPhortcode').click(function () {
                    SelectText('searchSplitMAPhortcode', '.ShortCodeHint4')
                });
                jQuery('#searchFMAPhortcode').click(function () {
                    SelectText('searchFMAPhortcode', '.ShortCodeHint3')
                });
                jQuery('#srListShortcode').click(function () {
                    SelectText('srListShortcode', '.ShortCodeHint5')
                });
                jQuery('#srSubscribeForm').click(function () {
                    SelectText('srSubscribeForm', '.ShortCodeHint6')
                });

                jQuery('#srListShortcodeInsert').click(function (e) {
                    e.preventDefault();
                    wp.media.editor.insert(jQuery('#srListShortcode').html());
                });
                jQuery('#clickToInertShortCode').click(function (e) {
                    e.preventDefault();
                    wp.media.editor.insert(jQuery('#shortCodeSelect').html());
                });
                jQuery('#customSearchFormInsert').click(function (e) {
                    e.preventDefault();
                    wp.media.editor.insert(jQuery('#customSearchForm').html());
                });
                jQuery('#searchADVShortcodeInsert').click(function (e) {
                    e.preventDefault();
                    wp.media.editor.insert(jQuery('#searchADVShortcode').html());
                });

                jQuery('#searchFMAPhortcodeInsert').click(function (e) {
                    e.preventDefault();
                    wp.media.editor.insert(jQuery('#searchFMAPhortcode').html());
                });

                jQuery('#searchSplitMAPhortcodeInsert').click(function (e) {
                    e.preventDefault();
                    wp.media.editor.insert(jQuery('#searchSplitMAPhortcode').html());
                });
                jQuery('#srSubscribeFormInsert').click(function (e) {
                    e.preventDefault();
                    wp.media.editor.insert(jQuery('#srSubscribeForm').html());
                });

                jQuery.ajax({
                    url: '<?php bloginfo('url') ?>/sr-ajax?action=getType',
                    type: 'post',
                    data: {
                        type: 'get'
                    },
                    success: function (response) {
                        var i = 0;
                        for (i; i <= response.length - 1; i++) {
                            jQuery("<option id='" + response[i]['system_name'] + "'>" + response[i]['system_name'] + "</option>").appendTo("#selectCollection");
                            jQuery("<option id='" + response[i]['system_name'] + "'>" + response[i]['system_name'] + "</option>").appendTo("#ListselectCollection");
                        }
                    }
                });
                updateSrList();
                jQuery('#selectCollection').change(function () {
                    if (jQuery(this).val() != 'None') {
                        jQuery('#searchADVShortcode').html('[sr-search coll="' + jQuery(this).val() + '" type="advanced"]');
                    } else {
                        jQuery('#searchADVShortcode').html('[sr-search type="advanced"]');

                    }
                });
                jQuery('#ListselectCollection').change(function () {
                    updateSrList();
                });
                jQuery('#srListShortcodeObjSub').change(function () {
                    updateSrList();
                });
                jQuery('#srListShortcodeObjCity').change(function () {
                    updateSrList();
                });
                function updateSrList() {
                    var SrListLine = "";
                    if (jQuery('#ListselectCollection').val() != null) {
                        SrListLine = SrListLine + ' type="' + jQuery('#ListselectCollection').val() + '"';
                    } else {
                        SrListLine = SrListLine + ' type="res"';
                    }
                    if (jQuery('#srListShortcodeObjSub').is(':checked')) {
                        SrListLine = SrListLine + ' object="subdivision"';
                    }
                    if (jQuery('#srListShortcodeObjCity').is(':checked')) {
                        SrListLine = SrListLine + ' object="city"';
                    }
                    jQuery('#srListShortcode').html('[sr-list' + SrListLine + ']');

                }

                seorets.startForm(jQuery('.sr-formsection'), function (root) {
                    var fieldsSet = [];
                    var tempField = [];
                    var showFields = [];
                    var timeS = 0;
                    jQuery.ajax({
                        url: '<?php bloginfo('url') ?>/sr-ajax?action=getType',
                        type: 'post',
                        data: {
                            type: 'get'
                        },
                        success: function (response) {
                            jQuery("#property-type").html(' ');
                            i = 0;
                            for (i; i <= response.length - 1; i++) {
                                jQuery("<option id='" + response[i]['system_name'] + "'>" + response[i]['system_name'] + "</option>").appendTo("#property-type");
                            }
                            updateCounter();
                            updShortcode();
                            getOtherFilds();
                        }
                    });
                    root.find('#property-type').change(function () {
                        root.attr("srtype", jQuery(this).val());
                        jQuery('.short').html('[sr-listings type="' + jQuery('#property-type').val() + '"]');
                        updateCounter();
                        updShortcode();
                        jQuery('#select_values').html();

                    });
                    jQuery('#showListings').click(function () {
                        window.open(
                            "<?php echo get_bloginfo('url') ?>/sr-search?" + encodeURIComponent(Base64.encode(JSON.stringify(seorets.getFormRequest(root)))),
                            '_blank' // <- This is what makes it open in a new window.
                        );
//                window.location = "<?// echo get_bloginfo('url') ?>///sr-search?" + encodeURIComponent(Base64.encode(JSON.stringify(seorets.getFormRequest(root))));

                    });
                    function getOtherFilds() {
                        jQuery('.short').html('[sr-listings type="' + jQuery('#property-type').val() + '"]');
                        jQuery.ajax({
                            url: '<?php bloginfo('url') ?>/sr-ajax?action=getFields',
                            type: 'post',
                            data: {
                                type: jQuery('#property-type').val()
                            },
                            success: function (response) {
                                jQuery("#select_fields").html(' ');
                                i = 0;
//                                console.log(response.length);
                                for (i; i <= response.length - 1; i++) {
                                    jQuery("<option data-field-name='" + response[i] + "' id='" + response[i] + "-s'>" + response[i] + "</option>").appendTo("#select_fields");
                                }
                                updValues();
                                tempField.push(jQuery("#select_fields").val());

                            }
                        });
                    }

                    function updValues() {
                        var valS = jQuery("#" + jQuery('#select_fields').val()).val();
                        var selVal = [];
                        if (valS) {
                            for (var r = 0; r <= valS.length; r++) {
                                selVal.push(valS[r]);
                            }
                        }
                        jQuery.ajax({
                            url: '<?php bloginfo('url') ?>/sr-ajax?action=getFieldsValue',
                            type: 'post',
                            data: {
                                type: jQuery('#property-type').val(),
                                fields: jQuery('#select_fields').val()
                            },
                            success: function (response) {
                                if (response == '') {
                                    jQuery("#select_values").fadeOut();
                                    jQuery(".hide").fadeIn();
                                } else {
                                    jQuery("#select_values").fadeIn();
                                    jQuery(".hide").fadeOut();
                                }

                                jQuery("#select_values").html(' ');
                                var i = 0;
                                for (i; i <= response.length - 1; i++) {
                                    var strOp = response[i].toLowerCase();
                                    strOp = strOp.replace(/\s+/g, '');
                                    if (selVal.indexOf(response[i]) > -1) {
                                        jQuery("<option selected id='" + strOp + "-ms'>" + response[i] + "</option>").appendTo("#select_values");
                                    } else {
                                        jQuery("<option id='" + strOp + "-ms'>" + response[i] + "</option>").appendTo("#select_values");
                                    }
                                }
                            }
                        });
                    }

                    jQuery('#property-type').change(function () {
                        jQuery('#select_values').html(' ');
                        if (jQuery('#select_fields').find('option:selected')) {
                            var selectId = jQuery('#select_fields').find('option:selected').attr('data-field-name');
                        }

                        if (jQuery('#property-type').val() != '') {
                            jQuery.ajax({
                                url: '<?php bloginfo('url') ?>/sr-ajax?action=getFields',
                                type: 'post',
                                data: {
                                    type: jQuery('#property-type').val()
                                },
                                success: function (response) {
                                    jQuery("#select_fields").html(' ');
                                    i = 0;
//                                    console.log(response.length);
                                    for (i; i <= response.length - 1; i++) {
                                        if (selectId && response[i] == selectId) {
                                            jQuery("<option selected data-field-name='" + response[i] + "' id='" + response[i] + "-s'>" + response[i] + "</option>").appendTo("#select_fields");
                                            updValues();
                                        } else {
                                            jQuery("<option data-field-name='" + response[i] + "' id='" + response[i] + "-s'>" + response[i] + "</option>").appendTo("#select_fields");
                                        }
                                    }
                                }
                            });


                        }
                    });

                    jQuery('#select_fields').change(function () {
                        timeS = 0;
                        updValues();
                        updateCounter();
                    });


                    var showsFields = function showsFields(i) {
                        var ind = showFields.indexOf(i);
                        var val = jQuery('#' + i).val();
                        var valLength = val.length;
                        if (ind < 0) {
                            var obg = {};
                            obg[jQuery("#select_fields").val()] = 0;
                            showFields.push(jQuery("#select_fields").val());
                            console.log(i);
                            var values = jQuery('#' + i).val();
                            var htm = '';
                            for (var t = 0; t <= values.length - 1; t++) {
                                var str_t = values[t].toLowerCase();
                                str_t = str_t.replace(/\s+/g, '');
                                str_t = str_t.replace(/[^a-zA-Z0-9]/g, '');
                                htm = htm + "<div id='" + str_t + "-d' class='value'>" + values[t] + "<span data-option-val='" + str_t + "' data-field-val='" + i + "' class='clickBooton'>X</span></div>"
                            }
                            var fieldsHtml =
                                "<div class='showAllFields' id='" + jQuery("#select_fields").val() + "-af'><div class='col-4-left'>" +
                                "<label for= ''>" + jQuery("#select_fields").val() + "  <span data-filed-value='" + jQuery("#select_fields").val() + "' class='deleteFiled'>(Remove)</span>: </label></div><div class='col-6-left'>" +
                                "<div class='valueLists' id='" + jQuery("#select_fields").val() + "-v'>" + htm +
                                "</div></div></div>";
                            jQuery(fieldsHtml).appendTo('#fieldsList');
                        } else {
                            var values = jQuery('#' + i).val();
                            var htm = '';
                            for (var t = 0; t <= values.length - 1; t++) {
                                var str_t = values[t].toLowerCase();
                                str_t = str_t.replace(/\s+/g, '');
                                str_t = str_t.replace(/[^a-zA-Z0-9]/g, '');
                                htm = htm + "<div id='" + str_t + "-d' class='value'>" + values[t] + "<span data-option-val='" + str_t + "' data-field-val='" + i + "' class='clickBooton'>X</span></div>"
                            }
                            jQuery("#" + jQuery("#select_fields").val() + "-v").html(htm);
                        }
                    };

                    function updShortcode() {
                        var htm = '';
                        if (fieldsSet.length >= 1) {
                            for (var r = 0; r <= fieldsSet.length - 1; r++) {
                                if (root.find(jQuery("#" + fieldsSet[r]))['length'] != 0) {
                                    if (jQuery("#" + fieldsSet[r]).val() != null) {
                                        if (fieldsSet[r] == 'price1' || fieldsSet[r] == 'price2') {
                                            htm = ' ' + htm + fieldsSet[r] + '="' + jQuery("#" + fieldsSet[r]).attr('data-operator') + jQuery("#" + fieldsSet[r]).val() + '" ';
                                        } else {
                                            htm = ' ' + htm + fieldsSet[r] + '="' + jQuery("#" + fieldsSet[r]).val() + '" ';
                                        }
                                    }
                                    else {
                                        htm = ' ' + htm + fieldsSet[r] + '="" ';
                                    }
                                }
                            }
                            jQuery('.short').html('[sr-listings type="' + jQuery('#property-type').val() + '"' + htm + ']');
                        } else {
                            jQuery('.short').html('[sr-listings type="' + jQuery('#property-type').val() + '"' + htm + ']');

                        }
                    }

                    jQuery('#add').click(function () {
                        var sol = jQuery('#' + jQuery("#select_fields").val()).val();
                        var ind = fieldsSet.indexOf(jQuery("#select_fields").val());
                        if (sol.length && ind < 0) {
                            fieldsSet.push(jQuery("#select_fields").val());
                        }
                        var tem = tempField.indexOf(jQuery("#select_fields").val());
                        if (tem > -1) {
                            tempField.splice(tem, 1);
                        }

                        var htm = '';
                        jQuery('#add').html('Change');
                        if (fieldsSet.length >= 1) {
                            for (var r = 0; r <= fieldsSet.length - 1; r++) {
//                        console.log(jQuery("#" + fieldsSet[r]).val());
//                        console.log(root.find(jQuery("#" + fieldsSet[r])));
                                if (root.find(jQuery("#" + fieldsSet[r]))['length'] != 0) {
                                    htm = ' ' + htm + fieldsSet[r] + '="' + jQuery("#" + fieldsSet[r]).val() + '" ';
                                    showsFields(jQuery("#select_fields").val());
//                            jQuery('#' + jQuery("#select_fields").val() + '-s').remove();
                                }
                            }
                            jQuery('.short').html('[sr-listings type="' + jQuery('#property-type').val() + '"' + htm + ']');
                        }
                        timeS = 0;
                    });
                    jQuery('#select_values').change(function () {
                        if (timeS != 0 || fieldsSet.indexOf(jQuery("#select_fields").val()) >= 0) {
                            var str_i = jQuery("#select_values").val();
//                    alert(str_i);
                            jQuery("#" + jQuery("#select_fields").val()).html('');
                            if (str_i.length >= 1) {
                                jQuery("#" + jQuery("#select_fields").val()).html('');
                                for (var o = 0; o <= str_i.length - 1; o++) {
                                    var str_l = str_i[o].toLowerCase();
                                    str_l = str_l.replace(/\s+/g, '');
                                    str_l = str_l.replace(/[^a-zA-Z0-9]/g, '');
                                    jQuery('<option id="' + str_l + '-o" selected value="' + str_i[o] + '">' + str_i[o] + '</option>').appendTo('#' + jQuery("#select_fields").val());
                                }
                            } else {
                                jQuery('<option selected value="' + jQuery("#select_values").val() + '">' + jQuery("#select_values").val() + '</option>').appendTo('#' + jQuery("#select_fields").val());
                            }
                            updShortcode();
                            showsFields(jQuery("#select_fields").val());
                        } else {
                            timeS++;
                            jQuery('<select id="' + jQuery("#select_fields").val() + '" class="sr-formelement" sroperator="=" multiple srfield="' + jQuery("#select_fields").val() + '"></select>').appendTo('.selectField');
                            var str = jQuery("#select_values").val();
//                    alert(str);
                            if (str.length >= 1) {
                                jQuery("#" + jQuery("#select_fields").val()).html('');
                                for (var i = 0; i <= str.length - 1; i++) {
                                    var str_e = str[i].toLowerCase();
                                    str_e = str_e.replace(/\s+/g, '');
                                    str_e = str_e.replace(/[^a-zA-Z0-9]/g, '');
                                    jQuery('<option id="' + str_e + '-o" selected value="' + str[i] + '">' + str[i] + '</option>').appendTo('#' + jQuery("#select_fields").val());
                                }
                            } else {
                                jQuery('<option selected value="' + jQuery("#select_values").val() + '">' + jQuery("#select_values").val() + '</option>').appendTo('#' + jQuery("#select_fields").val());
                            }
                            var sol = jQuery('#' + jQuery("#select_fields").val()).val();
                            var ind = fieldsSet.indexOf(jQuery("#select_fields").val());
                            if (sol.length && ind < 0) {
                                fieldsSet.push(jQuery("#select_fields").val());
                            }
                            updShortcode();
                            showsFields(jQuery("#select_fields").val());

                        }
                        updateCounter();

                    });
                    var updateCounter = function updateCounter() {
                        jQuery.ajax({
                            type: "GET",
                            dataType: 'json',
                            url: "<?php bloginfo('url') ?>/sr-ajax?action=get-listings-amount&conditions=" + encodeURIComponent(Base64.encode(JSON.stringify(seorets.getFormRequest(root)))),
                            success: function (response) {
                                jQuery('.counter').html(response.mes.count);
//                                console.log(response);
                            }
                        });
                    };
                    jQuery.ajax({
                        type: "GET",
                        dataType: 'json',
                        url: "<?php bloginfo('url') ?>/sr-ajax?action=get-listings-amount&conditions=" + encodeURIComponent(Base64.encode(JSON.stringify(seorets.getFormRequest(root)))),
                        success: function (response) {
//                            console.log(response);
                            jQuery('.counter').html(response.mes.count);
                        }
                    });
                    jQuery('#fieldsList').on('click', 'span.deleteFiled', function () {
                        var c = jQuery(this).attr('data-filed-value');
                        jQuery('#' + c).remove();
                        jQuery('#' + c + '-af').remove();
                        var tem = fieldsSet.indexOf(c);
                        if (tem > -1) {
                            fieldsSet.splice(tem, 1);
                            showFields.splice(tem, 1);
                        }
                        updateCounter();
                        updShortcode();
                        updValues();
                        if (c == jQuery('#select_fields').val()) {
                            jQuery('#add').html('Add Field');
                        }
                    });
                    jQuery('#fieldsList').on('click', 'span.clickBooton', function () {
                        var c = jQuery(this).attr('data-field-val');
                        var i = jQuery(this).attr('data-option-val');
                        jQuery('#' + c).find(jQuery('#' + i + '-o')).remove();
                        jQuery('#' + c + '-v').find(jQuery('#' + i + '-d')).remove();
                        updateCounter();
                        updShortcode();
                        updValues();
                    });
                    jQuery('.priceField').keyup(function () {
                        updateCounter();
                    });
                    jQuery('.order').change(function () {
                        if (fieldsSet.indexOf(jQuery(this).attr('id')) >= 0) {
//                            console.log('YEs');
                            if (jQuery(this).val() == "none") {
                                var tems = fieldsSet.indexOf(jQuery(this).attr('id'));
                                if (tems > -1) {
                                    fieldsSet.splice(tems, 1);
//                                    console.log(fieldsSet);
                                    updShortcode();
                                }
                            }
                        } else {
                            if (jQuery(this).val() != "none") {
//                                console.log('no');
                                fieldsSet.push(jQuery(this).attr('id'));
                            }
                        }
                        updShortcode();

                    });
                    jQuery('.perpage').change(function () {
                        if (fieldsSet.indexOf(jQuery(this).attr('id')) >= 0) {
//                            console.log('YEs');
                            if (jQuery(this).val() == "10") {
                                var tems = fieldsSet.indexOf(jQuery(this).attr('id'));
                                if (tems > -1) {
                                    fieldsSet.splice(tems, 1);
//                                    console.log(fieldsSet);
                                    updShortcode();
                                }
                            }
                        } else {
                            if (jQuery(this).val() != "10") {
//                                console.log('no');
                                fieldsSet.push(jQuery(this).attr('id'));
                            }
                        }
                        updShortcode();
                    });
                    jQuery('.onlymylistings').change(function () {
                        if (fieldsSet.indexOf(jQuery(this).attr('id')) >= 0) {
                            if (jQuery(this).is(':checked')) {
//                                console.log('YEs');
                            } else {
                                var temps = fieldsSet.indexOf(jQuery(this).attr('id'));
                                if (temps > -1) {
                                    fieldsSet.splice(temps, 1);
                                    updShortcode();
                                }
                            }
                        } else {
                            if (jQuery(this).is(':checked')) {
                                fieldsSet.push(jQuery(this).attr('id'));
                            }
//                            console.log('no');
                        }
                        updShortcode();
                    });
                    jQuery('.refine').change(function () {
                        if (fieldsSet.indexOf(jQuery(this).attr('id')) >= 0) {
                            if (jQuery(this).is(':checked')) {
//                                console.log('YEs');
                            } else {
                                var temps = fieldsSet.indexOf(jQuery(this).attr('id'));
                                if (temps > -1) {
                                    fieldsSet.splice(temps, 1);
                                    updShortcode();
                                }
                            }
                        } else {
                            if (jQuery(this).is(':checked')) {
                                fieldsSet.push(jQuery(this).attr('id'));
                            }
//                            console.log('no');
                        }
                        updShortcode();
                    });
                    jQuery('.priceField').focusout(function () {
                        updateCounter();
                        if (fieldsSet.indexOf(jQuery(this).attr('id')) >= 0) {
//                            console.log('YEs');
                            if (jQuery(this).val() == "") {
                                var tems = fieldsSet.indexOf(jQuery(this).attr('id'));
                                if (tems > -1) {
                                    fieldsSet.splice(tems, 1);
//                                    console.log(fieldsSet);
                                    updShortcode();
                                }
                            }
                        } else {
                            if (jQuery(this).val() != "") {
//                                console.log('no');
                                fieldsSet.push(jQuery(this).attr('id'));
                            }
                        }
                        updShortcode();
                    });
                });
                jQuery('body').on('click', 'a.deleteShortcode', function (e) {
                    e.preventDefault();
                    jQuery.ajax({
                        url: '<?php bloginfo('url') ?>/sr-ajax?action=deleteShortcode',
                        type: 'post',
                        data: {
                            shortcode: jQuery(this).attr('data-shortcode')
                        },
                        success: function (response) {
//                            console.log(response);
//                            var htmResponse = '';
//                            for (var i = 0; i <= response.length - 1; i++) {
//                                htmResponse = htmResponse + "<div class='row listscode'><div class='col-6-left'>" + response[i]
//                                    + "</div><div class='col-3-left'><a data-shortcode='" + response[i] + "' class='deleteShortcode' href=''>Delete</a></div></div>";
//                            }
//                            jQuery('body').find('.shortList').html(htmResponse);

                        }
                    });
                    jQuery(this).parent().parent().remove();
                });
                jQuery('#showAllShortcode').click(function (e) {
                    e.preventDefault();
                    jQuery.ajax({
                        url: '<?php bloginfo('url') ?>/sr-ajax?action=getAllShortcode',
                        type: 'post',
                        data: {
                            type: 'all'
                        },
                        success: function (response) {
                            var htmResponse = '';
                            if (response != null) {
                                for (var i = 0; i <= response.length - 1; i++) {
                                    htmResponse = htmResponse + "<div class='shortList'><div class='row listscode'><div class='col-6-left'>" + response[i]
                                        + "</div><div class='col-3-left'><a data-shortcode='" + response[i] + "' class='deleteShortcode' href=''>Delete</a></div></div></div>";
                                }
                                var htm = '<div id="sr-popup2" class="zoom-anim-dialog">' + htmResponse + '</div>';
                                jQuery.magnificPopup.open({
                                    items: {
                                        src: htm,
                                        type: 'inline'
                                    },
                                    fixedContentPos: false,
                                    fixedBgPos: true,

                                    overflowY: 'auto',

                                    closeBtnInside: true,
                                    preloader: false,

                                    midClick: true,
                                    closeOnBgClick: false,
                                    removalDelay: 300,
                                    mainClass: 'my-mfp-zoom-in'
                                });
                            }
                        }
                    });
                });
                jQuery('#saveShortcode').click(function (e) {
                    e.preventDefault();
                    jQuery.ajax({
                        url: '<?php bloginfo('url') ?>/sr-ajax?action=saveShortcode',
                        type: 'post',
                        data: {
                            shortcode: jQuery('#shortCodeSelect').html()
                        },
                        success: function (response) {
                            jQuery('.ShortCodeHint').html(response);
                            jQuery('.ShortCodeHint').fadeIn();
                            jQuery('.ShortCodeHint').delay(800).fadeOut("slow");
                        }
                    });
                });
                jQuery(".sr-menu-gen").click(function (e) {
                    e.preventDefault();
                    jQuery(".sr-menu-gen").removeClass('active');
                    jQuery(this).addClass('active');
                    jQuery(".nav-gen").find('.nav-item-gen').removeClass('activeItem');
                    jQuery(".nav-gen").find('#' + jQuery(this).attr('href')).addClass('activeItem');
                });
            });
        </script>

        <div class="wraps">
            <div class="row">
                <div class="col-15-left">
                    <ul class="sr_shortcode_generaot-menu">
                        <li><a class="sr-menu-gen active" href="shortcodeGenerator">Shortcode generator</a></li>
                        <li><a class="sr-menu-gen " href="searchShortcode">Search shortcode</a></li>
                        <li><a class="sr-menu-gen " href="mapShortcode">Map shortcode</a></li>
                        <li><a class="sr-menu-gen " href="otherShortcode">Other</a></li>
                    </ul>
                </div>
                <div class="col-85-left nav-gen">
                    <div id="shortcodeGenerator" class="sr-sh_generator sr-formsection nav-item-gen activeItem"
                         srtype="res"
                         sroperator="AND">
                        <div class="row">
                            <div class="selectType col-2">
                                <label for="">
                                    Select a type:
                                </label>
                                <select id="property-type" class="form-control"
                                        name="type">

                                </select>
                            </div>
                        </div>
                        <div class="selectField">
                            <div class="col-2-left">
                                <!--                                <div class="addButton">-->
                                <!--                                    <a type="submit" id="add" class="button-primary" style="margin-top: 20px;">Add-->
                                <!--                                        Field</a>-->
                                <!--                                </div>-->
                                <div class="select_fields__DIV">
                                    <label for="">Select fields:</label>
                                    <select name="" id="select_fields" class="form-control"></select>
                                </div>
                            </div>
                            <div class="col-2-left">
                                <div class="multiplySelectValues col-6-left">
                                    <label for="">Select possible Value</label>
                                    <select name="" id="select_values" class="form-control" multiple></select>
                                    <input type="text" class="hide form-control" id="value">
                                </div>
                                <div class="counter_div col-3-left">
                                    <label for="">Number of listings:</label>

                                    <div class="counter"></div>
                                    <a type="submit" id="showListings" class="button-primary"
                                       style="margin-top: 5px;">Show Listings</a>
                                </div>
                            </div>

                        </div>
                        <div style="margin-top: 20px" class="row">
                            <div class="col-2-left">
                                <div class="row">
                                    <div class="col-3-left"><label for="">Price Range:</label></div>
                                    <div class="col-3-left">
                                        <input type="text" id="price1" name="f89d5"
                                               class="sr-formelement priceField form-control"
                                               placeholder="Min:" srfield="price" data-operator="&gt;=:"
                                               sroperator="&gt;=:" srtype="numeric"/>
                                    </div>
                                    <div class="col-3-left">
                                        <input type="text" id="price2" name="pa3jv"
                                               class="sr-formelement priceField form-control"
                                               placeholder="Max:" srfield="price" data-operator="&lt;=:"
                                               sroperator="&lt;=:" srtype="numeric"/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-2-left">
                                <div class="row">
                                    <div class="col-3-left"><label for="">Display order:</label></div>
                                    <div class="col-2-left">
                                        <select class="form-control order" name="" id="order">
                                            <option selected value="none">None</option>
                                            <option value="price:ASC">Price, lowest first</option>
                                            <option value="price:DESC">Price, highest</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row" style="margin-top: 20px">
                            <div class="col-3-left">
                                <div class="row">
                                    <div class="col-6-left"><label for="">Listings to display per page:</label></div>
                                    <div class="col-3-left">
                                        <input type="number" id="perpage" value="10" class="form-control perpage">
                                    </div>
                                </div>

                            </div>
                            <div class="col-3-left">
                                <div class="row">
                                    <div class="col-6-left"><label for="onlymylistings">Only show my listings (this is
                                            set under
                                            the
                                            prioritization tab in the SEO RETS menu):</label></div>
                                    <div class="col-3-left">
                                        <input type="checkbox" data-val="yes" value="yes" class="onlymylistings"
                                               id="onlymylistings">
                                    </div>
                                </div>
                            </div>
                            <div class="col-3-left">
                                <div class="row">
                                    <div class="col-6-left"><label for="refine">Show refine search:</label></div>
                                    <div class="col-3-left">
                                        <input type="checkbox" class="refine" value="yes" id="refine">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="fieldsList" style="margin-top: 20px">
                            <div class="shortCode col-1" style="margin-bottom: 20px">
                                <div class="row">
                                    <div style="padding: 5px 0" class="col-2"><label style="float: left">Just click on
                                            shortCode
                                            to copy
                                            it.</label>
                                        <label style="float: right;margin-left: 10px" for=""><a id="saveShortcode"
                                                                                                href="#save">Save
                                                Shortcode</a></label>
                                        <label style="float: right;margin-left: 10px" for=""><a id="showAllShortcode"
                                                                                                href="#showAllShortcode">Show
                                                all
                                                Shortcode</a></label>
                                        <label style="float: right" for=""><a id="clickToInertShortCode" href="">Insert
                                                Shortcode</a></label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div id="shortCodeSelect" class="short col-2-left"></div>
                                    <div class="ShortCodeHint col-2-left"></div>
                                </div>
                            </div>

                        </div>

                    </div>
                    <div id="searchShortcode" class="searchShortcode nav-item-gen">
                        <div class="row" style="padding: 15px;">
                            <div class="col-4-left">Advaced Search</div>
                            <div class="col-5-left">
                                <div class="row">
                                    <div class="col-3-left">
                                        Select collection. (If you select this option, advanced search will be search
                                        only in this collection),
                                    </div>
                                    <div class="col-6-left">
                                        <select name="selectCollection" id="selectCollection">
                                            <option selected value="None">
                                                None
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-2-left">
                                        <div id="searchADVShortcode" class="searchADVShortcode"></div>
                                        <a href="" id="searchADVShortcodeInsert">Insert shortcode</a>
                                    </div>
                                    <div class="ShortCodeHint col-2-left"></div>
                                </div>
                            </div>
                        </div>
                        <div class="row" style="padding: 15px;">
                            <div class="col-4-left">Custom Search</div>
                            <div class="col-5-left">
                                <div class="row">
                                    <div class="col-6-left">
                                        To use this shortcode, at first add custom code SEO RETS ->Developer Tools->
                                        Advanced Settings -> Custom search form
                                    </div>

                                </div>
                                <div class="row">
                                    <div class="col-2-left">
                                        <div id="customSearchForm" class="customSearchForm"></div>
                                        <a href="" id="customSearchFormInsert">Insert shortcode</a>
                                    </div>
                                    <div class="ShortCodeHint2 col-2-left"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="mapShortcode" class="searchShortcode nav-item-gen ">
                        <div class="row" style="padding: 15px;">
                            <div class="col-4-left">Full width map Search</div>
                            <div class="col-5-left">
                                <div class="row">
                                    <div class="col-2-left">
                                        <div id="searchFMAPhortcode" class="searchFMAPhortcode"></div>
                                        <a href="" id="searchFMAPhortcodeInsert">Insert shortcode</a>
                                    </div>
                                    <div class="ShortCodeHint3 col-2-left"></div>
                                </div>
                            </div>
                        </div>
                        <div class="row" style="padding: 15px;">
                            <div class="col-4-left">Splitsearch map Search</div>
                            <div class="col-5-left">
                                <div class="row">
                                    <div class="col-6-left">
                                        The map split to show listings in list and on the map.
                                    </div>

                                </div>
                                <div class="row">
                                    <div class="col-2-left">
                                        <div id="searchSplitMAPhortcode" class="searchSplitMAPhortcode"></div>
                                        <a href="" id="searchSplitMAPhortcodeInsert">Insert shortcode</a>
                                    </div>
                                    <div class="ShortCodeHint4 col-2-left"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="otherShortcode" class="searchShortcode nav-item-gen ">
                        <div class="row" style="padding: 15px;">
                            <div class="col-4-left">List Shortcode</div>
                            <div class="col-5-left">
                                <div class="row">
                                    <div class="col-2-left">
                                        <div>
                                            <select name="ListselectCollection" id="ListselectCollection">

                                            </select>
                                        </div>
                                        <div class="margin-top-10">
                                            <p><input name="srListShortcodeObj" checked="checked" type="radio"
                                                      value="subdivision"
                                                      id="srListShortcodeObjSub"/>
                                                <label for="srListShortcodeObjSub">Subdivision</label>
                                            </p>
                                            <p><input name="srListShortcodeObj" type="radio" value="city"
                                                      id="srListShortcodeObjCity"/>
                                                <label for="srListShortcodeObjCity">City</label>
                                            </p>
                                        </div>
                                        <div id="srListShortcode" class="srListShortcode"></div>
                                        <a href="" id="srListShortcodeInsert">Insert shortcode</a>
                                    </div>
                                    <div class="ShortCodeHint5 col-2-left"></div>
                                </div>
                            </div>
                        </div>
                        <div class="row" style="padding: 15px;">
                            <div class="col-4-left">Seo Rets Subscribe</div>
                            <div class="col-5-left">
                                <div class="row">
                                    <div class="col-6-left">
                                        Show subscribe form
                                    </div>

                                </div>
                                <div class="row">
                                    <div class="col-2-left">
                                        <div id="srSubscribeForm" class="srSubscribeForm"></div>
                                        <a href="" id="srSubscribeFormInsert">Insert shortcode</a>
                                    </div>
                                    <div class="ShortCodeHint6 col-2-left"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    public function postFilterHook()
    {

        add_action('template_redirect', array(__CLASS__, 'postFilterRedirect'));
    }

    public function compareStrings($a, $b)
    {

        $a = trim($a);
        $b = trim($b);
        preg_match('/\s/', $a, $matches, PREG_OFFSET_CAPTURE);

        $aLenHalf = round(strlen($a) / 2);

        if (empty($matches) || ($matches[0][1] > $aLenHalf)) {
            $a = preg_replace('/^[^A-Za-z\s]+/', '', $a);
        } else {
            $a = preg_replace('/^[^A-Za-z\s]+.*?\s/', '', $a);
        }

        preg_match('/\s/', $b, $matchesB, PREG_OFFSET_CAPTURE);
        $bLenHalf = round(strlen($b) / 2);

        if (empty($matchesB) || ($matchesB[0][1] > $bLenHalf)) {
            $b = preg_replace('/^[^A-Za-z\s]+/', '', $b);
        } else {
            $b = preg_replace('/^[^A-Za-z\s]+.*?\s/', '', $b);
        }

        $res = strcasecmp($a, $b);
        if ($res == 0) {
            return 0;
        } elseif ($res > 0) {
            return 1;
        } else {
            return -1;
        }

    }

    public function recoveringListData(&$data)
    {

        $z = 0;
        foreach ($data->result as $key => $value) {
            $data->result[$key] = trim($value);

            $a = trim($value);
            if ($a == '14th & Coal Un 01') {
                $y = 1;
            } else {
                $y = 0;
            }
            preg_match('/\s/', $a, $matches, PREG_OFFSET_CAPTURE);

            $aLenHalf = round(strlen($a) / 2);

            if (empty($matches) || ($matches[0][1] > $aLenHalf)) {
                $a = preg_replace('/^[^A-Za-z\s]+/', '', $a);
            } else {
                $a = preg_replace('/^[^A-Za-z\s]+.*?\s/', '', $a);
            }
            $a = trim($a);
            $firstSymbolValid = !preg_match('/[A-Za-z]/', $a[1]);
            if (empty($a) || !preg_match('/[a-zA-Z]/', $a) || strlen($a) < 2 || strlen($a) > 50 || $firstSymbolValid) {
                unset($data->result[$key]);
            }
        }

    }

    public function postFilterRedirect()
    {
        global $wp_query;
        $themeDir = dirname(TEMPLATEPATH) . '/' . get_stylesheet() . '/';
        $templates = get_option('sr_template');
        $method = $wp_query->query['sr_method'];
        $arraySystemMethodToOption = array(
            'favorites' => 'User Favorites',
            'details' => 'Listing Details',
            'forgot' => 'Forgot Password',
            'login' => 'User Login',
            'reset' => 'Password Reset',
            'signup' => 'User Signup',
            'search' => 'Search',
            'subscribe' => 'Email Subscribe',
            'verify' => 'Verify User'
        );

        $templateFile = '';
        if ($templates['type'] == 'all') {
            $templateFile = $templates['all-value'];

        } elseif ($templates['type'] == 'every') {
            $templateFile = $templates['every-values'][$arraySystemMethodToOption[$method]];
        }
        if (!empty($templateFile)) {
            include($themeDir . $templateFile);
            exit;
        }
    }

    private function loadTinyMce()
    {
        if (array_key_exists('sr_tinymce_method', $_GET)) {

            $method = $_GET['sr_tinymce_method'];
            if (!empty($method)) {
                include($this->tinymce_dir . $method . '/dialog.php');
            } else {
                echo 'Invalid method.';
            }
            exit;
        }
    }

    public function rewrite_rules_array_handler($rules)
    {
        return array_merge($this->get_rewrites(), $rules);
    }

    public function add_upload_resources()
    {
        if (isset($_GET['page']) && $_GET['page'] == 'seo-rets-branding') {
            add_action('admin_enqueue_scripts', array($this, 'setup_upload_resources'));
        }

    }

    public function unfound_page_holder($place, $extra = array())
    {
        $content = get_option("sr_unfoundpage");
        $content = apply_filters('the_content', $content);
        if (empty($content)) {
            $content = "<p>No properties matched this search criteria, please go back and try a different search.</p>";
        }
        echo $content;
    }

    public static function setup_upload_resources()
    {
        wp_enqueue_script('media-upload');
        wp_enqueue_script('thickbox');
        wp_enqueue_style('thickbox');
    }


    public function display_update_notice()
    {
        if (get_option("sr_display_update_banner") == true) {
            echo '<div class="error"><p><strong>Warning</strong> - Your ' . $this->admin_title . ' plugin is out of date. Please <a href="admin.php?page=seo-rets-updates">click here</a> to get the latest version.</p></div>';
        }
    }


    public function create_post_type()
    {
        register_post_type('sr_city',
            array(
                'labels' => array(
                    'name' => 'Cities',
                    'singular_name' => 'City',
                    'add_new' => 'Add New',
                    'add_new_item' => 'Add New City',
                    'edit' => 'Edit',
                    'edit_item' => __('Edit City'),
                    'new_item' => __('New City'),
                    'view' => __('View City'),
                    'view_item' => __('View City'),
                    'search_items' => __('Search Cities'),
                    'not_found' => __('No cities found'),
                    'not_found_in_trash' => __('No cities found in Trash')
                ),
                'public' => true,
                'has_archive' => true,
                'menu_icon' => $this->plugin_dir . '/resources/images/city-icon.png',
                'supports' => array('editor'),
                'rewrite' => array('slug' => 'cities'),
            )
        );
    }

    public function activate()
    {

        if ($this->boot == "") {
            update_option("sr_boot", 'true');
        }
        if (wp_next_scheduled('sr_purge_transients') === false) {
            wp_schedule_event(current_time('timestamp'), 'daily', 'sr_purge_transients');
        }
        flush_rewrite_rules(false);

        $templatesList = get_option('sr_templates_list');
        $currentTemplate = get_option('sr_templates');


        $id = -1;
        $id2 = -1;
        $id3 = -1;
        if (!empty($templatesList)) {
            foreach ($templatesList as $key => $template) {
                if ($template['name'] == 'seo rets responsive template') {
                    $id = $key;
                }
                if ($template['name'] == 'seo rets responsive template2') {
                    $id2 = $key;
                }
                if ($template['name'] == 'seo rets responsive template3') {
                    $id3 = $key;
                }
            }
        } elseif (!empty($currentTemplate)) {
            $templatesList[0]['name'] = 'original styles';
            $templatesList[0]['default'] = 1;
            $templatesList[0]['id'] = 0;
            $templatesList[0]['templates'] = $currentTemplate;
            $key = 0;
        } else {
            $key = 0;
        }
//        if(!empty($currentTemplate)&&empty($templatesList)){
//            $templatesList[0]['name']='original styles';
//            $templatesList[0]['default']=1;
//            $templatesList[0]['id']=0;
//            $templatesList[0]['templates']=$currentTemplate;
//        }


        if ($id < 0) {
            if (empty($templatesList)) {
                $id = 0;
                $tempID = 0;

            } else {
                $key++;
                $id = $key;
                $tempID = $templatesList[$key - 1]['id'] + 1;
            }
            $templatesList[$id]['name'] = 'seo rets responsive template';
            $templatesList[$id]['id'] = $tempID;

            if (empty($currentTemplate)) {
                $templatesList[$id]['default'] = 1;
            }

            $responsiveResult = file_get_contents($this->server_plugin_dir . "/resources/defaults/template-responsive-result.php");
            $responsiveDetails = file_get_contents($this->server_plugin_dir . "/resources/defaults/template-responsive-details.php");
            $responsiveCssJs = file_get_contents($this->server_plugin_dir . "/resources/defaults/template-responsive-css-js.php");
            $templatesList[$id]['templates']['details'] = $responsiveDetails;
            $templatesList[$id]['templates']['result'] = $responsiveResult;
            $templatesList[$id]['templates']['css'] = $responsiveCssJs;

            if ($templatesList[$id]['default'] == 1) {

                $currentTemplate['details'] = $responsiveDetails;
                $currentTemplate['result'] = $responsiveResult;
                $currentTemplate['css'] = $responsiveCssJs;
                update_option('sr_templates', $currentTemplate);
            }

        }

        if ($id2 < 0) {
            if (empty($templatesList)) {
                $id2 = 1;
                $tempID = 1;

            } else {
                $id2 = $key + 1;
                $tempID = $templatesList[$key]['id'] + 1;
            }

            $templatesList[$id2]['name'] = 'seo rets responsive template2';
            $templatesList[$id2]['id'] = $tempID;

            $responsiveResult2 = file_get_contents($this->server_plugin_dir . "/resources/defaults/template-responsive-result2.php");
            $responsiveDetails2 = file_get_contents($this->server_plugin_dir . "/resources/defaults/template-responsive-details2.php");
            $responsiveCssJs2 = file_get_contents($this->server_plugin_dir . "/resources/defaults/template-responsive-css-js2.php");
            $templatesList[$id2]['templates']['details'] = $responsiveDetails2;
            $templatesList[$id2]['templates']['result'] = $responsiveResult2;
            $templatesList[$id2]['templates']['css'] = $responsiveCssJs2;
//

            if ($templatesList[$id2]['default'] == 1) {

                $currentTemplate['details'] = $responsiveDetails2;
                $currentTemplate['result'] = $responsiveResult2;
                $currentTemplate['css'] = $responsiveCssJs2;
                update_option('sr_templates', $currentTemplate);
            }

        }
        if ($id3 < 0) {
            if (empty($templatesList)) {
                $id3 = 2;
                $tempID = 2;

            } else {
                $id3 = $key + 2;
                $tempID = $templatesList[$key + 1]['id'] + 1;
            }

            $templatesList[$id3]['name'] = 'seo rets responsive template3';
            $templatesList[$id3]['id'] = $tempID;

            $responsiveResult3 = file_get_contents($this->server_plugin_dir . "/resources/defaults/template-responsive-result3.php");
            $responsiveDetails3 = file_get_contents($this->server_plugin_dir . "/resources/defaults/template-responsive-details3.php");
            $responsiveCssJs3 = file_get_contents($this->server_plugin_dir . "/resources/defaults/template-responsive-css-js3.php");
            $templatesList[$id3]['templates']['details'] = $responsiveDetails3;
            $templatesList[$id3]['templates']['result'] = $responsiveResult3;
            $templatesList[$id3]['templates']['css'] = $responsiveCssJs3;
//

            if ($templatesList[$id3]['default'] == 1) {

                $currentTemplate['details'] = $responsiveDetails3;
                $currentTemplate['result'] = $responsiveResult3;
                $currentTemplate['css'] = $responsiveCssJs3;
                update_option('sr_templates', $currentTemplate);
            }

        }

        update_option('sr_templates_list', $templatesList);


    }


    public function deactivate()
    {
        wp_clear_scheduled_hook('sr_purge_transients');
        SEO_RETS_Plugin::purge_all_transients();
    }

    public function uninstall()
    {
        wp_clear_scheduled_hook('sr_purge_transients');
        SEO_RETS_Plugin::purge_all_transients();

//FIXME delete all options that were set
    }

    public function city_page_url($key)
    {
        $cities = get_option("sr_citypage_url_map");

        return $cities[$key];
    }

    public function create_city_pages()
    {
        update_option("sr_citypage_url_map", array());

        $cities = array();
        foreach ($this->metadata as $property_type) {
            $cities = array_merge($property_type->fields->city->values, $cities);
        }
        $cities = array_unique($cities);

        $map_option = get_option("sr_citypage_url_map");
        $url_map = $map_option ? $map_option : array();

        foreach ($cities as $city) {
            $my_post = array(
                'post_title' => $city . " Real Estate",
                'post_type' => 'sr_city',
                'post_content' => '[sr-listings type="res" city="' . $city . '"]',
                'post_status' => 'publish',
                'post_author' => 1,
                'comment_status' => 'closed'
            );

            if (!isset($url_map[$city])) {
                $post_id = wp_insert_post($my_post);
                $inserted_post = get_post($post_id);

                $url_map[$city] = home_url() . "/cities/" . $inserted_post->post_name;
            }

        }

        update_option("sr_citypage_url_map", $url_map);
    }

    public function register($key = null)
    {
        if ($key !== null) {
            $this->api_key = $key;
        }

        $this->make_nonce();

        $response = $this->api_request("activate_plugin", array(
            'baseurl' => home_url(),
            'nonce' => $this->nonce
        ), false, false);

        if ($response->error == 0) {
            update_option('sr_feed', $response->feed);
            $this->feed = $response->feed;
            update_option('sr_metadata', $response->metadata);
            $this->metadata = $response->metadata;
            if ($this->feed->elite) {
                $this->create_city_pages();
            }

            return true;
        }

        return $response->error_description;
    }

    public function deregister()
    {
        $response = $this->api_request("deactivate_plugin", null, false, false);
        update_option('sr_feed', false);
        update_option('sr_metadata', false);
    }

    public function refresh_feed()
    {
        global $wpdb;
        $wpdb->query("DELETE FROM `wp_options` where `option_name` LIKE '_transient_timeout_sr%' OR `option_name` LIKE '_transient_sr%';");
        $this->make_nonce();

        $response = $this->api_request("get_metadata", array('baseurl' => home_url(), 'nonce' => $this->nonce));

// if $response is null avoid doing anything because it would wipe out plugin options.
// API must have been unreachable for this refresh.
// Fix me add error logic for this situation.
        if (isset($response)) {

            if (home_url() != $this->baseurl) {
                $this->baseurl = home_url();
                update_option("sr_baseurl", $this->baseurl);
            }

            if ($response->error == 0) {
                update_option('sr_feed', $response->feed);
                $this->feed = $response->feed;
                update_option('sr_metadata', $response->metadata);
                if ($this->feed->elite) {
                    $this->create_city_pages();
                }

                return true;
            }

        }

        return false;
    }

    private function make_nonce()
    {
        $nonce = sha1((string)mt_rand() . (string)mt_rand() . (string)mt_rand() . (string)mt_rand());
        $this->nonce = $nonce;
        update_option("sr_nonce", $nonce);
    }

    public function register_template($type, $location)
    {
        switch ($type) {
            case "details":
                $this->details_template = $location;
                break;
            case "results":
                $this->results_template = $location;
                break;
            default:
//FIXME return error
        }
    }

    public function show_popup()
    {
        ?>
        <script type="text/javascript">
            var interval = null;
            function run_close_popup() {
                jQuery.ajax({
                    url: "<?php echo home_url() ?>/sr-ajax?action=can-i-close-popup",
                    processData: false,
                    data: 'can-i-close-popup',
                    success: function (resp) {
                        if (resp.mes == true) {
                            close_popup();
                            if (interval != null) {
                                clearInterval(interval);
                            }
                        }
                    }
                });
            }
            interval = setInterval(run_close_popup, 5000);
        </script>
        <script type="text/javascript">

            var sr_popup;

            function close_popup() {
                jQuery.ajax({
                    url: "<?php echo home_url() ?>/sr-ajax?action=no-popup"
                });
                jQuery.magnificPopup.close();
                jQuery("#sr-popup-form").fadeOut("slow", function () {
                    sr_popup.fadeOut("slow");
                });
            }
            ;
            var sizeFrame = function sizeFrame() {
                var F = document.getElementById("sr-popup-frame");
                var F2 = document.getElementById("sr-popup2");
                var value;
                var constantHeight = 40;

                if (F.contentDocument) {
                    value = F.contentDocument.documentElement.scrollHeight + constantHeight; //FF 3.0.11, Opera 9.63, and Chrome
                } else {
                    value = F.contentWindow.document.body.scrollHeight + constantHeight; //IE6, IE7 and Chrome
                }
                if (value == '' || value < 220) {
                    return false;
                }
                var windowHeight = jQuery(window).height();
                if (value + constantHeight <= windowHeight) {
                    F.height = value;
                }
                else {
                    F.height = windowHeight - (windowHeight / 100 * 15);
                }
                F.contentDocument.documentElement.scrollHeight = parseInt(F.height);
                F.contentWindow.document.body.scrollHeight = parseInt(F.height);
                jQuery("#sr-popup-frame").height(parseInt(F.height));
//                document.getElementById('#sr-popup-frame').contentWindow.document.body.offsetHeight + 'px';
//                jQuery("#sr-popup-frame").css('height', parseInt(F.height));
//                jQuery("#sr-popup-frame").css('width', parseInt(F2.width));
//                jQuery("#sr-popup2").css('height', parseInt(F.height));
                return true;

            }
            var callAmount = 0;
            var callSizeFrame = function callSizeFrame() {
                var iframeLoadFlag;
//                iframeLoadFlag = !sizeFrame();

                if (iframeLoadFlag) {
                    window.setTimeout(callSizeFrame, 500);
                }
            }
            jQuery(function ($) {
                closeButton = true;

                if (<?php if ($this->popup_options['force'] == "enabled") {
                    echo "true";
                } else {
                    echo "false";
                }?>) {
                    markFrame = '<div style="text-align:center;" id="sr-popup2" class="zoom-anim-dialog">' +
                        '<iframe class="mfp-iframe" id="sr-popup-frame" allowfullscreen style="border:0;width:100%" frameborder="0"></iframe>' +
                        '</div>';
//                    closeButton = false;
                } else {
                    markFrame = '<div style="text-align:center;" id="sr-popup2" class="zoom-anim-dialog"><div id="container">' +
                        '<iframe class="mfp-iframe" id="sr-popup-frame" allowfullscreen style="border:0;width:100%" frameborder="0"></iframe>' +
                        '</div><div class="mfp-close"></div></div>';

                }
                <!--                -->
                <?// var_dump($this->popup_options['force']); ?>

                htm = '<?php echo get_bloginfo('url');?>/sr-contact';

                jQuery.magnificPopup.open({
                    items: {
                        src: htm,
                        type: 'iframe'
                    },
                    iframe: {
                        markup: markFrame

                    },
                    fixedContentPos: true,
                    fixedBgPos: true,
                    overflowY: 'auto',
                    showCloseBtn: closeButton,
                    closeBtnInside: closeButton,
                    preloader: false,

                    midClick: false,
                    closeOnBgClick: false,
                    removalDelay: 300,
                    mainClass: 'my-mfp-zoom-in',
                    callbacks: {
                        open: function () {
                            jQuery("#sr-popup-frame").load(function () {
                                sizeFrame();
                            });
                        }
                    }
                });
//                callSizeFrame();
            });
        </script>
        <?php
    }


    public function put_meta_info()
    {
        $seodata = get_option('sr_seodata');
        $description = isset($seodata['description']) ? $seodata['description'] : $this->seo_defaults['description'];
        $keywords = isset($seodata['keywords']) ? $seodata['keywords'] : $this->seo_defaults['keywords'];
        $description = $this->parse_seo_data($description);
        $keywords = $this->parse_seo_data($keywords);

        echo '<!-- Start SEO RETS Meta Data -->
<meta name="description" content="' . htmlentities($description) . '" />
<meta name="keywords" content="' . htmlentities($keywords) . '" />
<!-- End SEO RETS Meta Data -->' . "\n";
    }

    public function print_meta_info_list()
    {
        echo $this->meta_data;
    }


    public function put_404_header()
    {
        header("HTTP/1.0 404 Not Found");
    }


    private function find_matches($data)
    {
        $matches = array();
        preg_match_all("/%([a-z_]+)%/", $data, $matches);

        return $matches;
    }

    private function rrmdir($dir)
    {
        foreach (glob("{$dir}/*") as $file) {
            if (is_dir($file)) {
                $this->rrmdir($file);
            } else {
                unlink($file);
            }
        }
        rmdir($dir);
    }

    public function include_return($loc, $vars = null)
    {
        if ($vars) {
            extract($vars, EXTR_SKIP); //SCREW YOU EXTRACT AND OVERWRITING $loc
        }

        $seo_rets_plugin = $this;
        global $wp_query;
        ob_start();
        $i = include("{$this->server_plugin_dir}/{$loc}");

        return (($i == 1) ? "" : $i) . ob_get_clean();
    }

    private function parse_seo_data($data)
    {
        $matches = $this->find_matches($data);

        for ($n = 0; $n < count($matches[0]); $n++) {
            if (isset($this->detail_result->{$matches[1][$n]})) {
                if ($matches[1][$n] == 'unit_number' && $this->detail_result->{$matches[1][$n]} != '') {
                    $data = str_replace($matches[0][$n], '#' . $this->detail_result->{$matches[1][$n]}, $data);
                } else {
                    $data = str_replace($matches[0][$n], $this->detail_result->{$matches[1][$n]}, $data);
                }

            } else {
                $data = str_replace($matches[0][$n], '', $data);
            }
        }

        return $data;
    }


    public function update_plugin()
    {
        if ($this->boot == "") {
            update_option("sr_boot", 'true');
        }
        $plugins_dir = dirname(dirname(__FILE__));

        if (!class_exists('ZipArchive')) {
            return "Error: PHP ZipArchive package required!";
        }

        if (!is_writable($plugins_dir) || !is_writable("{$plugins_dir}/seo-rets")) {
            return "Error: Plugin directory not writable!";
        }

        $running_version = new Version($this->plugin_version);
        $tmp_dir = "{$plugins_dir}/seo-rets-tmp";
        $zip_loc = "{$tmp_dir}/update.zip";
        $update = $this->api_request('update', array(
            'running_version' => array(
                'major' => $running_version->version_array[0],
                'minor' => $running_version->version_array[1],
                'build' => $running_version->version_array[2]
            )
        ));

        foreach ($update->versions as $version) {
            eval("?>{$version->execute_before}");

            if (is_dir($tmp_dir)) {
                return "Error: temporary folder already exists!";
            }
            mkdir($tmp_dir);

            //Archive code, here be dragons!
            file_put_contents($zip_loc, base64_decode($version->package));
            $archive = new ZipArchive;

            if (!$archive->open($zip_loc)) {
                return "Unable to open zip";
            }
            if (!$archive->extractTo($tmp_dir)) {
                return "Unable to extract plugin";
            }
            $archive->close($zip_loc);
            unlink($zip_loc);
            //End Archive code

            if (!is_dir("{$tmp_dir}/seo-rets")) {
                $this->rrmdir($tmp_dir); //try to remove tmp dir and bail out
                return "Error: failed to unpack plugin!";
            }

            if (is_dir("{$plugins_dir}/seo-rets-new")) {
                return "Error: new plugin folder already exists!";
            }

            if (is_dir("{$tmp_dir}/seo-rets-old")) {
                return "Error: archive looks improperly packaged!";
            }


            ////////////////////////////////
            //Start critical code
            ////////////////////////////////


            if (!@rename("{$tmp_dir}/seo-rets", "{$plugins_dir}/seo-rets-new")) {
                $this->rrmdir($tmp_dir); //try to remove tmp dir and new plugin folder and bail out
                $this->rrmdir("{$plugins_dir}/seo-rets-new");

                return "Error: failed to move new plugin!";
            };

            if (!@rename("{$plugins_dir}/seo-rets", "{$tmp_dir}/seo-rets-old")) {
                //FUUUUUUUUUUUUUUU this is the bad one
                if (is_dir("{$tmp_dir}/seo-rets-old")) {
                    //Looks like it worked but was still complaining for some reason
                    //Move it back and bail out
                    @rename("{$tmp_dir}/seo-rets-old", "{$plugins_dir}/seo-rets");
                }
                $this->rrmdir($tmp_dir); //try to remove tmp dir and bail out
                return "Error: failed to move new plugin!";
            }

            if (!@rename("{$plugins_dir}/seo-rets-new", "{$plugins_dir}/seo-rets")) {
                //FUUUUUUUUUUUU also bad
                if (is_dir("{$plugins_dir}/seo-rets")) {
                    //looks successful but revert just in case it's an empty dir or something
                    @rename("{$plugins_dir}/seo-rets", "{$tmp_dir}/seo-rets");
                }
                @rename("{$tmp_dir}/seo-rets-old", "{$plugins_dir}/seo-rets");
                $this->rrmdir($tmp_dir); //try to remove tmp dir and bail out
                $this->rrmdir("{$plugins_dir}/seo-rets-new");

                return "Error: failed to move new plugin!";
            }


            ////////////////////////////////
            //End critical code
            ////////////////////////////////


            $this->rrmdir($tmp_dir);
            eval("?>{$version->execute_after}");
        }
        $this->make_nonce();
        $response = $this->api_request("get_metadata", array(
            'baseurl' => home_url(),
            'nonce' => $this->nonce
        ), false, false);
        update_option('sr_metadata', $response->metadata);
        update_option('sr_lastupdated', time());
        update_option("sr_display_update_banner", false);


        return "Plugin updated";
    }

    /*
SESSION STUFF
*/


    public function start_session()
    {
        if (isset($_COOKIE[$this->cookie_name])) {
            $session_string = $_COOKIE[$this->cookie_name];
        } else {
            $session_string = $this->set_new_cookie();
            $this->new_session = true;
        }
        $this->session_string = $session_string;
    }

    public function set_session_data($key, $data)
    {
        $session = $this->get_session();
        $session[$key] = $data;
        $this->set_session($session);

        return true;
    }

    public function get_session_data($key)
    {
        $session = $this->get_session();
        if (isset($session[$key])) {
            return $session[$key];
        }

        return false;
    }

    private function get_session()
    {
        $this->session = apply_filters('filter_sr_get_session', $this->session, "sr_sessions_{$this->session_string}");
        if ($this->session === null) {
            $this->session = get_transient("sr_sessions_{$this->session_string}");

            if ($this->session === false) {
                $this->session = array();
            }
        }

        return $this->session;
    }

    private function set_session($newsession)
    {
        $this->session = apply_filters('filter_sr_set_session', $newsession, $this->session_expiration, "sr_sessions_{$this->session_string}");
        $expiration = $this->session_expiration;
        if ($this->new_session) {
            $expiration = 60 * 60; //Initial session expiration is an hour
        }
//        $this->session = $newsession;
        set_transient("sr_sessions_{$this->session_string}", $newsession, $expiration);
    }

    private function set_new_cookie()
    {
        $session_string = $this->get_session_string();
        setcookie($this->cookie_name, $session_string, time() + $this->session_expiration, '/');
        $_COOKIE[$this->cookie_name] = $session_string;

        return $session_string;
    }

    private function get_session_string()
    {
        do {
            for ($str = '', $n = 0; $n < 10; $n++) {
                $str .= chr(rand(97, 122));
            }
        } while (get_transient("sr_sessions_{$str}") !== false);

        return $str;
    }

    /*
END SESSION STUFF
*/

    public function short_state($s)
    {
        $tmp = ucwords(strtolower($s));
        $rev = array_flip($this->state_change);

        return isset($rev[$tmp]) ? $rev[$tmp] : ((strlen($s) == 2) ? $s : 'NA');
    }

    public function long_state($s)
    {
        $tmp = strtoupper($s);

        return isset($this->state_change[$tmp]) ? $this->state_change[$tmp] : $s;
    }

    private function obj_prop_in_array($value, $array, $prop)
    {

        foreach ($array as $v) {
            if ($v[$prop] == $value) {
                return true;
            }
        }

        return false;
    }

    public static function lead_alert($lead, $source)
    {
        //FIXME currently only supports a list of emails delimited by line breaks
        //FIXME source is just a string, need to add formal source object
        //FIXME add support for having lead destinations (email, store to db, mailchimp, SMS, whatever someone can dream up)
        //FIXME add support for filters on both the source and the lead itself
        $settings = get_option("sr_leadcapture");
        if (!$settings) {
            return;
        }

        $default_email = get_bloginfo('admin_email');

        $emails = array_map("trim", explode("\n", $settings));
        $content = "$lead has signed up for $source";
        $domain = $_SERVER['SERVER_NAME'];
        if (substr($domain, 0, 4) == "www.") {
            $domain = substr($domain, 4);
        }
        $headers = "From: leads@{$domain}\r\n";
        $headers .= "Bcc: " . implode(",", $emails);

        $email = (count($emails) < 1) ? $default_email : "";

        self::sendEmail($email, "New Lead", $content, $headers);
    }

    public static function purge_expired_transients()
    {
        global $wpdb;
        $sql = "DELETE vrow, trow
			FROM {$wpdb->options} vrow, {$wpdb->options} trow
			WHERE vrow.option_name LIKE \"_transient_sr_%\" AND
			vrow.option_name NOT LIKE \"_transient_timeout_sr_%\" AND
			trow.option_name = CONCAT(\"_transient_timeout_sr_\", SUBSTRING(vrow.option_name, 15))
			AND trow.option_value < UNIX_TIMESTAMP()";
        $wpdb->query($sql);
    }

    public static function purge_all_transients()
    {
        global $wpdb;
        $sql = "DELETE vrow, trow
			FROM {$wpdb->options} vrow, {$wpdb->options} trow
			WHERE vrow.option_name LIKE \"
			%\" AND
			vrow.option_name NOT LIKE \"_transient_timeout_sr_%\" AND
			trow.option_name = CONCAT(\"_transient_timeout_sr_\", SUBSTRING(vrow.option_name, 15))";
        $wpdb->query($sql);
    }

    public function get_rewrites()
    {
        if (!$this->api_key) {
            return array();
        }

        $rules = array();

        if ($this->metadata !== false) {
            if ($this->feed->plugin_extra_details_pages) {
                foreach ($this->metadata as $type) {
                    $rules["[^/]+/[^/]+/[^/][^/]/[^/]+/([^/]+)/(([a-z]+\\.)?{$type->system_name})/(\w+)$"] = 'index.php?sr_mls=$matches[1]&sr_type=$matches[2]&sr_method=details&sr_tmp=$matches[4]';
                    $rules["[^/]+/[^/]+/[^/][^/]/[^/]+/([^/]+)/(([a-z]+\\.)?{$type->system_name})$"] = 'index.php?sr_mls=$matches[1]&sr_type=$matches[2]&sr_method=details&sr_tmp=overview';
                }
            } else {
                foreach ($this->metadata as $type) {
                    $rules["[^/]+/[^/]+/[^/][^/]/[^/]+/([^/]+)/(([a-z]+\\.)?{$type->system_name})$"] = 'index.php?sr_mls=$matches[1]&sr_type=$matches[2]&sr_method=details';

                }
            }
        }

        $rules['sr-search(/[\d]+)?$'] = 'index.php?sr_method=search';
        $rules['sr-narrow-search(/[\d]+)?$'] = 'index.php?sr_method=narrowsearch';
        $rules['sr-cities(/[\d]*).*?$'] = 'index.php?sr_method=customsearch';
        $rules['sr-communities(/[\d]*).*?$'] = 'index.php?sr_method=customsearch';
        $rules['sr-condos(/[\d]*).*?$'] = 'index.php?sr_method=customsearch';
        $rules['sr-customsearch(/[\d]+)?$'] = 'index.php?sr_method=customsearch';
        $rules['sr-mapsearch$'] = 'index.php?sr_method=mapsearch';
        $rules['sr-css$'] = 'index.php?sr_method=css';
        $rules['sr-rc$'] = 'index.php?sr_method=rc';
        $rules['sr-ajax$'] = 'index.php?sr_method=ajax';
        $rules['sr-contact$'] = 'index.php?sr_method=contact';
        $rules['sr-subscribe$'] = 'index.php?sr_method=subscribe';
        $rules['sr-map$'] = 'index.php?sr_method=map';
        $rules['sr-sitemap\.xml$'] = 'index.php?sr_method=sitemap';
        $rules['sr-pdf$'] = 'index.php?sr_method=pdf';
        $rules['sr-signup$'] = 'index.php?sr_method=signup';
        $rules['sr-forgot$'] = 'index.php?sr_method=forgot';
        $rules['sr-login$'] = 'index.php?sr_method=login';
        $rules['sr-logout$'] = 'index.php?sr_method=logout';
        $rules['sr-reset$'] = 'index.php?sr_method=reset';
        $rules['sr-favorites$'] = 'index.php?sr_method=favorites';
        $rules['sr-search-fav$'] = 'index.php?sr_method=saveseach';
        $rules['sr-verify$'] = 'index.php?sr_method=verify';
        $rules['sr-alert$'] = 'index.php?sr_method=alert';
        $rules['sr-alerts$'] = 'index.php?sr_method=alerts';
        $rules['sr-user'] = 'index.php?sr_method=useroption';
        $rules['sr-compare'] = 'index.php?sr_method=compare';
        return $rules;
    }

    public function posts_filter($posts)
    {
        global $wp_query;
        if (!isset($wp_query->query['sr_method'])) return $posts;

        if (!defined("DONOTCACHEPAGE")) define("DONOTCACHEPAGE", true);

//keep WordPress from messing with our custom posts
        remove_filter("the_content", "wptexturize");
        remove_filter("the_content", "convert_smilies");
        remove_filter("the_content", "convert_chars");
        remove_filter("the_content", "wpautop");
        remove_filter("the_content", "prepend_attachment");
        remove_action("wp_head", "rel_canonical");
        remove_filter("the_posts", array($this, "posts_filter"));

// Keep yoast from messing with our custom posts
        if (defined('WPSEO_VERSION')) add_filter('wpseo_canonical', '__return_false');

//Remove this later
///////////////////////////////////
        $wp_query->found_posts = 0;
        $wp_query->max_num_pages = 0;
        $wp_query->is_page = 1;
        $wp_query->is_home = null;
        $wp_query->is_singular = 1;
        $post_id = time();
///////////////////////////////////
        $currentPage = new stdClass();
        $currentPage->ID = $post_id;
        $currentPage->comment_count = 0;
        $currentPage->comment_status = "closed";
        $currentPage->ping_status = "closed";
        $currentPage->post_author = 1;
        $currentPage->post_date = date("c"); //$date;
        $currentPage->post_date_gmt = date("c");
        $currentPage->post_excerpt = "";
        $currentPage->post_name = "SEORETS Page";
        $currentPage->post_parent = 0;
        $currentPage->post_status = "publish";
        $currentPage->post_type = "page";

        $method = $wp_query->query['sr_method'];
        $listAllowedMehods = array('details', 'overview', 'narrowsearch', 'search', 'alerts', 'saveseach', 'useroption',
            'customsearch', 'mapsearch', 'subscribe', 'signup', 'forgot', 'login', 'logout', 'reset', 'favorites', 'verify');

        if (!in_array($method, $listAllowedMehods)) {
            $_SERVER['REQUEST_METHOD'] = 'HEAD';
        }

        if (preg_match('/[^a-z]/', $method) === 0) {
            $method_loc = "{$this->server_plugin_dir}/methods/{$method}.php";

            if (file_exists($method_loc)) {

                include($method_loc);
            }
        }
        do_action('sr-template-redirect');
        return array($currentPage);
    }


    public function mlsid_cache($mlsids)
    {

        if (count($mlsids) == 0) {
            return array();
        }

        $mlsids = array_map('serialize', $mlsids);
        $keyarray = array_combine($mlsids, array_fill(0, count($mlsids), null));
        $needed_keys = array_keys(array_diff_key($keyarray, $this->mlsid_cache_array));
        $needed = array_map('unserialize', $needed_keys);

        if (count($needed) > 0) {

            $mergequeries = array();

            foreach ($needed as $listing) {
                $mergequeries[] = array(
                    'type' => $listing['type'],
                    'query' => array(
                        'boolopr' => 'AND',
                        'conditions' => array(
                            array(
                                'field' => 'mls_id',
                                'operator' => '=',
                                'value' => $listing['mls']
                            )
                        )
                    ),
                    'limit' => array(
                        'range' => 1
                    )
                );
            }

            $request = array(
                'query' => $mergequeries,
                'limit' => array(
                    'range' => count($needed)
                )
            );

            $response = $this->api_request('get_listings', $request);
            $new_results = array();

            if (count($response->result) == count($needed)) {

                $new_results = array_combine($needed_keys, $response->result);

            } else {

                foreach ($response->result as $listing) {

                    $requested_key = false;

                    foreach ($needed as $key => $requested) {

                        if ($requested['mls'] == $listing->mls_id) {
                            $new_results[serialize($requested)] = $listing;
                            $requested_key = $key;
                            break;
                        }

                    }

                    if ($requested_key !== false) {
                        unset($needed[$requested_key]);
                    }

                }
            }

            $this->mlsid_cache_array = array_merge($new_results, $this->mlsid_cache_array);
        }

        $results = array_intersect_key($this->mlsid_cache_array, $keyarray);
        $sorter = new sort_by_array($mlsids);
        uksort($results, array($sorter, 'sort'));

        return $results;
    }

    public function get_listing_string($l, $t)
    { //$l = listing, $t = type
        return "{$l->address}, {$l->city}, " . $this->long_state($l->state) . ": " . site_url() . $this->listing_to_url($l, $t);
    }

    public function listing_to_url($listing, $type)
    {
        $zip = $listing->zip;
        $address = $listing->address;
        $city = $listing->city;

        if (empty($zip)) {
            $zip = "0";
        }

        if (empty($address)) {
            $address = "N/A";
        }

        if (empty($city)) {
            $city = "N/A";
        }

        $components = array("", $address, $city, $this->short_state($listing->state), $zip);
        $components = array_map(array("SEO_RETS_Plugin", "pretty_url"), $components);
        $components[] = $listing->mls_id;
        $components[] = $this->to_sys_type($type);

        return implode("/", $components);
        //return "/{$this->pretty_url($listing->address)}/{$this->pretty_url($listing->city)}/{$this->pretty_url($this->short_state($listing->state))}/{$this->pretty_url($listing->zip)}/{$listing->mls_id}/{$this->to_sys_type($type)}";
    }

    public function to_sys_type($type)
    {
        $match = array();
        if (preg_match("/^([a-zA-Z]+)\\.([a-zA-Z]+)$/", $type, $match)) {
            $test = $this->to_sys_type($match[2]);
            if ($test === null) {
                $test = $match[2];
            }

            return "{$match[1]}.{$test}";
        }
        foreach ($this->metadata as $atype) {
            if ($atype->pretty_name == $type || $atype->system_name == $type) {
                return $atype->system_name;
            }
        }

        return null;
    }

    public function to_pretty_type($type)
    {
        foreach ($this->metadata as $atype) {
            if ($atype->pretty_name == $type || $atype->system_name == $type) {
                return $atype->pretty_name;
            }
        }
    }

    public static function pretty_url($seg)
    {
        $seg = strtolower($seg);
        $seg = preg_replace('/[^a-z0-9-\\s]/', '', $seg);
        $seg = preg_replace('/[\\s-]+/', '-', $seg);
        $seg = trim($seg, '-');

        return $seg;
    }

    public static function add_query_vars($vars)
    {
        return array_merge($vars, array('sr_mls', 'sr_type', 'sr_method', 'sr_tmp'));
    }

    public function convert_to_api_conditions($get_format_conditions)
    {
        if (!isset($get_format_conditions->b) || !isset($get_format_conditions->c)) {
            return null;
        } // One of the required properties wasn't set
        if (!is_array($get_format_conditions->c)) {
            return null;
        } // subconditions are invalid format

        $return_obj = new STDClass();
        $return_obj->conditions = array();
        $return_obj->boolopr = intval($get_format_conditions->b) ? "AND" : "OR";

        foreach ($get_format_conditions->c as $condition) {

            if (isset($condition->b)) {
                $return_obj->conditions[] = $this->convert_to_api_conditions($condition);
            } else {
//                if (!isset($condition->f) || !isset($condition->v)) {
//                    continue;
//                } // required things not set
                $new_fov = new STDClass();
                $new_fov->field = $condition->f;
                $new_fov->operator = isset($condition->o) ? $condition->o : "=";
                $new_fov->loose = isset($condition->l) ? 1 : 0;
                $new_fov->value = $condition->v;

                $return_obj->conditions[] = (array)$new_fov;

            }
        }

        return (array)$return_obj;
    }

    public function convert_to_api_conditions_2($get_format_conditions)
    {
        if (!isset($get_format_conditions->b) || !isset($get_format_conditions->c)) {
            return null;
        } // One of the required properties wasn't set
        if (!is_array($get_format_conditions->c)) {
            return null;
        } // subconditions are invalid format

        $return_obj = new STDClass();
        $return_obj->conditions = array();
        $return_obj->boolopr = intval($get_format_conditions->b) ? "AND" : "OR";

        foreach ($get_format_conditions->c as $condition) {

            if (isset($condition->b)) {
                $return_obj->conditions[] = $this->convert_to_api_conditions($condition);
            } else {
                if (!isset($condition->f) || !isset($condition->v)) {
                    continue;
                } // required things not set


                if ($condition->f == "address") {
                    $val = explode(" ", $condition->v);
                    $cond = array();
                    foreach ($val as $key => $addreline) {
                        $new_fov = new STDClass();
                        $new_fov->field = $condition->f;
                        $new_fov->operator = isset($condition->o) ? $condition->o : "=";
                        $new_fov->loose = isset($condition->l) ? 1 : 0;
                        $new_fov->value = trim($addreline);
                        $cond['conditions'][] = (array)$new_fov;
                    }
                    $cond['boolopr'] = 'AND';
                    $return_obj->conditions[] = (array)$cond;
                } else {
                    $new_fov = new STDClass();
                    $new_fov->field = $condition->f;
                    $new_fov->operator = isset($condition->o) ? $condition->o : "=";
                    $new_fov->loose = isset($condition->l) ? 1 : 0;
                    $new_fov->value = trim($condition->v);
                    $return_obj->conditions[] = (array)$new_fov;

                }


            }

        }

        return (array)$return_obj;
    }

    public function convert_to_search_conditions($get_format_conditions)
    {
        if (!isset($get_format_conditions['boolopr']) || !isset($get_format_conditions['conditions'])) {
            return null;
        } // One of the required properties wasn't set
        if (!is_array($get_format_conditions['conditions'])) {
            return null;
        } // subconditions are invalid format

        $return_obj = new STDClass();
        $return_obj->c = array();
        $return_obj->b = intval($get_format_conditions['boolopr']) ? "AND" : "OR";

        foreach ($get_format_conditions['conditions'] as $condition) {

            if (isset($condition['boolopr'])) {
                $return_obj->c[] = $this->convert_to_search_conditions($condition);
            } else {
                if (!isset($condition['field']) || !isset($condition['value'])) {
                    return null;
                } // required things not set

                $new_fov = new STDClass;
                $new_fov->f = $condition['field'];
                $new_fov->o = isset($condition['operator']) ? $condition['operator'] : "=";
                $new_fov->l = isset($condition['loose']) ? 1 : 0;
                $new_fov->v = $condition['value'];

                $return_obj->c[] = (array)$new_fov;
            }
        }

        return (array)$return_obj;
    }

    public function stop_request($request)
    {
        global $wp_query;
        if (!isset($wp_query->query["sr_method"])) {
            return $request;
        }

        remove_filter("posts_request", array($this, __METHOD__)); //We only stop the first request
        return '';
    }

    public function pagination_text_html($queryObj, $curPage, $lastPage, $result_count, $page_name = 'sr-search')
    {

        $pages = $this->get_page_list($curPage, $lastPage);
        $blogurl = home_url();
        $html = '';
        $current = current($pages);
        if ($curPage > 1) {
            $html .= " <a class='s1' href=\"{$blogurl}/$page_name?" . $this->getQueryPage($queryObj, $current) . "\">FIRST</a> |";
            $html .= " <a href=\"{$blogurl}/$page_name?" . $this->getQueryPage($queryObj, $curPage - 1) . '">PREV</a> |'; //previous link
        }
        if ($curPage < $lastPage) {
            $html .= ' <a href="' . $blogurl . '/' . $page_name . '?' . $this->getQueryPage($queryObj, $curPage + 1) . '">NEXT</a> |'; //next link
        }
        if ($current != $lastPage) {
            $html .= " <a href=\"{$blogurl}/$page_name?" . $this->getQueryPage($queryObj, $lastPage) . "\">LAST</a> ";
        }
        $jumpTo = '';
        $jumpTo .= " <select id='jumpto'>";
        while (($next = next($pages)) !== false) {
//            if ($current != $curPage) {
            $jumpTo .= "<option val=\"{$blogurl}/$page_name?" . $this->getQueryPage($queryObj, $current) . "\">{$current}</option>";
//            } else {
//                $jumpTo .= $current;
//            }
            $current = current($pages);
        }

        $jumpTo .= "</select>";

        $last = ($curPage * $queryObj->p);

        $end_index = ($last > $result_count) ? $last - ($queryObj->p - ($result_count % $queryObj->p)) : $last;
//        $end_index = 1;

        return number_format($result_count) > 0 ? '<div class="srm-pages">Pages: ' . $html . '<div style="float:right;">Showing ' . (($curPage * $queryObj->p) - ($queryObj->p - 1)) . '-' . $end_index . ' of ' . number_format($result_count) . '</div></div>' : null;
    }

    public function pagination_html($queryObj, $curPage, $lastPage, $result_count, $page_name = 'sr-search')
    {

        $pages = $this->get_page_list($curPage, $lastPage);
        $blogurl = home_url();
        $html = '';

        if ($curPage > 1) {
            $html .= "<a href=\"{$blogurl}/$page_name?" . $this->getQueryPage($queryObj, $curPage - 1) . '">&lt;&lt;</a> '; //previous link
        }

        $current = current($pages);

        while (($next = next($pages)) !== false) {
            if ($current != $curPage) {
                $html .= "<a href=\"{$blogurl}/$page_name?" . $this->getQueryPage($queryObj, $current) . "\">{$current}</a>";
            } else {
                $html .= $current;
            }

            if ($next - $current == 1) {
                $html .= ' | ';
            } else {
                $html .= ' ... ';
            }

            $current = current($pages);
        }

        if ($current != $curPage) {
            $html .= "<a href=\"{$blogurl}/$page_name?" . $this->getQueryPage($queryObj, $current) . "\">{$current}</a>";
        } else {
            $html .= $current;
        }

        if ($curPage < $lastPage) {
            $html .= ' <a href="' . $blogurl . '/' . $page_name . '?' . $this->getQueryPage($queryObj, $curPage + 1) . '">&gt;&gt;</a>'; //next link
        }


        $last = ($curPage * $queryObj->p);

        $end_index = ($last > $result_count) ? $last - ($queryObj->p - ($result_count % $queryObj->p)) : $last;

        return number_format($result_count) > 0 ? '<div class="srm-pages">Pages: ' . $html . '<div style="float:right;">Showing ' . (($curPage * $queryObj->p) - ($queryObj->p - 1)) . '-' . $end_index . ' of ' . number_format($result_count) . '</div></div>' : null;
    }

    public function pagination_html_customsearch($queryObj, $curPage, $lastPage, $result_count, $page_name = 'sr-customsearch')
    {


        $type = $queryObj->t;

        $object = $queryObj->q->c[0]->f;

        if ($object == "city") {
            $link = "sr-cities/";
        } elseif ($object == "subdivision") {
            $link = "sr-communities/";
        } elseif ($type == 'cnd') {
            $link = "sr-condos/";
        }
        $objectValue = $queryObj->q->c[0]->v;
        $objectValue = preg_replace('/\s/', '+', $objectValue);
        $link = $link . $objectValue . '/' . $type;


        $pages = $this->get_page_list($curPage, $lastPage);
        $blogurl = home_url();
        $html = '';
        if ($curPage > 1) {
            $temp = $curPage - 1;
            $html .= "<a href=\"{$blogurl}/" . $link . '/' . $temp . '">&lt;&lt;</a> '; //previous link
        }

        $current = current($pages);

        while (($next = next($pages)) !== false) {
            if ($current != $curPage) {
                $html .= "<a href=\"{$blogurl}/" . $link . '/' . $current . "\">{$current}</a>";
            } else {
                $html .= $current;
            }

            if ($next - $current == 1) {
                $html .= ' | ';
            } else {
                $html .= ' ... ';
            }

            $current = current($pages);
        }

        if ($current != $curPage) {
            $html .= "<a href=\"{$blogurl}/" . $link . '/' . $current . "\">{$current}</a>";
        } else {
            $html .= $current;
        }
        if ($curPage < $lastPage) {
            $temp = $curPage + 1;
            $html .= "<a href=\"{$blogurl}/" . $link . '/' . $temp . "\">&gt;&gt;</a>";
        }


        $last = ($curPage * $queryObj->p);

        $end_index = ($last > $result_count) ? $last - ($queryObj->p - ($result_count % $queryObj->p)) : $last;

        return '<div class="srm-pages">Pages: ' . $html . '<div style="float:right;">Showing ' . (($curPage * $queryObj->p) - ($queryObj->p - 1)) . '-' . $end_index . ' of ' . number_format($result_count) . '</div></div>';
    }

    public function paginate($current_page, $base_url, $per_page, $this_page, $total, $width = 3, $postfix = '')
    {

        $num_pages = ceil($total / $per_page);
        $output = '';

        $start = $current_page - $width;
        if (($current_page - $width) < 1) {
            $start = 1;
        }
        if ((($current_page + $width) > $num_pages) && (($width * 2) < $num_pages)) {
            $start = ($num_pages - ($width * 2));
        }
        if ($current_page > 1) {
            $output .= '<a href="' . $base_url . ($current_page - 1) . $postfix . '">&lt;&lt;</a> ';
        }
        if ($start > 1) {
            $output .= '<a href="' . $base_url . '1' . $postfix . '">1</a> ... ';
        }
        for ($x = $start; $x <= ($start + ($width * 2)) && $x <= $num_pages; $x++) {
            if ($current_page == $x) {
                $tag = $x;
            } else {
                $tag = '<a href="' . $base_url . $x . $postfix . '">' . $x . '</a>';
            }
            if (($x == ($start + ($width * 2))) || ($x == $num_pages)) {
                $output .= $tag;
            } else {
                $output .= $tag . ' | ';
            }
        }
        if (($start + ($width * 2)) < $num_pages) {
            $output .= ' ... <a href="' . $base_url . $num_pages . $postfix . '">' . $num_pages . '</a>';
        }
        if (($current_page < $num_pages)) {
            $output .= ' <a href="' . $base_url . ($current_page + 1) . $postfix . '">&gt;&gt;</a>';
        }

        return '<div class="srm-pages">Pages: ' . $output . '<div style="float:right;">Showing ' . (($current_page * $per_page) - ($per_page - 1)) . '-' . (($current_page * $per_page) - ($per_page - 1) + ($this_page - 1)) . ' of ' . number_format($total) . '</div></div>';
    }

    public function setup_menu_bar($wp_admin_bar)
    {

        if (!current_user_can('activate_plugins')) {
            return;
        }


        $wp_admin_bar->add_node(array(
            'id' => $this->admin_id,
            'title' => $this->admin_title,
            'href' => home_url() . "/wp-admin/admin.php?page=" . $this->admin_id
        ));

        if ($this->api_key) {
            $wp_admin_bar->add_node(array(
                'id' => $this->admin_id . '-status',
                'title' => 'Status',
                'href' => home_url() . "/wp-admin/admin.php?page=" . $this->admin_id,
                'parent' => $this->admin_id
            ));

            if ($this->feed->elite) {
                $wp_admin_bar->add_node(array(
                    'id' => $this->admin_id . '-elite',
                    'title' => 'Elite',
                    'href' => home_url() . "/wp-admin/admin.php?page=" . $this->admin_id . "-elite",
                    'parent' => $this->admin_id
                ));
            }

            $wp_admin_bar->add_node(array(
                'id' => $this->admin_id . '-branding',
                'title' => 'Plugin Branding',
                'href' => home_url() . "/wp-admin/admin.php?page=" . $this->admin_id . "-branding",
                'parent' => $this->admin_id
            ));


            $wp_admin_bar->add_node(array(
                'id' => $this->admin_id . '-developer-tools',
                'title' => 'Developer Tools',
                'href' => home_url() . "/wp-admin/admin.php?page=" . $this->admin_id . "-tools",
                'parent' => $this->admin_id
            ));

            $wp_admin_bar->add_node(array(
                'id' => $this->admin_id . '-listing-prioritization',
                'title' => 'Listing Prioritization',
                'href' => home_url() . "/wp-admin/admin.php?page=" . $this->admin_id . "-prioritization",
                'parent' => $this->admin_id
            ));

            $wp_admin_bar->add_node(array(
                'id' => $this->admin_id . '-feed-info',
                'title' => 'Feed Information',
                'href' => home_url() . "/wp-admin/admin.php?page=" . $this->admin_id . "-feed-info",
                'parent' => $this->admin_id
            ));

//            $wp_admin_bar->add_node(array(
//                'id' => $this->admin_id . '-seo-content',
//                'title' => 'SEO Content',
//                'href' => home_url() . "/wp-admin/admin.php?page=" . $this->admin_id . "-seo-content",
//                'parent' => $this->admin_id
//            ));

        }
    }

    private function setup_shortcodes()
    {

        foreach ($this->shortcodes as $shortcode) {
            add_shortcode($shortcode, array($this, "process_shortcode"));
        }
    }

    public function process_shortcode($params, $content, $tag)
    {

        if (!in_array($tag, $this->shortcodes)) {
            //well dang, that's an error
            return;
        }

        return $this->include_return("shortcodes/{$tag}.php", array(
            "params" => $params,
            "seo_rets_plugin" => $this
        ));
    }

    public function setup_menu()
    {
        if ($this->api_key) {
            add_menu_page($this->admin_title, $this->admin_title, 'activate_plugins', $this->admin_id, '', $this->plugin_dir . 'resources/images/icon.png');

            add_submenu_page($this->admin_id, $this->admin_title . ' :: Status', 'Status', 'activate_plugins', $this->admin_id, create_function('', '
				global $seo_rets_plugin; include("menu/status.php");
			'));

            if ($this->feed->elite) {
                add_submenu_page($this->admin_id, $this->admin_title . ' :: Elite', 'Elite', 'activate_plugins', $this->admin_id . '-elite', create_function('', '
					global $seo_rets_plugin; include("menu/elite.php");
				'));
            }

            add_submenu_page($this->admin_id, $this->admin_title . ' :: Plugin Branding', 'Plugin Branding', 'activate_plugins', $this->admin_id . '-branding', create_function('', '
				global $seo_rets_plugin; include("menu/branding.php");
			'));

            add_submenu_page($this->admin_id, $this->admin_title . ' :: Developer Tools', 'Developer Tools', 'activate_plugins', $this->admin_id . '-tools', create_function('', '
				global $seo_rets_plugin; include("menu/developer-tools.php");
			'));
            add_submenu_page($this->admin_id, $this->admin_title . ' :: Shortcode Generator', 'Shortcode Generator', 'activate_plugins', $this->admin_id . '-shortcode', create_function('', '
				global $seo_rets_plugin; include("menu/shortcode-generator.php");
			'));
            add_submenu_page($this->admin_id, $this->admin_title . ' :: Listing Prioritization', 'Listing Prioritization', 'activate_plugins', $this->admin_id . '-prioritization', create_function('', '
				global $seo_rets_plugin; include("menu/listing-prioritization.php");
			'));

            add_submenu_page($this->admin_id, $this->admin_title . ' :: Feed Information', 'Feed Information', 'activate_plugins', $this->admin_id . '-feed-info', create_function('', '
				global $seo_rets_plugin; include("menu/feed-information.php");
			'));
            if ($this->feed->agent_crm && $this->crm == "true") {
                add_submenu_page($this->admin_id, $this->admin_title . ' :: CRM', 'CRM', 'activate_plugins', $this->admin_id . '-crm', create_function('', '
				global $seo_rets_plugin; include("menu/signup.php");
			'));
            }

//            add_submenu_page($this->admin_id, $this->admin_title . ' :: SEO Content', 'SEO Content', 'activate_plugins', $this->admin_id . '-seo-content', create_function('', '
//				global $seo_rets_plugin; include("menu/seo_content.php");
//			'));


            add_filter("mce_external_plugins", array($this, "AddTinyMcePlugin"));
            add_filter("mce_buttons", array($this, "RegisterTinyMceButton"));
        } else {
            add_menu_page($this->admin_title . ' :: Setup', 'SEO RETS', 'activate_plugins', $this->admin_id, create_function('', '
				global $seo_rets_plugin; include("menu/setup.php");
			'), $this->plugin_dir . 'resources/images/icon.png');
        }
    }

    public function parse_url_to_vars()
    {
        return json_decode(base64_decode(urldecode($_SERVER['QUERY_STRING'])));
    }

    /*
    private function swap_api_host() {

              if ( isset($this->has_swapped) ) {
                        update_option('sr_apihost', 'api.seorets.com');
                        $this->api_host = 'api.seorets.com';
                        $this->api_url  = "http://" . $this->api_host . "/v" . $this->api_version;
                        return false;
              }

              $this->has_swapped = true;
              update_option('sr_apihost', 'api2.seorets.com');
              $this->api_host = 'api2.seorets.com';
              $this->api_url  = "http://" . $this->api_host . "/v" . $this->api_version;
              return true;

    }
    */

    public function api_request($method, $parameters = null, $allowcache = true, $save = true)
    {
        if (get_option('sr_listingsOrder') != "none") {
            $order = "none";

            if (get_option('sr_listingsOrder') == 'heightolow') {
                $order = "DESC";
            } else {
                $order = "ASC";
            }
            if ($method == "get_listings") {
                if (!$parameters['query'][0]['order'] && isset($parameters['query'][0])) {
                    $srt = array(
                        'field' => 'price',
                        'order' => $order,
                    );
                    foreach ($parameters['query'] as $d => $q) {
                        $parameters['query'][$d]['order'][] = $srt;
                    }
                }
            }
        }
//        $t = $parameters['limit']['range'] + $parameters['limit']['offset'];
//        if($t > 250) {
//            $q = $t - 250;
//            $parameters['limit']['range'] = $parameters['limit']['range']- $q;
//        }
        $request = array(
            'api-key' => $this->api_key,
            'request' => json_encode(array("method" => $method, "parameters" => $parameters))
        );
        $response = json_decode($this->http_request($this->api_url, $request));

        return $response;
    }

    private function buildXForwarded()
    {

        if (function_exists('apache_request_headers')) {

            $headers = apache_request_headers();

            if (isset($headers['X-Forwarded-For'])) {
                return "X-Forwarded-For: {$headers['X-Forwarded-For']}, {$_SERVER['REMOTE_ADDR']}";
            }

        }

        return "X-Forwarded-For: {$_SERVER['REMOTE_ADDR']}";
    }

    public function http_request($url, $post = null, &$mime = null)
    {
        $ch = curl_init();
        $headers = array($this->buildXForwarded());


        if ($post !== null) {

            $post_string = '';

            foreach ($post as $key => $value) {
                $post_string .= $key . '=' . urlencode($value) . '&';
            }

            rtrim($post_string, '&');

            curl_setopt($ch, CURLOPT_POST, count($post));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
        }

        curl_setopt($ch, CURLOPT_USERAGENT, "SEORETS/{$this->plugin_version}");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);

        $result = curl_exec($ch);

        $mime = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);

        curl_close($ch);


        return $result;
    }

    public function remove_field_from_get($fields, $get)
    {
        foreach ($get['conditions'] as $key => $value) {
            if (in_array($value['field'], $fields)) {
                unset($get['conditions'][$key]);
            }
        }

        return $get;
    }

    public function arr_to_hidden($arr, $parents = array())
    { // Converts an associative array to a string of hidden fields

        $hidden_string = '';

        foreach ($arr as $key => $value) {
            $keys = $parents;
            $keys[] = $key;

            if (is_array($value)) {
                $hidden_string .= $this->arr_to_hidden($value, $keys);
            } else {

                $name_string = htmlentities($keys[0]);

                unset($keys[0]);
                $keys = array_values($keys);

                foreach ($keys as $k) {
                    $name_string .= '[' . htmlentities($k) . ']';
                }

                $hidden_string .= '<input type="hidden" name="' . $name_string . '" value="' . htmlentities($value) . '" />';

            }
        }

        return $hidden_string;
    }

    public function build_conditions($params)
    {
        $conditions = array();
        foreach ($params as $aparam) {

            $field = $aparam['field'];
            $param = $aparam['value'];
            $cast = $aparam['cast'];
            $ors = explode(",", $param);

            if (count($ors) > 1) {
                foreach ($ors as $or) {
                    $or_conds[] = $this->parse_shortcode_param($or, $field, $cast);
                }
                $conditions[] = array(
                    'boolopr' => 'OR',
                    'conditions' => $or_conds
                );
            } else {
                $conditions[] = $this->parse_shortcode_param($param, $field, $cast);
            }
            unset($or_conds);
        }

        return $conditions;
    }

    public function filter_params($params, $type)
    {

        $new_params = array();
        foreach ($params as $key => $value) {

            $newkey = rtrim($key, '0123456789');
            if ($this->is_field_valid($type, $newkey)) {
                $new_params[] = array('field' => $newkey, 'value' => $params[$key]);
            }
        }

        return $new_params;
    }


    public function parse_shortcode_param($param, $field, $cast)
    {
        $parts = explode(":", html_entity_decode($param), 2);

        if (count($parts) == 2) {

            //$parts[0] = str_replace("&gt;", ">", $parts[0]);
            //$parts[0] = str_replace("&lt;", "<", $parts[0]);

            //$parts[0] = html_entity_decode($parts[0]);

            $operator = $parts[0];
            $value = $parts[1];
        } else { // cast the data for api
            if ($cast == "numeric") {
                $operator = "&ei";
                $value = $parts[0];
            } else {
                $operator = "=";
                $value = $parts[0];
            }
        }


        if ($operator == "LIKE") {
            $condition = array(
                'field' => $field,
                'operator' => $operator,
                'value' => trim($value),
                'loose' => true
            );
        } else {
            $condition = array(
                'field' => $field,
                'operator' => $operator,
                'value' => trim($value)
            );
        }

        return $condition;


    }

    public function is_type_valid($atype)
    {
        if (preg_match("/^([a-zA-Z]+)\\.([a-zA-Z]+)$/", $atype)) {
            return true;
        }
        if ($atype) {
            if ($atype == "all") {
                return true;
            } else {
                foreach ($this->metadata as $type) {
                    if ($type->pretty_name == $atype || $type->system_name == $atype) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    public function is_field_valid($atype, $afield)
    {

        if (preg_match("/^([a-zA-Z]+)\\.([a-zA-Z]+)$/", $atype) == 1 && $afield != "type") {
            return !in_array($afield, array(
                "limit",
                "order",
                "perpage",
                "onlymylistings",
                "disablepagination",
                "silent",
                "object"
            ));
        }
        if ($atype == "all") {
            foreach ($this->metadata as $type) {
                foreach ($type->fields as $field) {
                    if ($field->pretty_name == $afield || $field->system_name == $afield) {
                        return true;
                    }
                }
            }
        } else {
            foreach ($this->metadata as $type) {
                if ($type->pretty_name == $atype || $type->system_name == $atype) {
                    foreach ($type->fields as $field) {
                        if ($field->pretty_name == $afield || $field->system_name == $afield) {
                            return true;
                        }
                    }
                }
            }
        }


        return false;
    }

    public static function is_type_hidden($type)
    {
        return (bool)preg_match("/^h-/", $type);
    }

    public function check_requirements()
    {
        $errors = false;

        $functions = array(
            'curl_init' => 'cURL Library Required'
        );

        foreach ($functions as $function => $description) {
            if (!function_exists($function)) {
                $errors[] = $description;
            }
        }

        if (version_compare(PHP_VERSION, '5.2.0', '<')) {
            $errors[] = 'PHP Version > 5.2.0 Required';
        }

        if (get_option('permalink_structure') != "/%postname%" && get_option('permalink_structure') != "/%postname%/") {
            $errors[] = 'You must set your permalink structure to "/%postname%" or "/%postname%/"';
        }


        return $errors;
    }

    public function prioritize($request, $prioritization)
    {
        $mergerequests = array();
        $requestconditions = $request['query'];
        $removeconditions = array();

        foreach ($prioritization as $priority) {
            $newrequest = $request;
            $newrequest['query'] = array(
                'boolopr' => "AND",
                'conditions' => array(
                    $requestconditions,
                    array(
                        'field' => $priority['field'],
                        'operator' => "=",
                        'value' => $priority['id']
                    )
                )
            );

            $newrequest['query']['conditions'] = array_merge($newrequest['query']['conditions'], $removeconditions);
            $mergerequests[] = $newrequest;
            $removeconditions[] = array(
                'field' => $priority['field'],
                'operator' => "<>",
                'value' => $priority['id']
            );
        }
        $request['query']['conditions'] = array_merge($request['query']['conditions'], $removeconditions);
        $mergerequests[] = $request;

        return $mergerequests;
    }

    public function AddTinyMcePlugin($plugins)
    {
        $plugins["srproperties"] = $this->plugin_dir . "tinymce/listings/editor_plugin.js";
        $plugins["srsearch"] = $this->plugin_dir . "tinymce/search/editor_plugin.js";
        $plugins["srmaps"] = $this->plugin_dir . "tinymce/maps/editor_plugin.js";
        $plugins["sralerts"] = $this->plugin_dir . "tinymce/alerts/editor_plugin.js";
        $plugins["srfeatured"] = $this->plugin_dir . "tinymce/featured/editor_plugin.js";

        return $plugins;
    }

    public static function RegisterTinyMceButton($buttons)
    {
        array_push($buttons, "separator", "srproperties", "srfeatured", "srsearch", "srmaps", "sralerts");

        return $buttons;
    }


    public function getQueryPage($queryObj, $page)
    {
        $queryObj = clone $queryObj;
        $queryObj->g = $page;

        return urlencode(base64_encode(json_encode($queryObj)));
    }

    public function get_page_list($curPage, $lastPage, $radius = 2)
    {
        $result = array(1, $lastPage);
        $start = max($curPage - $radius, 1);
        $result = array_unique(array_merge($result, range($start, $start + (2 * $radius))));
        $filter = new filter_by_range(1, $lastPage);
        $result = array_filter($result, array($filter, 'filter'));
        sort($result);

        return $result;
    }

    public static function sendEmail($to, $subject, $message, $additional_headers = '')
    {
        try {
            if (get_option('sr_emailmethod') == 'use_wp_mail') {
                $res = wp_mail($to, $subject, $message, $additional_headers);

                return $res;
            } else {
                $res = mail($to, $subject, $message, $additional_headers);

                return $res;
            }
        } catch (Exception $ex) {
            return false;
        }
    }

}

class filter_by_range
{
    public function filter_by_range($start, $end)
    {
        $this->start = $start;
        $this->end = $end;
    }

    public function filter($element)
    {
        return $this->start <= $element && $element <= $this->end;
    }
}

class sort_by_array
{
    public function sort_by_array($array)
    {
        $this->array = array_values($array);
    }

    public function sort($keyone, $keytwo)
    {
        return array_search($keyone, $this->array) - array_search($keytwo, $this->array);
    }
}

$seo_rets_plugin = new SEO_RETS_Plugin();

class Version
{

    public $version_array = array();

    function __construct($version_string)
    {
        $version = explode(".", trim($version_string));

        for ($n = 0; $n < 3; $n++) {
            $this->version_array[$n] = isset($version[$n]) ? intval($version[$n]) : 0;
        }
    }

    public function is_greater_version($version)
    {
        for ($n = 0; $n < 3; $n++) {
            if ($this->version_array[$n] < $version->version_array[$n]) {
                return true;
            }
        }

        return false;
    }

    public function next_major()
    {
        $this->version_array[0]++;
    }

    public function next_minor()
    {
        $this->version_array[1]++;
    }

    public function next_build()
    {
        $this->version_array[2]++;
    }

    public function to_string()
    {
        $str = "";
        foreach ($this->version_array as $part) {
            $str .= $part . ".";
        }

        return rtrim($str, ".");
    }

}
