<?php
/**
 * @author : Moch Deden
 * @site : http://selesdepselesnul.com 
 */
class YoutubeLinkGenerator {

	public function __construct($mashapeKey) {
		$this->mashapeKey = $mashapeKey;
	}

	private function generateReduceDownloadLinks($links) {
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
	
	public function generate($videoUrl) {
		$config = parse_ini_file('yougrabtube.ini');
		$response = Unirest\Request::get(
		  	$config['downloader_end_point'].$videoUrl,
		    [
		      "X-Mashape-Key" => $this->mashapeKey,
		      "Accept" => "application/json"
		    ]
		);

		$links = $response->body->link;
		return $this->generateReduceDownloadLinks($links);
	}
}