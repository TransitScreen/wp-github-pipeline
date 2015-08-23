<?php

class HelpersTest extends WP_UnitTestCase {


	function test_convert_markdown() {

		# converts markdown
		$this->assertStringMatchesFormat( '<p><code>foo</code></p>', convert_markdown('```foo```') );
		$this->assertStringMatchesFormat( '<h1>%s</h1>', convert_markdown('# Some title') );

	}

	function test_needs_white_text() {

		$this->assertTrue( needs_white_text('#000000') ); # black, duh
		$this->assertTrue( needs_white_text('#007700') ); # dark green
		$this->assertFalse( needs_white_text('#FFFFFF') ); # white
		$this->assertFalse( needs_white_text('#fbca04') ); # orangy yellow

	}

	function test_build_labels() {

		# This should be fixtures
		$labels = array(
			array(
				"url" => "https://api.github.com/repos/jquery/jquery/labels/CLA:%20Valid",
				"name" => "CLA: Valid",
				"color"=> "007700"
			),
			array(
			 	"url" => "https://api.github.com/repos/jquery/jquery/labels/Blocker",
				"name" => "Blocker",
			  	"color" => "f7c6c7"
			)
		);

		#dark label
		$label1 = build_label($labels[0]);
		$this->assertContains( 'background-color:#007700', $label1);
		$this->assertContains( 'color:#FFF', $label1);

		#light label
		$label2 = build_label($labels[1]);
		$this->assertContains( 'background-color:#f7c6c7', $label2);
		$this->assertNotContains( 'color:#FFF', $label2);

	}

	function test_format_issue() {

		$issue1 = array (
			  'url' => 'https://api.github.com/repos/jquery/jquery/issues/2540',
			  'labels_url' => 'https://api.github.com/repos/jquery/jquery/issues/2540/labels{/name}',
			  'comments_url' => 'https://api.github.com/repos/jquery/jquery/issues/2540/comments',
			  'events_url' => 'https://api.github.com/repos/jquery/jquery/issues/2540/events',
			  'html_url' => 'https://github.com/jquery/jquery/issues/2540',
			  'id' => 101236726,
			  'number' => 2540,
			  'title' => 'Use new qunit interface',
			  'user' => 
			  array (
			    'login' => 'markelog',
			    'id' => 945528,
			    'avatar_url' => 'https://avatars.githubusercontent.com/u/945528?v=3',
			    'gravatar_id' => '',
			    'url' => 'https://api.github.com/users/markelog',
			    'html_url' => 'https://github.com/markelog',
			    'followers_url' => 'https://api.github.com/users/markelog/followers',
			    'following_url' => 'https://api.github.com/users/markelog/following{/other_user}',
			    'gists_url' => 'https://api.github.com/users/markelog/gists{/gist_id}',
			    'starred_url' => 'https://api.github.com/users/markelog/starred{/owner}{/repo}',
			    'subscriptions_url' => 'https://api.github.com/users/markelog/subscriptions',
			    'organizations_url' => 'https://api.github.com/users/markelog/orgs',
			    'repos_url' => 'https://api.github.com/users/markelog/repos',
			    'events_url' => 'https://api.github.com/users/markelog/events{/privacy}',
			    'received_events_url' => 'https://api.github.com/users/markelog/received_events',
			    'type' => 'User',
			    'site_admin' => false,
			  ),
			  'labels' => 
			  array (
			    0 => 
			    array (
			      'url' => 'https://api.github.com/repos/jquery/jquery/labels/Build',
			      'name' => 'Build',
			      'color' => '0052cc',
			    ),
			  ),
			  'state' => 'open',
			  'locked' => false,
			  'assignee' => NULL,
			  'milestone' => NULL,
			  'comments' => 0,
			  'created_at' => '2015-08-16T06:01:56Z',
			  'updated_at' => '2015-08-16T06:01:56Z',
			  'closed_at' => NULL,
			  'body' => '',
		);

		$issue1build = format_issue($issue1, 'toggle');
		# no toggle if there's no body
		$this->assertNotContains('toggle', $issue1build);

		$issue2 = $issue1;
		$issue2['body'] = 'xyz';
		$issue2build = format_issue($issue2, 'toggle');
		$this->assertContains('toggle', $issue2build);

	}

}
