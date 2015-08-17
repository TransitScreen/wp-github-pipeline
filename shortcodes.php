<?php
/* REGISTER SHORTCODES AND CALLBACKS */

function milestones_func( $atts ) {
	$atts = shortcode_atts( array(
		'state' => 'all',
		'direction' => 'asc',
		'sort' =>'due_on'
	), $atts, 'gh_milestones' );
	
	$gh = new Github();
	if (!$gh->has_settings)
		return $gh->missing_settings_msg;

	$milestones = $gh->get_milestones($atts);

	return format_milestones( $milestones ); 
}
add_shortcode( 'gh_milestones', 'milestones_func' );


function issues_func( $atts ) {

	#if a search term was entered do nothing.
	if ( !empty($_GET['gh_searchterm']) )
		return;

	$atts = shortcode_atts( array(
		'labels' => NULL,
		'state' => NULL,
		'per_page' => 50, #max is 100
		'show_body' => FALSE
	), $atts, 'gh_issues' );

	$gh = new Github();
	if (!$gh->has_settings)
		return $gh->missing_settings_msg;

	$issues = $gh->get_issues($atts);
	
	$return = format_issues($issues, $atts['show_body']);
	$return = prepend_page_count($return, $gh, count($issues) );
	$return = append_page_links($return, $gh);

	return $return;

}
add_shortcode( 'gh_issues', 'issues_func' );


function searchform_func( $atts ) {
	$atts = shortcode_atts( array(
		'placeholder' => NULL,
		'type' => NULL,
		'in' => NULL,
		'labels'=>NULL,
		'state'=>NULL,
		'show_body'=>FALSE
	), $atts, 'gh_searchform' );
	
	$gh = new Github();
	if (!$gh->has_settings)
		return $gh->missing_settings_msg;

	$results = NULL;
	$msg = 'Enter a search term to begin.';

	$return = build_search_form($atts['placeholder']);

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

	$return .= (!empty($issues)) ? format_issues($issues, $atts['show_body']) : NULL;
	// $return .= append_page_links($return, $gh);

	return $return;

}
add_shortcode( 'gh_searchform', 'searchform_func' );