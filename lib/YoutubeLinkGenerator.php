<?php
/**
 * @author : Moch Deden
 * @site : http://selesdepselesnul.com 
 */
class YoutubeLinkGenerator {

	public static function generateVideoId($videoUrl) {
		$query = parse_url($videoUrl)['query'];
		parse_str($query, $arrQuery);
		$videoId = $arrQuery['v'];
		return $videoId;
	}

	private static function generateDownloadLinks($links) {
		$downloadLinks = array_map(function($x) {
			$type = $x->type;
			return [
				"format" => $type->format,
				"quality" => $type->quality,
				"url" => $x->url
			]; 
		}, $links);
		$properDownloadLinks = array_slice($downloadLinks, 0, count($downloadLinks)-1);
		return $properDownloadLinks;
	}
	
	public static function generate($mashapeKey, $videoUrl) {
		$config = parse_ini_file('yougrabtube.ini');
		$response = Unirest\Request::get(
		  	$config['downloader_end_point'].YoutubeLinkGenerator::generateVideoId($videoUrl),
		    [
		      "X-Mashape-Key" => $mashapeKey,
		      "Accept" => "application/json"
		    ]
		);

		$links = $response->body->link;
		return YoutubeLinkGenerator::generateDownloadLinks($links);
	}
}