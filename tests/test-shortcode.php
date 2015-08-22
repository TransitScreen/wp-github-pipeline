<?php

class ShortcodeTest extends WP_UnitTestCase {

	function before_each() {

		# mandatory config settings to prevent message
		update_option('wpghdash_gh_repo', 'jquery');
		update_option('wpghdash_gh_org', 'jquery');
		update_option('wpghdash_repo_is_public', 1);
	}

	//TODO: This isn't a real test yet...
	function test_get_milestones() {

		# create a post
		$post_data = array('post_title'=>'Milestones',
							'post_content'=>'[gh_milestones]');
		$p = $this->factory->post->create_and_get($post_data);
		$content =  apply_filters( 'the_content', $p->post_content );
		$this->assertNotEquals( '[gh_milestones]', $content );

	}


}

