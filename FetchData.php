<?php
/**
 * Denial Detour
 * 
 * LICENSE
 * 
 * By viewing, using, or actively developing this application in any way, you are
 * henceforth bound the license agreement, and all of its changes, set forth by
 * ForwardFour Innovations. The license can be found, in its entirety, at this 
 * address: http://forwardfour.com/license.
 * 
 * Copyright (c) 2012 and Onwards, ForwardFour Innovations
 * http://forwardfour.com/license    [Proprietary/Closed Source]  
 */

/**
 * FetchData is a class which reads a standard YouTube video URL and parses it
 * in order to fetch all revalent data about a given video from the YouTube data
 * API.
*/
	class FetchData {
		private $videoID = 0;
		
		public function __construct($URL) {
		//Use PHP's native capabilites to capture and parse the URL
			$URLFragments = parse_url($URL);
			
		//Check and see if a URL was provided
			if (empty($URL)) {
				echo "<data><status>error</status><message><title>No Link Provided</title><content>You did not provide us with a link! Please go back and enter the link of the video you wish to watch, or search YouTube for a one.</content></message></data>";
			}
			
		//Parse $URLFragments['query'] for the video ID
			if (!empty($URLFragments['query'])) {
			//Break up each of the parameters, which are deliminated by the ampersand
				$parameters = explode("&", $URLFragments['query']);
				
			//Then look for the attribute named "v", which contains the video ID
				foreach($parameters as $param) {
					$paramSegment = explode("=", $param);
					
					if (strtolower($paramSegment['0']) == "v") {
						$this->videoID = $paramSegment['1'];
						break;
					}
				}
		//No URL query was passed
			} else {
				echo "<data><status>error</status><message><title>Invalid Link Provided</title><content>We could not understand the link that you gave us. Although it does link to youtube.com, it does not appear to link to any video. A typical video link from YouTube looks like this: www.youtube.com/watch?v=XXXXXXXXXXX, where the list of Xs can be anysd combination of letters or numbers. Try finding a link that looks similar to this an try again.</content></message></data>";
			}
			
		//Check and see if the URL is in the format youtube.com/watch?v=XXXXXXXXXXX and that the video ID was found
		//The video ID will be 10 if the URL query was invalid or unparsable
			if ($this->videoID != "0") {
			//Now that all of the tests have been passed, fetch the data URL
				echo print_r($this->fetch($this->videoID));
			} else {
				echo "<data><status>error</status><message><title>Invalid Link Provided</title><content>We could not understand the link that you gave us. Although it does link to youtube.com, it does not appear to link to any video. A typical video link from YouTube looks like this: www.youtube.com/watch?v=XXXXXXXXXXX, where the list of Xs can be any combination of letters or numbers. Try finding a link that looks similar to this an try again.</content></message></data>";
			}
		}
		
		private function fetch($ID) {
		//Build the API URL from the given data
			$URL = "http://youtube.com/get_video_info?video_id=" . $ID;
			
		//Fetch the contents of the requested page
			$contents = file_get_contents($URL);
			
		//Explode query so that the entire query can be parsed into an array
			$firstSplit = explode("&", $contents);
			
		//Parse the contents into an array
			$return = array();
			
			foreach ($firstSplit as $value) {
				$secondSplit = explode("=", $value);
				
			//Some parameters will require additional parsing
				switch ($secondSplit['0']) {						
				//The "url_encoded_fmt_stream_map" parameter is weird and it required more parsing, as in includes all of the video URLs
					case "url_encoded_fmt_stream_map" : 						
					//This is a URL encoded query within the "url_encoded_fmt_stream_map", so this requires it own, subparser
						$URLFMTsSplit = explode("&", urldecode($secondSplit['1']));
						$returnURLFMTs = array();
						
					//This query value has parameter names that repeat in a pattern: url, quality, fallback_host, type, itag. Append an interator to avoid conflicts
						$iterator = 1;
						
						foreach ($URLFMTsSplit as $URLFMTs) {	
							$URLFMTsDetails = explode("=", $URLFMTs);
							
							switch ($URLFMTsDetails['0']) {
							//The "type" parameter *may* have an embedded "codecs" paramter that can be pulled out
								case "type" : 
								//There will be a "; ", if a codec name is provided
									$typeSplit = explode("; ", urldecode($URLFMTsDetails['1']));
									$returnCodecs = array();
									
								//Generate the "codecs" parameter
									if (!empty($typeSplit['1'])) {
									//If we are given this for the codec parameter: codecs="vp8.0, vorbis", strip the 'codecs="' and '"'
										$codecs = str_replace('codecs="', "", $typeSplit['1']);
										$codecs = str_replace('"', "", $codecs);
										$codecs = explode(", ", $codecs);
									
										foreach ($codecs as $codec) {
											$returnCodecs[] = urldecode($codec);
										}
									}
									
								//Append "type" parameter
									$returnURLFMTs["type_" . $iterator] = urldecode($typeSplit['0']);
									
								//Append the codecs parameter
									$returnURLFMTs["codecs_" . $iterator] = $returnCodecs;
									
									break;
								
							//The "itag" parameter requires even more parsing as a video URL is listed within this parameter
								case "itag" : 
									$itagSplit = explode(",", urldecode($URLFMTsDetails['1']));
								
								//The above split seperated the "itag" number from an attached "url" parameter. Attach the "itag" parameter here...
									$returnURLFMTs[$URLFMTsDetails['0'] . "_" . $iterator] = urldecode($itagSplit['0']);
									
								//...increment the iterator at the end of every pattern (which is after each "itag" parameter) ...
									$iterator ++;
									
								//... finally append the next "url" parameter. Note that the last "itag" in the list will not have a URL (WHY???)
								//Note the use of the "$URLFMTsDetails", since the explode at the "=" exploded this off the end of the "itag" parameter, and must be retrieved there
									if (!empty($URLFMTsDetails['2'])) {
										$returnURLFMTs["url_" . $iterator] = urldecode($URLFMTsDetails['2']);
									}
									
									break;
									
							//This can be parse regularily
								default : 
									$returnURLFMTs[$URLFMTsDetails['0'] . "_" . $iterator] = urldecode($URLFMTsDetails['1']);
									break;
							}
						}
						
						$return[$secondSplit['0']] = $returnURLFMTs;
						break;
						
				//Break the following parametersy into an array
					case "keywords" : 
					case "fmt_list" : 
					case "ad_channel_code_overlay" : 
					case "fexp" : 
					case "excluded_ads" : 
					case "watermark" : 
						$objectSplit = explode(",", urldecode($secondSplit['1']));
						$returnObject = array();
						
						foreach ($objectSplit as $object) {
							$returnObject[] = urldecode($object);
						}
						
						$return[$secondSplit['0']] = $returnObject;
						break;
						
				//This can be parse regularily
					default : 
						$return[$secondSplit['0']] = urldecode($secondSplit['1']);
						break;
					
				}
			}
			
			return $return;
		}
	}
	
	new FetchData(urldecode($_GET['URL']));
?>