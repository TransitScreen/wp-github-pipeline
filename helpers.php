<?php

/* HELPERS FOR PRESENTING MARKUP */

function format_issues( $issues, $body=false ) {

	if (empty($issues))
		return;

	#load necessary scripts if we're toggling
	if (strtolower($body)==='toggle'){
		wp_enqueue_script( 'toggle' );
	}

	$return = '<div class="issues-list">';

	foreach ($issues as $issue) {

		$return .= '<h3 class="issue__title">'. htmlentities( $issue['title'] ).' #'. $issue['number'].'</h3>';

		$return .= build_labels( $issue['labels'] );

		$return .= '<ul class="issue__details">';
		$return .= '<li>State: '.$issue['state'].'</li>';
		$return .= (!empty($issue['closed_at'])) ? '<li>Closed: '.wpghpl_formatdate($issue['closed_at']).'</li>' : NULL;
		$return .= (!empty($issue['assignee'])) ? '<li>Assigned to: '.$issue['assignee']['login'].'</li>' : NULL;
		$return .= (!empty($issue['milestone'])) ? '<li><span class="issue__details__milestone '.( ($issue['milestone']['closed_at']) ? 'issue__details__milestone--closed' : NULL ).'">Milestone: '.$issue['milestone']['title'].( ($issue['milestone']['description'])? ": ".$issue['milestone']['description']: NULL ).'</span></li>' : NULL;
		$return .= '</ul>';
		
		if ($body===strtolower('toggle')) {
			$return .= '<div class="issue__toggle-wrap">';
			$return .= '<div class="issue__body">'.convert_markdown($issue['body'], true).'</div>';
			$return .= '<a href="#" form-toggle-btn>&darr; More</a>';
			$return .= '</div>';
		} else if ($body){
			$return .= '<div class="issue__body">'.convert_markdown($issue['body'], true).'</div>';
		}
	}

	$return .= '</div>';

	return $return;
}

use League\CommonMark\CommonMarkConverter;
function convert_markdown($string, $linebreaks=null){
	$newstring = $string;
	if ($linebreaks)
		$newstring = nl2br($newstring);
	$converter = new CommonMarkConverter();
	$newstring =  $converter->convertToHtml( $newstring );
	return $newstring;
}

function build_labels( $labels ) {
	$return = '';
	foreach ($labels as $label) {
		$return .= build_label($label);
	}
	return $return;
}

function build_label( $label ) {

	$return = '';
	$return .= '<span class="issue__label" ';
	$return .= 'style="background-color:#'. $label['color'] . ';';
	$return .= ( needs_white_text($label['color']) ) ? ' color:#FFF;' : NULL;
	$return .= '" '; #closing the style attribute
	$return .= '>'; 
	$return .= $label['name'];
	$return .= '</span>';

	return $return;

}

/**
 * Checks if a color is dark enough to warrant white text over it
 * @param $colorhex (string) A hex color value
 * @return (bool) TRUE if white text should be used
 */
function needs_white_text( $hexcolor ) {
	
	$r = hexdec(substr($hexcolor,0,2)); 
	$g = hexdec(substr($hexcolor,2,2)); 
	$b = hexdec(substr($hexcolor,4,2)); 
	$yiq = (($r*299)+($g*587)+($b*114))/1000; 
	return ($yiq >= 128) ? FALSE : TRUE;
}

function build_search_form($placeholder=NULL) {
	$value = (!empty($_GET['gh_searchterm'])) ? $_GET['gh_searchterm'] : NULL;
	$return = '';
	$return .= '<form class="issue-searchform" method="GET" action="'. get_the_permalink() .'">';
	$return .= '<input type="text" name="gh_searchterm" value="'. $value .'" placeholder="'.$placeholder.'" /> ';
	$return .= '<input type="submit" value="Search" /></form>';
	return $return;
}

function format_milestones( $milestones ) {

	if (empty($milestones))
		return;

	$return = '<div class="milestones-list">
	';

	foreach ($milestones as $milestone) {

		$return .= '<h3 class="milestone__title">'.$milestone['title'].'</h3>
		';

		$return .= '<ul class="milestone__details">
		';
		// $return .= '<li>State: '.$milestone['state'].'</li>';
		$return .= ($milestone['due_on']) ? '<li>Due: '.wpghpl_formatdate($milestone['due_on']).'</li>' : NULL;
		$return .= '<li>Open issues: '.$milestone['open_issues'].' Closed issues: '.$milestone['closed_issues'].'</li>';

		$return .= '<li>Issues:<br/>'. print_milestone_issues($milestone['issues']) .'</li>';

		$return .= '
		</ul>';
	}

	$return .= '
	</div>';

	return $return;
}

function print_milestone_issues($issues) {

	if (!$issues)
		return;

	$return = '<ul class="milestone__issues-list">
	';
	foreach ($issues as $issue) {
		$class = ( $issue['state']=='open' ) ? 'open' : 'closed';
		$return .='<li class="milestone__issue-item '.$class.'">'. htmlentities($issue['title']) . ' ';
		$return .= build_labels($issue['labels']);
		$return .='</li>
		';
	}
	$return .= '</ul>
	';
	return $return;

}

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

function wpghpl_formatdate($data_str, $format=NULL) {
	$format = ($format) ? $format : 'F j, Y';
	return date_i18n( $format, strtotime($data_str) );
}

function dump($var) {
	echo "<pre>";
	var_dump($var);
	echo "</pre>";
}