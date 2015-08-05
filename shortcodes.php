<?php

function milestones_func( $atts ) {
	$atts = shortcode_atts( array(
		'state' => 'all'
	), $atts, 'gh_milestones' );
	
	$gh = new Github();
	if (!$gh->has_settings)
		return $gh->missing_settings_msg;

	$milestones = $gh->get_milestones($atts);

	return format_milestones( $milestones ); 
}
add_shortcode( 'gh_milestones', 'milestones_func' );

function format_milestones( $milestones ) {

	if (empty($milestones))
		return;

	$return = '<div class="milestones-list">';

	foreach ($milestones as $milestone) {

		$return .= '<h3 class="milestone__title">'.$milestone['title'].'</h3>';

		$return .= '<ul class="milestone__details">';
		// $return .= '<li>State: '.$milestone['state'].'</li>';
		$return .= ($milestone['due_on']) ? '<li>Due: '.wpghdash_formatdate($milestone['due_on']).'</li>' : NULL;
		$return .= '<li>Open issues: '.$milestone['open_issues'].' Closed issues: '.$milestone['closed_issues'].'</li>';

		$return .= '<li>Issues:<br/>'. print_milestone_issues($milestone['issues']) .'</li>';

		$return .= '</ul>';
	}

	$return .= '</div>';

	return $return;
}


function print_milestone_issues($issues) {

	if (!$issues)
		return;

	$return = '<ul class="milestone__issues-list">';
	foreach ($issues as $issue) {
		$class = ( $issue['state']=='open' ) ? 'open' : 'closed';
		$return .='<li class="milestone__issue-item '.$class.'">'. $issue['title'] . ' ';
		$return .= build_labels($issue['labels']);
		$return .='</li>';
	}
	$return .= '</ul>';
	return $return;

}


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
	
	#load necessary scripts if we're toggling
	if ($atts['show_body']=='toggle'){
		wp_enqueue_script( 'toggle' );
	}

	$return = format_issues($issues, $atts['show_body']);
	$return = prepend_page_count($return, $gh, count($issues) );
	$return = append_page_links($return, $gh);

	return $return;

}
add_shortcode( 'gh_issues', 'issues_func' );

function prepend_page_count( $string, $Github, $on_page_count) {

	$start = ($Github->per_page)*($Github->page-1)+1;
	$end = $start+$on_page_count-1;
	$count = "<div class='gh-result-count'>Showing: {$start} - {$end} out of X</div>";

	$newstring = $count . $string;

	return $newstring;
}

/* 
 * Append previous or next page links to returned string (if appropriate)
 * @param $string (str) the input string
 * @param $Client (Github) an instance of the Github API class to check for pagination
 * @return string
 */
function append_page_links($string, $Client) {

	$query = (!empty($_SERVER['QUERY_STRING'])) ? $_SERVER['QUERY_STRING'] : NULL;

	#remove previous page query param
	if (!empty($_GET['gh_page']))
		$query = str_replace('gh_page='.$_GET['gh_page'], '', $query);

	$page_url = get_the_permalink();

	if ($query)
		$page_url .= '?'.$query;

	$punc = ($query) ? '&' : '?';

	$prev_url = $page_url . $punc . 'gh_page='.($Client->page-1);
	$next_url = $page_url . $punc . 'gh_page='.($Client->page+1);

	#add either next or prev link
	$pagination = '';
	$pagination .= ($Client->has_prev_page ) ? '<a class="gh-pagination__link gh-pagination__link--prev" href="'.$prev_url.'">&larr; Previous</a>' : NULL;
	$pagination .= ($Client->has_next_page ) ? '<a class="gh-pagination__link gh-pagination__link--next" href="'.$next_url.'">Next &rarr;</a>' : NULL;
	# wrap either/both with div
	$newstring = ( $pagination ) ? $string . '<div class="gh-pagination">' . $pagination . '</div>' : $string;

	return $newstring;
}

function format_issues( $issues, $body=false ) {

	if (empty($issues))
		return;

	$return = '<div class="issues-list">';

	foreach ($issues as $issue) {

		$return .= '<h3 class="issue__title">'.$issue['title'].' #'. $issue['number'].'</h3>';

		$return .= build_labels( $issue['labels'] );

		$return .= '<ul class="issue__details">';
		$return .= '<li>State: '.$issue['state'].'</li>';
		$return .= (!empty($issue['closed_at'])) ? '<li>Closed: '.wpghdash_formatdate($issue['closed_at']).'</li>' : NULL;
		$return .= (!empty($issue['assignee'])) ? '<li>Assigned to: '.$issue['assignee']['login'].'</li>' : NULL;
		$return .= (!empty($issue['milestone'])) ? '<li><span class="issue__details__milestone '.( ($issue['milestone']['closed_at']) ? 'issue__details__milestone--closed' : NULL ).'">Milestone: '.$issue['milestone']['title'].( ($issue['milestone']['description'])? ": ".$issue['milestone']['description']: NULL ).'</span></li>' : NULL;
		$return .= '</ul>';
		
		if ($body===strtolower('toggle')) {
			$return .= '<a href="#" form-toggle-btn>Show text</a>';
			$return .= '<div class="issue__body">'.convert_text_to_markup($issue['body']).'</div>';
		} else if ($body){
			$return .= '<div class="issue__body">'.convert_text_to_markup($issue['body']).'</div>';
		}
	}

	$return .= '</div>';

	return $return;
}

function convert_text_to_markup($string){
	return nl2br($string);
}

function build_labels( $labels ) {
	$return = '';
	foreach ($labels as $label) {
		$return .= '<span class="issue__label" ';
		$return .= 'style="background-color:#'. $label['color'].'" ';
		$return .= '>'; 
		$return .= $label['name'];
		$return .= '</span>';
	}
	return $return;
}

function print_search_form() {
	$value = (!empty($_GET['gh_searchterm'])) ? $_GET['gh_searchterm'] : NULL;
	?>
	<form class="issue-searchform" method="GET" action="<?php the_permalink() ?>">
		<input type="text" name="gh_searchterm" value="<?php echo $value; ?>" />
		<input type="submit" value="Search" />
	</form>
	<?php	
}

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

	print_search_form();

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

	echo '<div class="gh_searchform__msg">' . $msg . '</div>';

	$return = (!empty($issues)) ? format_issues($issues, $atts['show_body']) : NULL;
	$return = append_page_links($return, $gh);

	return $return;

}
add_shortcode( 'gh_searchform', 'searchform_func' );
