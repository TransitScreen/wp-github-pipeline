<?php

/**
Plugin Name: WP GitHub Pipeline
Plugin URI: https://github.com/TransitScreen/wp-github-pipeline
Description: Creates a custom dashboard for your GitHub projects
Version: 1.2
Author: TransitScreen
Author URI: http://transitscreen.com/
License: GPLv2 or later
Text Domain: wpghpl
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );


# Track plugin version for future upgrades
if (!defined('WPGHDASH_VERSION_KEY'))
    define('WPGHDASH_VERSION_KEY', 'wpghpl_version');
if (!defined('WPGHDASH_VERSION_NUM'))
    define('WPGHDASH_VERSION_NUM', '1.0');
add_option(WPGHDASH_VERSION_KEY, WPGHDASH_VERSION_NUM);

require_once 'vendor/autoload.php';
require_once 'helpers.php';
require_once('shortcodes.php');
require_once('github.php');

#register the menu
add_action( 'admin_menu', 'wpghpl_plugin_menu' );
function wpghpl_plugin_menu() {
	add_submenu_page( 'options-general.php', 'GitHub', 'GitHub', 'manage_options', 'wpghpl', 'wpghpl_plugin_options');
}
#print the markup for the page
function wpghpl_plugin_options() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}

	wpghpl_handle_authentication_redirection();

	$token = get_option('wpghpl_token');

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

			<input type="hidden" name="action" value="update_wpghpl_settings" />


			<h3>GitHub Repository Info</h3>
			<p>
			<label>GitHub Organization:</label>
			<input class="" type="text" name="wpghpl_gh_org" value="<?php echo get_option('wpghpl_gh_org'); ?>" />
			</p>

			<p>
			<label>GitHub repository (slug):</label>
			<input class="" type="text" name="wpghpl_gh_repo" value="<?php echo get_option('wpghpl_gh_repo'); ?>" />
			</p>

			<?php $client_id = get_option('wpghpl_client_id'); ?>

			<?php if (get_option('wpghpl_auth_single_user') || TRUE) : ?>
			<!-- fields for credentials -->
			<h3>GitHub Application Credentials</h3>

			<p>NOTE: If you're repository is public you can skip this step</p>

			<p><a href="https://github.com/settings/applications/new">Register a new gitHub application...</a></p>

			<p><strong>IMPORTANT:</strong> Enter the homepage of your site in the field labeled: "Authorization callback URL".</p>

			Enter the credentials provided by GitHub for your registered application.
			<p>
			<label>GitHub Application Client ID:</label>
			<input class="" type="text" name="wpghpl_client_id" value="<?php echo $client_id; ?>" />
			</p>
			<p>
			<label>GitHub Application Client Secret:</label>
			<input class="" type="password" name="wpghpl_client_secret" value="<?php echo get_option('wpghpl_client_secret'); ?>" />
			</p>
			<?php endif; ?>

			<input class="button button-primary" type="submit" value="Save" />
		</form>

	<?php if ( get_option('wpghpl_client_id') && get_option('wpghpl_client_secret') ) : ?>

		<?php
		$redirect_uri = admin_url('options-general.php?page=wpghpl');
		$state = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 10);
		update_option('wpghpl_auth_state', $state);
		$auth_url = "https://github.com/login/oauth/authorize?state={$state}&client_id={$client_id}&scope=repo&redirect_uri={$redirect_uri}";
		?>

		<p>
			<?php if (!$token) : ?>
			<a class="button button-primary" href="<?php echo $auth_url; ?>">Authorize Pipeline to talk to GitHub</a>
			<?php else : ?> 
			<span>Pipeline is authorized! You're ready to go.</span>
			<?php endif; ?>
		</p>
	<?php
	endif;

	echo '</div>';
}

#register the action that the form submits to
add_action( 'admin_post_update_wpghpl_settings', 'wpghpl_handle_save' );
function wpghpl_handle_save() {

	#check which options were sent
	$client_id = (!empty($_POST['wpghpl_client_id'])) ? $_POST['wpghpl_client_id'] : NULL;
	$client_secret = (!empty($_POST['wpghpl_client_secret'])) ? $_POST['wpghpl_client_secret'] : NULL;
	$repo = (!empty($_POST['wpghpl_gh_repo'])) ? $_POST['wpghpl_gh_repo'] : NULL;
	$org = (!empty($_POST['wpghpl_gh_org'])) ? $_POST['wpghpl_gh_org'] : NULL;
	$singleuser = (!empty($_POST['wpghpl_auth_single_user'])) ? $_POST['wpghpl_auth_single_user'] : NULL;

	#if the repo changes, remember to unset the is_public flag in the DB
	if ($repo != get_option('wpghpl_gh_repo') ) {
		wpghpl_save_repo_is_public($org, $repo);
	}

	update_option( 'wpghpl_client_id', $client_id, TRUE );
	update_option( 'wpghpl_client_secret', $client_secret, TRUE );
	update_option( 'wpghpl_gh_repo', $repo, TRUE );
	update_option('wpghpl_gh_org', $org, TRUE);

	#redirect back to page
	$redirect_url = get_bloginfo('url') . "/wp-admin/options-general.php?page=wpghpl&status=success";
    header("Location: ".$redirect_url);
    exit;
}

function wpghpl_save_repo_is_public($user, $repo) {

	$client = new \Github\Client();
	
	#github throws a 404 error if the repo is private
	try {
		$repo = $client->api('repo')->show($user, $repo);
		$public = (!$repo['private']);
		update_option('wpghpl_repo_is_public', $public );
	} catch (Exception $e) {
		update_option('wpghpl_repo_is_public', 0 );
	}

}


# Add css
add_action( 'wp_enqueue_scripts', 'wpghpl_include_style' );
function wpghpl_include_style(){
	//TODO: Make this conditional, based on optional setting
	wp_enqueue_style('wpghpl_styles', plugins_url('css/style.css', __FILE__)); 
}

# Here we register scripts into the footer, but we DONT enque them yet. The shortcodes will do that.
add_action( 'wp_enqueue_scripts', 'register_wpghpl_script' );
function register_wpghpl_script() {
	wp_register_script( 'angular', plugins_url( '/vendor/angularjs/angular.min.js' , __FILE__ ), array(), NULL, true );
	wp_register_script( 'module', plugins_url( '/js/module.js' , __FILE__ ), array('angular'), NULL, true );
	wp_register_script( 'toggle', plugins_url( '/js/form-toggle-btn.js' , __FILE__ ), array('module'), NULL, true );
}

# Here we wrap the content with a div with the Angular ng-app attribute
add_filter('the_content', 'wpghpl_wrap_ng_app', 999);
function wpghpl_wrap_ng_app($content) {
        return '<div class="pipeline-wrap" ng-app="pipeline">'.$content . '</div>';
}

/** 
 * Check for whether code and/or state params are being passed back from GitHub after 
 * user authorizes the regsitered app. If so, exchange for token and save.  
 */
