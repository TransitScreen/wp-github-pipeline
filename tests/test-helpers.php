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


}
