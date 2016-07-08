<?php
require __DIR__ . '/vendor/autoload.php';
use PHPUnit\Framework\TestCase;

class YoutubeUrlParserTest extends TestCase {

	public function setUp() {
		$this->youtubeUrlParser = new YoutubeUrlParser;
	}

	public function testParseShortUrl() {
		
		$this->assertEquals('4OmL8fl_nOo', 
			$this->youtubeUrlParser->parseShort('https://youtu.be/4OmL8fl_nOo'));
	}

	public function testParseLongUrl() {
		
		$this->assertEquals('4OmL8fl_nOo', 
			$this->youtubeUrlParser->parseLong('https://www.youtube.com/watch?v=4OmL8fl_nOo'));
	}
}