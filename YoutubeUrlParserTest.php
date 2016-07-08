<?php
require __DIR__ . '/vendor/autoload.php';
use PHPUnit\Framework\TestCase;

class YoutubeUrlParserTest extends TestCase {
	public function testParseShortUrl() {
		$yUrlParser = new YoutubeUrlParser;
		$this->assertEquals('4OmL8fl_nOo', 
			$yUrlParser->parseShort('https://youtu.be/4OmL8fl_nOo'));
	}
}