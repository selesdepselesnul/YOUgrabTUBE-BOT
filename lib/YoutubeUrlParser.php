<?php
class YoutubeUrlParser {
	
	const SHORT_URL = 'https://youtu.be/';

	public function parseShort($url) {
		$exp = explode(self::SHORT_URL, $url);
		return $exp[1];
	}

	public function parseLong($url) {
		$query = parse_url($url)['query'];
		parse_str($query, $arrQuery);
		$videoId = $arrQuery['v'];
		return $videoId;
	}
}