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
			
			<h3>GitHub Authorization Style</h3>
			Many requests to the GitHub API require user authentication. There are two ways to do this. By default, each
			Wordpress user must have a GitHub account and enter those credentials under the GitHub Credentials section. 
			This is allows more control over permissions, but also means we need to store GitHub passwords in the database, 
			which sucks from a security standpoint. If you enable <i>Single user</i> below you can create a throw-away GitHub 
			user account and then only those credentials will be stored.
			<p>
				<input type="hidden" name="wpghdash_auth_single_user" value=0 />
				<input type="checkbox" name="wpghdash_auth_single_user" value=1 <?php echo (get_option('wpghdash_auth_single_user')) ? "checked" : NULL; ?>/> Enable single user authentication</label>
			</p>


			<?php if (get_option('wpghdash_auth_single_user')) : ?>
			<!-- fields for credentials -->
			<h3>GitHub Single User Credentials</h3>
			Enter the credentials for the gitHub user account that Pipeline will use to interact with GitHub.
			<p>
			<label>GitHub Single User Name:</label>
			<input class="" type="text" name="wpghdash_client_id" value="<?php echo get_option('wpghdash_client_id'); ?>" />
			</p>
			<p>
			<label>GitHub Single User Password:</label>
			<input class="" type="password" name="wpghdash_client_secret" value="<?php echo get_option('wpghdash_client_secret'); ?>" />
			</p>
			<?php endif; ?>

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
	$singleuser = (!empty($_POST['wpghdash_auth_single_user'])) ? $_POST['wpghdash_auth_single_user'] : NULL;

	#don't unset values when these aren't included in the form
	if ($client_id || $client_secret){
		update_option( 'wpghdash_client_id', $client_id, TRUE );
		update_option( 'wpghdash_client_secret', $client_secret, TRUE );
	}
	
	update_option( 'wpghdash_gh_repo', $repo, TRUE );
	update_option('wpghdash_gh_org', $org, TRUE);
	update_option( 'wpghdash_auth_single_user', $singleuser, TRUE);

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

/**
ADD THE GITHUB CREDENTIAL FIELDS TO USER PROFILE PAGE
*/
add_action( 'show_user_profile', 'wpghdash_extra_user_profile_fields' );
add_action( 'edit_user_profile', 'wpghdash_extra_user_profile_fields' );
add_action( 'personal_options_update', 'wpghdash_save_extra_user_profile_fields' );
add_action( 'edit_user_profile_update', 'wpghdash_save_extra_user_profile_fields' );
 
function wpghdash_save_extra_user_profile_fields( $user_id )
 {
 if ( !current_user_can( 'edit_user', $user_id ) ) { return false; }
 	update_user_meta( $user_id, 'wpghdash_gh_username', $_POST['wpghdash_gh_username'] );
 	update_user_meta( $user_id, 'wpghdash_gh_pwd', $_POST['wpghdash_gh_pwd'] );
 }
#Developed By wpghdash , http://wpghdash.com
function wpghdash_extra_user_profile_fields( $user )
{ ?>
	<h3>GitHub Credentials</h3>

	<table class="form-table">
	<tr>
	<th><label for="wpghdash_gh_username">GitHub User Name</label></th>
	<td>
	<input type="text" id="wpghdash_gh_username" name="wpghdash_gh_username" size="20" value="<?php echo esc_attr( get_the_author_meta( 'wpghdash_gh_username', $user->ID )); ?>">
	<span class="description">Your GitHub username, eg: emersonthis</span>
	</td>
	</tr>
	<tr>
	<th><label for="wpghdash_gh_pwd">GitHub Password</label></th>
	<td>
	<input type="password" id="wpghdash_gh_pwd" name="wpghdash_gh_pwd" size="20" value="<?php echo esc_attr( get_the_author_meta( 'wpghdash_gh_pwd', $user->ID )); ?>">
	<span class="description">Your GitHub password</span>
	</td>
	</tr>
	</table>
<?php }

require_once 'vendor/autoload.php';
require_once('shortcodes.php');
require_once('github.php');