<?

require_once(__DIR__ . "/../lib/Errbit.php");

class TestCase extends PHPUnit_Framework_TestCase {
	public function testSingleTag() {
		$tag = $this->tag('name','Tim');
		$this->assertSame("<name>Tim</name>", $tag->asXml());

		$tag->attribute("key","val");
		$this->assertSame("<name key=\"val\">Tim</name>", $tag->asXml());
	}

	protected function tag($name, $value = null) {
		$root = new Errbit_XmlBuilder();
		return $root->tag($name, $value);
	}
}
