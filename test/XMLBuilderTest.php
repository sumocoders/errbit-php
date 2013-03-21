<?

require_once(__DIR__ . "/../lib/Errbit.php");

class TestCase extends PHPUnit_Framework_TestCase {
	public function testSingleTag() {
		$tag = $this->tag('name','Tim');
		$this->assertSame("<name>Tim</name>", $tag->asXml());

		$tag->attribute("key","val");
		$this->assertSame("<name key=\"val\">Tim</name>", $tag->asXml());
	}

	public function testEscaping() {
		$tag = $this->tag('name','<>');
		$this->assertSame("<name>&lt;&gt;</name>", $tag->asXml());

		$tag = $this->tag('name','Tim');
		$tag->attribute("key",'"&');
		$this->assertSame("<name key=\"&quot;&amp;\">Tim</name>", $tag->asXml());
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
	}

	protected function tag() {
		$root = new Errbit_XmlBuilder();
		return call_user_func_array(
			array($root, 'tag'), func_get_args());
	}
}