function wpghpl_handle_authentication_redirection() {

	#check if we're receiving the GitHub temporary code
	$code = ( !empty($_GET['code']) ) ? $_GET['code'] : FALSE; 
	$state = ( !empty($_GET['state']) ) ? $_GET['state'] : FALSE; 

	if (!$code)
		return;

	$saved_state = get_option('wpghpl_auth_state');

	if ($state != $saved_state)
		return; //TODO: This should throw an error!

	update_option('wpghpl_auth_code', $code);

	//TODO: The php-githup-api library can probably do this easier
	//TODO: This should handle non-success scenarios, like user NOT granting access
	$guzzle = new \Guzzle\Http\Client('https://github.com');
	$guzzle->setDefaultOption('headers', array('Accept' => 'application/json'));
	$body = array
	(
		'client_id' => get_option('wpghpl_client_id'),
		'client_secret' =>get_option('wpghpl_client_secret'),
		'code' => $code,
		'redirect_uri' => admin_url('options-general.php?page=wpghpl'),
		'state' => $state
	);
	$request = $guzzle->post('https://github.com/login/oauth/access_token', null, $body );
	$response = $request->send();

	$data = $response->json();

	if (!empty($data['access_token']))
		update_option('wpghpl_token', $data['access_token']);

}

function dump($var) {
	echo "<pre>";
	var_dump($var);
	echo "</pre>";
}

/**
ADD THE GITHUB CREDENTIAL FIELDS TO USER PROFILE PAGE
*/
/*
add_action( 'show_user_profile', 'wpghpl_extra_user_profile_fields' );
add_action( 'edit_user_profile', 'wpghpl_extra_user_profile_fields' );
add_action( 'personal_options_update', 'wpghpl_save_extra_user_profile_fields' );
add_action( 'edit_user_profile_update', 'wpghpl_save_extra_user_profile_fields' );
 
function wpghpl_save_extra_user_profile_fields( $user_id )
{
	if ( !current_user_can( 'edit_user', $user_id ) ) { return false; }
 	update_user_meta( $user_id, 'wpghpl_gh_username', $_POST['wpghpl_gh_username'] );
 	update_user_meta( $user_id, 'wpghpl_gh_pwd', $_POST['wpghpl_gh_pwd'] );
}

function wpghpl_extra_user_profile_fields( $user )
{ ?>
	<h3>GitHub Credentials</h3>

	<table class="form-table">
	<tr>
	<th><label for="wpghpl_gh_username">GitHub User Name</label></th>
	<td>
	<input type="text" id="wpghpl_gh_username" name="wpghpl_gh_username" size="20" value="<?php echo esc_attr( get_the_author_meta( 'wpghpl_gh_username', $user->ID )); ?>">
	<span class="description">Your GitHub username, eg: emersonthis</span>
	</td>
	</tr>
	<tr>
	<th><label for="wpghpl_gh_pwd">GitHub Password</label></th>
	<td>
	<input type="password" id="wpghpl_gh_pwd" name="wpghpl_gh_pwd" size="20" value="<?php echo esc_attr( get_the_author_meta( 'wpghpl_gh_pwd', $user->ID )); ?>">
	<span class="description">Your GitHub password</span>
	</td>
	</tr>
	</table>
<?php }
*/