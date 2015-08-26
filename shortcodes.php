<?php

/* REGISTER SHORTCODES AND CALLBACKS */

function wpghpl_milestones_func( $atts ) {
	
	$gh = new WPGHPL_Github();
	if (!$gh->has_settings)
		return $gh->missing_settings_msg;

	$milestones = $gh->get_milestones($atts);

	return WPGHPL\format_milestones( $milestones ); 
}
add_shortcode( 'gh_milestones', 'wpghpl_milestones_func' );


function wpghpl_issues_func( $atts ) {

	$show_body = ( !empty($atts['show_body']) ) ? $atts['show_body'] : FALSE;

	#if a search term was entered do nothing.
	if ( !empty($_GET['gh_searchterm']) )
		return;

	$gh = new WPGHPL_Github();
	if (!$gh->has_settings)
		return $gh->missing_settings_msg;

	$issues = $gh->get_issues($atts);
	
	$return = WPGHPL\format_issues($issues, $show_body);
	//$return = WPGHPL\prepend_page_count($return, $gh, count($issues) );
	$return = WPGHPL\append_page_links($return, $gh);

	return $return;

}
add_shortcode( 'gh_issues', 'wpghpl_issues_func' );


function wpghpl_searchform_func( $atts ) {

	$placeholder = ( !empty($atts['placeholder']) ) ? $atts['placeholder'] : FALSE;
	$show_body = ( !empty($atts['show_body']) ) ? $atts['show_body'] : FALSE;

	$gh = new WPGHPL_Github();
	if (!$gh->has_settings)
		return $gh->missing_settings_msg;

	$results = NULL;
	$msg = 'Enter a search term to begin.';

	$return = WPGHPL\build_search_form($placeholder);

	if ( !empty($_GET['gh_searchterm'] )) {
		if (strlen( $_GET['gh_searchterm'] ) < 2) {
			$msg = 'Search term is too short!';
			$results = 0;

		} else {

			$atts['term'] = $_GET['gh_searchterm'];

			$issues = $gh->search_issues( $atts );
			$results = count($issues);
			$msg = "Results: " . $results;

		}
	
	}

	$return .= '<div class="gh_searchform__msg">' . $msg . '</div>';

	$return .= (!empty($issues)) ? WPGHPL\format_issues($issues, $show_body) : NULL;
	// $return .= append_page_links($return, $gh);

	return $return;

}
add_shortcode( 'gh_searchform', 'wpghpl_searchform_func' );