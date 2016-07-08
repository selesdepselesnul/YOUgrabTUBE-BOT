<?php
class YoutubeUrlParser {
	
	const SHORT_URL = '/https:\/\/youtu\.be\/.+/i';
	const LONG_URL = '/https:\/\/www\.youtube\.com\/watch\?v=.+/i';

	public function parseShort($url) {
		if(preg_match(self::SHORT_URL, $url)) {
			$path = parse_url($url)['path'];
			$videoId = explode('/', $path);
			return $videoId[1];		
		}
		return '';
	}

	public function parseLong($url) {
		if(preg_match(self::LONG_URL, $url)) {
			$query = parse_url($url)['query'];
			parse_str($query, $arrQuery);
			$videoId = $arrQuery['v'];
			return $videoId;	
		} 
		return '';
	}

}