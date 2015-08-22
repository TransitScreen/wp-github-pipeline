<?php

class HelpersTest extends WP_UnitTestCase {


	function test_convert_markdown() {

		# converts markdown
		$this->assertStringMatchesFormat( '<p><code>foo</code></p>', convert_markdown('```foo```') );
		$this->assertStringMatchesFormat( '<h1>%s</h1>', convert_markdown('# Some title') );

	}


}
