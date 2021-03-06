<?php

class StubGithub extends WPGHPL_Github{

	public function __construct() {

		parent::__construct();

		$this->has_settings = TRUE;

		$this->issues = 
		array ( 
			0 => array ( 'url' => 'https://api.github.com/repos/jquery/jquery/issues/2591', 'labels_url' => 'https://api.github.com/repos/jquery/jquery/issues/2591/labels{/name}', 'comments_url' => 'https://api.github.com/repos/jquery/jquery/issues/2591/comments', 'events_url' => 'https://api.github.com/repos/jquery/jquery/issues/2591/events', 'html_url' => 'https://github.com/jquery/jquery/issues/2591', 'id' => 106142983, 'number' => 2591, 'title' => 'stop() resets slideToggle()', 'user' => array ( 'login' => 'kapetance', 'id' => 13976708, 'avatar_url' => 'https://avatars.githubusercontent.com/u/13976708?v=3', 'gravatar_id' => '', 'url' => 'https://api.github.com/users/kapetance', 'html_url' => 'https://github.com/kapetance', 'followers_url' => 'https://api.github.com/users/kapetance/followers', 'following_url' => 'https://api.github.com/users/kapetance/following{/other_user}', 'gists_url' => 'https://api.github.com/users/kapetance/gists{/gist_id}', 'starred_url' => 'https://api.github.com/users/kapetance/starred{/owner}{/repo}', 'subscriptions_url' => 'https://api.github.com/users/kapetance/subscriptions', 'organizations_url' => 'https://api.github.com/users/kapetance/orgs', 'repos_url' => 'https://api.github.com/users/kapetance/repos', 'events_url' => 'https://api.github.com/users/kapetance/events{/privacy}', 'received_events_url' => 'https://api.github.com/users/kapetance/received_events', 'type' => 'User', 'site_admin' => false, ), 'labels' => array ( 0 => array ( 'url' => 'https://api.github.com/repos/jquery/jquery/labels/Effects', 'name' => 'Effects', 'color' => '0052cc', ), ), 'state' => 'open', 'locked' => false, 'assignee' => NULL, 'milestone' => NULL, 'comments' => 4, 'created_at' => '2015-09-12T09:22:35Z', 'updated_at' => '2015-09-13T18:36:38Z', 'closed_at' => NULL, 'body' => 'If I do something like this, slideToggle animation, when stop is clicked, will reset to start position. If you inspect #content element, you can see overflow:hidden rule in it inline style. But if you call stop animation, that rule is removed. ``` Toggle Stop
		Some content
		```', ), 
			1 => array ( 'url' => 'https://api.github.com/repos/jquery/jquery/issues/2590', 'labels_url' => 'https://api.github.com/repos/jquery/jquery/issues/2590/labels{/name}', 'comments_url' => 'https://api.github.com/repos/jquery/jquery/issues/2590/comments', 'events_url' => 'https://api.github.com/repos/jquery/jquery/issues/2590/events', 'html_url' => 'https://github.com/jquery/jquery/issues/2590', 'id' => 106124438, 'number' => 2590, 'title' => 'Offset: Fractions test fails in Chrome 45', 'user' => array ( 'login' => 'Krinkle', 'id' => 156867, 'avatar_url' => 'https://avatars.githubusercontent.com/u/156867?v=3', 'gravatar_id' => '', 'url' => 'https://api.github.com/users/Krinkle', 'html_url' => 'https://github.com/Krinkle', 'followers_url' => 'https://api.github.com/users/Krinkle/followers', 'following_url' => 'https://api.github.com/users/Krinkle/following{/other_user}', 'gists_url' => 'https://api.github.com/users/Krinkle/gists{/gist_id}', 'starred_url' => 'https://api.github.com/users/Krinkle/starred{/owner}{/repo}', 'subscriptions_url' => 'https://api.github.com/users/Krinkle/subscriptions', 'organizations_url' => 'https://api.github.com/users/Krinkle/orgs', 'repos_url' => 'https://api.github.com/users/Krinkle/repos', 'events_url' => 'https://api.github.com/users/Krinkle/events{/privacy}', 'received_events_url' => 'https://api.github.com/users/Krinkle/received_events', 'type' => 'User', 'site_admin' => false, ), 'labels' => array ( 0 => array ( 'url' => 'https://api.github.com/repos/jquery/jquery/labels/Offset', 'name' => 'Offset', 'color' => '0052cc', ), ), 'state' => 'open', 'locked' => false, 'assignee' => array ( 'login' => 'mzgol', 'id' => 1758366, 'avatar_url' => 'https://avatars.githubusercontent.com/u/1758366?v=3', 'gravatar_id' => '', 'url' => 'https://api.github.com/users/mzgol', 'html_url' => 'https://github.com/mzgol', 'followers_url' => 'https://api.github.com/users/mzgol/followers', 'following_url' => 'https://api.github.com/users/mzgol/following{/other_user}', 'gists_url' => 'https://api.github.com/users/mzgol/gists{/gist_id}', 'starred_url' => 'https://api.github.com/users/mzgol/starred{/owner}{/repo}', 'subscriptions_url' => 'https://api.github.com/users/mzgol/subscriptions', 'organizations_url' => 'https://api.github.com/users/mzgol/orgs', 'repos_url' => 'https://api.github.com/users/mzgol/repos', 'events_url' => 'https://api.github.com/users/mzgol/events{/privacy}', 'received_events_url' => 'https://api.github.com/users/mzgol/received_events', 'type' => 'User', 'site_admin' => false, ), 'milestone' => array ( 'url' => 'https://api.github.com/repos/jquery/jquery/milestones/2', 'html_url' => 'https://github.com/jquery/jquery/milestones/3.0.0', 'labels_url' => 'https://api.github.com/repos/jquery/jquery/milestones/2/labels', 'id' => 561714, 'number' => 2, 'title' => '3.0.0', 'description' => '', 'creator' => array ( 'login' => 'dmethvin', 'id' => 157858, 'avatar_url' => 'https://avatars.githubusercontent.com/u/157858?v=3', 'gravatar_id' => '', 'url' => 'https://api.github.com/users/dmethvin', 'html_url' => 'https://github.com/dmethvin', 'followers_url' => 'https://api.github.com/users/dmethvin/followers', 'following_url' => 'https://api.github.com/users/dmethvin/following{/other_user}', 'gists_url' => 'https://api.github.com/users/dmethvin/gists{/gist_id}', 'starred_url' => 'https://api.github.com/users/dmethvin/starred{/owner}{/repo}', 'subscriptions_url' => 'https://api.github.com/users/dmethvin/subscriptions', 'organizations_url' => 'https://api.github.com/users/dmethvin/orgs', 'repos_url' => 'https://api.github.com/users/dmethvin/repos', 'events_url' => 'https://api.github.com/users/dmethvin/events{/privacy}', 'received_events_url' => 'https://api.github.com/users/dmethvin/received_events', 'type' => 'User', 'site_admin' => false, ), 'open_issues' => 44, 'closed_issues' => 169, 'state' => 'open', 'created_at' => '2014-02-07T02:09:56Z', 'updated_at' => '2015-09-12T19:23:41Z', 'due_on' => NULL, 'closed_at' => NULL, ), 'comments' => 1, 'created_at' => '2015-09-12T02:59:21Z', 'updated_at' => '2015-09-12T19:36:20Z', 'closed_at' => NULL, 'body' => 'Two days ago, TestSwarm updated the jquery-core browser set from Chrome 43/44 to Chrome 44/45. This revealed a test failure. : > offset: fractions (see #7730 and #7885) (1, 1, 2) > Check top > Expected: 1000 > Result: 999.984375 Looks like Chrome 45 is more precise than our tests expect.', ), 
			2 => array ( 'url' => 'https://api.github.com/repos/jquery/jquery/issues/2588', 'labels_url' => 'https://api.github.com/repos/jquery/jquery/issues/2588/labels{/name}', 'comments_url' => 'https://api.github.com/repos/jquery/jquery/issues/2588/comments', 'events_url' => 'https://api.github.com/repos/jquery/jquery/issues/2588/events', 'html_url' => 'https://github.com/jquery/jquery/pull/2588', 'id' => 105782269, 'number' => 2588, 'title' => 'Ajax: Mitigate possible XSS vulnerability', 'user' => array ( 'login' => 'markelog', 'id' => 945528, 'avatar_url' => 'https://avatars.githubusercontent.com/u/945528?v=3', 'gravatar_id' => '', 'url' => 'https://api.github.com/users/markelog', 'html_url' => 'https://github.com/markelog', 'followers_url' => 'https://api.github.com/users/markelog/followers', 'following_url' => 'https://api.github.com/users/markelog/following{/other_user}', 'gists_url' => 'https://api.github.com/users/markelog/gists{/gist_id}', 'starred_url' => 'https://api.github.com/users/markelog/starred{/owner}{/repo}', 'subscriptions_url' => 'https://api.github.com/users/markelog/subscriptions', 'organizations_url' => 'https://api.github.com/users/markelog/orgs', 'repos_url' => 'https://api.github.com/users/markelog/repos', 'events_url' => 'https://api.github.com/users/markelog/events{/privacy}', 'received_events_url' => 'https://api.github.com/users/markelog/received_events', 'type' => 'User', 'site_admin' => false, ), 'labels' => array ( 0 => array ( 'url' => 'https://api.github.com/repos/jquery/jquery/labels/CLA:%20Valid', 'name' => 'CLA: Valid', 'color' => '007700', ), ), 'state' => 'open', 'locked' => false, 'assignee' => NULL, 'milestone' => NULL, 'comments' => 0, 'created_at' => '2015-09-10T10:48:05Z', 'updated_at' => '2015-09-10T10:48:08Z', 'closed_at' => NULL, 'pull_request' => array ( 'url' => 'https://api.github.com/repos/jquery/jquery/pulls/2588', 'html_url' => 'https://github.com/jquery/jquery/pull/2588', 'diff_url' => 'https://github.com/jquery/jquery/pull/2588.diff', 'patch_url' => 'https://github.com/jquery/jquery/pull/2588.patch', ), 'body' => 'Fixes gh-2432', ), 
		);

	}

	public function get_issues($options=array()) { return $this->issues; }

}

class WPGHPL_ShortcodeTest extends WP_UnitTestCase {

	function before_each() {

		# mandatory config settings to prevent message
		update_option('wpghdash_gh_repo', 'jquery');
		update_option('wpghdash_gh_org', 'jquery');
		update_option('wpghdash_repo_is_public', 1);
	}

	function test_wpghpl_issues_func() {

		$atts = array("per_page"=>3);

		$output = wpghpl_issues_func( $atts, new StubGithub() );

		$class_count = preg_match_all('/class="issue"/U', $output); #this regex will need to be adjusted if other classes are added

		#issues class gets assigned to each 
        $this->assertEquals( $atts['per_page'], $class_count );

	}

}