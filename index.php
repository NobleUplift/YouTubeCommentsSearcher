#!/usr/bin/php -dmemory_limit=1000000000 -dsafe_mode=Off -ddisable_functions=NULL
<?php
	ini_set('display_errors',1);
	ini_set('display_startup_errors',1);
	error_reporting(-1);
	
	if (!function_exists('curl_init')) {
		die('cURL is not installed!\n');
	}
	
	if (!isset($argc))
		die("No arguments given.\nUsage:\nycs.php <videoid> <search>\n");
	
	if ($argc > 1)
		$videoid = $argv[1];
	else
		die("Video ID not specified.\nUsage:\nycs.php <videoid> <search>\n");
	
	if ($argc > 2)
		$search = $argv[2];
	else
		die("Search string not specified.\nUsage:\nycs.php <videoid> <search>\n");
	
	define("MAXPAGES", 200);
	$opts = array(CURLOPT_RETURNTRANSFER => TRUE, CURLOPT_TIMEOUT => 10);
	
	$page = 1;
	while ($page <= MAXPAGES) {
		$url = "http://www.youtube.com/all_comments?v=$videoid&page=$page";
		echo "Searching $url...\n";
		$curl = curl_init($url);
		curl_setopt_array($curl, $opts);
		$output = curl_exec($curl);
		if (preg_match("'<ul id=\"all-comments\">\\s+</ul>'si", $output) === 1) {
			echo "Last page $page found, exiting.\n";
			break;
		}
		
		$offset = 0;
		$result = 0;
		do {
			$result = stripos($output, $search, $offset);
			$offset = $result + 1;
			
			if ($result !== FALSE) {
				echo "$search found on page $page!\n";
				echo substr($output, $result - 25, 50) . "\n";
			}
		} while ($result !== FALSE);
		
		curl_close($curl);
		$page++;
		sleep(1);
	}
?>