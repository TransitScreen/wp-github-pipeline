<?php
/**
Plugin Name: WP GitHub Pipeline
Description: Create a custom wordpress dashboard...
Version: 0.1.0
Author: Team TransitScreen
Author URI: http://transitscreen.com/
License: GPLv2 or later
Text Domain: wpgithubdash
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

# Track plugin version for future upgrades
if (!defined('WPGHDASH_VERSION_KEY'))
    define('WPGHDASH_VERSION_KEY', 'wpghdash_version');
if (!defined('WPGHDASH_VERSION_NUM'))
    define('WPGHDASH_VERSION_NUM', '0.1.0');
add_option(WPGHDASH_VERSION_KEY, WPGHDASH_VERSION_NUM);


#register the menu
add_action( 'admin_menu', 'wpghdash_plugin_menu' );

#add it to the tools panel
function wpghdash_plugin_menu() {
	add_submenu_page( 'options-general.php', 'GitHub', 'GitHub', 'manage_options', 'wpghdash', 'wpghdash_plugin_options');
}


#print the markup for the page
function wpghdash_plugin_options() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}

	echo '<div class="wrap">';

	echo '<h2>GitHub Settings</h2>';

	if ( isset($_GET['status']) && $_GET['status']=='success') { 
	?>
		<div id="message" class="updated notice is-dismissible">
			<p>Settings updated!</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
		</div>
	<?php
	}

	?>
		<form method="post" action="/wp-admin/admin-post.php">

			<input type="hidden" name="action" value="update_wpghdash_settings" />

			<h3>GitHub Repository Info</h3>
			<p>
			<label>GitHub Organization:</label>
			<input class="" type="text" name="wpghdash_gh_org" value="<?php echo get_option('wpghdash_gh_org'); ?>" />
			</p>

			<p>
			<label>GitHub repository (slug):</label>
			<input class="" type="text" name="wpghdash_gh_repo" value="<?php echo get_option('wpghdash_gh_repo'); ?>" />
			</p>

			<a href="https://github.com/settings/applications/new">Register a new gitHub application...</a>
			

			<!-- fields for credentials -->
			<h3>GitHub Global User Credentials</h3>
			Enter the credentials for the gitHub user account that Pipeline will use to interact with GitHub.
			<p>
			<label>GitHub Global User:</label>
			<input class="" type="text" name="wpghdash_client_id" value="<?php echo get_option('wpghdash_client_id'); ?>" />
			</p>
			<p>
			<label>GitHub Global Password:</label>
			<input class="" type="text" name="wpghdash_client_secret" value="<?php echo get_option('wpghdash_client_secret'); ?>" />
			</p>

			<input class="button button-primary" type="submit" value="Save" />
		</form>

	<?php
	echo '</div>';
}

#callback for handling the request
function wpghdash_handle_request() {

	#check which options were sent
	$client_id = (!empty($_POST['wpghdash_client_id'])) ? $_POST['wpghdash_client_id'] : NULL;
	$client_secret = (!empty($_POST['wpghdash_client_secret'])) ? $_POST['wpghdash_client_secret'] : NULL;
	$repo = (!empty($_POST['wpghdash_gh_repo'])) ? $_POST['wpghdash_gh_repo'] : NULL;
	$org = (!empty($_POST['wpghdash_gh_org'])) ? $_POST['wpghdash_gh_org'] : NULL;

	update_option( 'wpghdash_client_id', $client_id, TRUE );
	update_option( 'wpghdash_client_secret', $client_secret, TRUE );
	update_option( 'wpghdash_gh_repo', $repo, TRUE );
	update_option('wpghdash_gh_org', $org, TRUE);

	#redirect back to page
	$redirect_url = get_bloginfo('url') . "/wp-admin/options-general.php?page=wpghdash&status=success";
    header("Location: ".$redirect_url);
    exit;
}

#register the action that the form submits to
add_action( 'admin_post_update_wpghdash_settings', 'wpghdash_handle_request' );

#helpers
function wpghdash_formatdate($data_str, $format=NULL) {
	$format = ($format) ? $format : 'F j, Y';
	return date_i18n( $format, strtotime($data_str) );
}

# Add css
function wpghdash_include_style(){
	//TODO: Make this conditional, based on optional setting
	wp_enqueue_style('wpghdash_styles', plugins_url('css/style.css', __FILE__)); 
}
add_action( 'wp_enqueue_scripts', 'wpghdash_include_style' );


# Here we register scripts into the footer, but we DONT enque them yet. The shortcodes will do that.
add_action( 'wp_enqueue_scripts', 'register_wpghdash_script' );
function register_wpghdash_script() {
	wp_register_script( 'angular', plugins_url( '/vendor/angularjs/angular.min.js' , __FILE__ ), array(), NULL, true );
	wp_register_script( 'module', plugins_url( '/js/module.js' , __FILE__ ), array('angular'), NULL, true );
	wp_register_script( 'toggle', plugins_url( '/js/form-toggle-btn.js' , __FILE__ ), array('module'), NULL, true );
}

# Here we wrap the content with a div with the Angular ng-app attribute
add_filter('the_content', 'wpghdash_wrap_ng_app');
function wpghdash_wrap_ng_app($content) {
        return '<div class="pipeline-wrap" ng-app="pipeline">'.$content . '</div>';
}


require_once 'vendor/autoload.php';
require_once('shortcodes.php');
require_once('github.php');