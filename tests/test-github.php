<?php

class WPGHPL_GithubTest extends WP_UnitTestCase {

	function setUp() {
		
		parent::setUp();

		$gh = new WPGHPL_Github();
		
		# mandatory config settings to prevent message
		update_option($gh->prefix . 'gh_repo', 'jquery');
		update_option($gh->prefix . 'gh_org', 'jquery');
		update_option($gh->prefix . 'repo_is_public', 1);

	}

	function test_defaults() {

		$gh = new WPGHPL_Github();
		#defaults to page 1
		$this->assertEquals( 1, $gh->page );

		#reacts to get params for pages
		$_GET['gh_page'] = 6;
		$gh = new WPGHPL_Github();
		$this->assertEquals( 6, $gh->page );

		$this->assertTrue( isset($gh->has_settings) );
	}

	function test_check_for_settings() {

		# returns true for repo + org when repo is public
		$gh = new WPGHPL_Github();
		$this->assertTrue( $gh->has_settings );

		# returns false for repo + org when repo is private (needs token)
		update_option($gh->prefix . 'repo_is_public', 0);
		$gh = new WPGHPL_Github();
		$this->assertFalse( $gh->has_settings );

		# returns true for repo + org when repo is private and token is set
		update_option($gh->prefix . 'token', 'abcxyz');
		$gh = new WPGHPL_Github();
		$this->assertTrue( $gh->has_settings );

	}


}

