<?

require_once(__DIR__ . "/../lib/Errbit.php");

class TestCase extends PHPUnit_Framework_TestCase {
	public function testSingleTag() {
		$tag = $this->tag('name','Tim');
		$this->assertSame("<name>Tim</name>", $tag->asXml());

		$tag->attribute("key","val");
		$this->assertSame("<name key=\"val\">Tim</name>", $tag->asXml());

		$tag = $this->tag('name','head');
		$this->assertSame("<name>head</name>", $tag->asXml());

		$tag = $this->tag('name','array_shift');
		$this->assertSame("<name>array_shift</name>", $tag->asXml());
	}

	public function testEscaping() {
		$tag = $this->tag('name','<>');
		$this->assertSame("<name>&lt;&gt;</name>", $tag->asXml());

		$tag = $this->tag('name','Tim');
		$tag->attribute("key",'"&');
		$this->assertSame("<name key=\"&quot;&amp;\">Tim</name>", $tag->asXml());
	}

	/**
	 * The underlying DOMDocument was for some reason trying to parse the tag
	 * content as XML and if it saw an unterminated entity reference
	 * (like '&am') would throw an exception. 
	 *
	 * This test ensures the "value" part can be anything
	 */
	public function testPartialEscaping() {
		$tag = $this->tag('name','&am<&lt');
		$this->assertSame("<name>&amp;am&lt;&amp;lt</name>", $tag->asXml());

		$tag = $this->tag('name','<a></a>');
		$this->assertSame("<name>&lt;a&gt;&lt;/a&gt;</name>", $tag->asXml());

		$tag = $this->tag('name','Tim');
		$tag->attribute("key",'"&&amp;');
		$this->assertSame("<name key=\"&quot;&amp;&amp;amp;\">Tim</name>", $tag->asXml());
	}

	public function testNestedTags() {
		$tag = $this->tag('person', array('name' => 'Tim'), function($builder) {
			$builder->tag("var", 123, array('key' => 'age'));
		});
		$expected =
			'<person name="Tim">'.
				'<var key="age">123</var>'.
			'</person>';
		$this->assertSame($expected, $tag->asXml());

		$tag = $this->tag('b', array('k' => 'i'), function($builder) {
			$builder->tag('b', 'Tim', array('k' => 'v'));
			$builder->tag('b', array('k' => 'x'), function($builder) {
				$builder->tag('b', 10);
			});
		});
		$expected =
			'<b k="i">'.
				'<b k="v">Tim</b>'.
				'<b k="x">'.
					'<b>10</b>'.
				'</b>'.
			'</b>';
		$this->assertSame($expected, $tag->asXml());
	}

	public function testXmlVarsFor() {
		$input = array (
		  'name' => 'Tim',
		  'info' =>
		  array (
			 'age' => 10
		  )
		);
		$tag = $this->tag('notice');
		Errbit_Notice::xmlVarsFor($tag, $input);
		$expected =
			'<notice>'.
				'<var key="name">Tim</var>'.
				'<var key="info">'.
					'<var key="age">10</var>'.
				'</var>'.
			'</notice>';
		$this->assertSame($expected, $tag->asXml());
	}

	protected function tag() {
		$root = new Errbit_XmlBuilder();
		return call_user_func_array(
			array($root, 'tag'), func_get_args());
	}
}
