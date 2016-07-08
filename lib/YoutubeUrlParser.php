<?php
class YoutubeUrlParser {
	
	const SHORT_URL = 'https://youtu.be/';
	const LONG_URL = '/https:\/\/www\.youtube\.com\/watch\?v=.+/i';

	public function parseShort($url) {
		$exp = explode(self::SHORT_URL, $url);

		if(count($exp) > 1)
			return $exp[1];
		else
			return '';
	}

	public function parseLong($url) {
		if(preg_match(self::LONG_URL, $url)) {
			$query = parse_url($url)['query'];
			parse_str($query, $arrQuery);
			$videoId = $arrQuery['v'];
			return $videoId;	
		} else {
			return '';
		}
	}
}