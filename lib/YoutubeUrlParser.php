<?php
class YoutubeUrlParser {
	public function parseShort($url) {
		$exp = explode('https://youtu.be/', $url);
		return $exp[1];
	}
}