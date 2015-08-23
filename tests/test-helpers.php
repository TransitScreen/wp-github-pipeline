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


}
