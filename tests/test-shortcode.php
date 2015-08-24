<?php

class WPGHPL_ShortcodeTest extends WP_UnitTestCase {

	function before_each() {

		# mandatory config settings to prevent message
		update_option('wpghdash_gh_repo', 'jquery');
		update_option('wpghdash_gh_org', 'jquery');
		update_option('wpghdash_repo_is_public', 1);
	}

	//TODO: This isn't a real test yet...
	function test_placeholder() {

		$this->assertTrue(true);
		
	}


}

