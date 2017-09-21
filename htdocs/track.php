<?php
header('Content-type: audio/mpeg');

/*
TODO: We could assign and check $_SESSION['bestof'] if we wanted to
*/

// Discourage abuse...
if($_SERVER['HTTP_REFERER'] && strpos($_SERVER['HTTP_REFERER'], 'https://randomfoo.net/') !== 0) $exit++; 
if(strpos($_SERVER['HTTP_USER_AGENT'], 'Mozilla/') === FALSE) $exit++;
if(!$_SERVER['HTTP_REFERER'] && strpos($_SERVER['HTTP_USER_AGENT'], 'Gecko/')) $exit--;

// DEBUG
if($_SERVER['HTTP_USER_AGENT'] == 'bestof-debug') {
  $debug = 1;
}

if($exit && !$debug) {
  /* DEBUG */
  $f = fopen('users', 'a');
  fwrite($f, date('r') . "\n");
  fwrite($f, 'Browser: ' . $_SERVER['HTTP_USER_AGENT'] . "\n");
  fwrite($f, 'Referer: ' . $_SERVER['HTTP_REFERER'] . "\n\n");
  fclose($f);
  /* */
  header('HTTP/1.1 403 Forbidden'); 
  exit();
}

// Check simple play limiter
// Just to keep the server from melting down...

/* TODO:
  Smarter per client limit tracking...
  * Does Flash pass SESSION cookies?
  * use session stamps - limit # of active sessions
    * can be passed around... - per session limits? (100)
*/
$limit = 50;
$limit_time = 300; // 5 minutes (~1 track)

$cache = new Memcached;
$cache->addServer('localhost', 11211) or die ('Could not connect');

$hits = $cache->get('bestof.ratelimiter');
if(!$hits) { $hits = 0; }
if($hits < $limit) {
  $cache->increment('bestof.ratelimiter', 1, 0, $limit_time);
} else {
  // More than hits_per_second!
  header('Content-type: text/plain');
  print 'Over the limit!';

  $f = fopen('limit', 'a');
  fwrite($f, date('r') . "\n");
  fclose($f);

  exit;
}

function shutdown() {
  $cache->decrement('bestof.ratelimiter');
}
register_shutdown_function('shutdown');






// Do we have a trackid?
$url = explode('/', $_SERVER['REQUEST_URI'], 4);
if(!$trackid = preg_replace('/[^0-9]/', '', $url[3])) {
  exit;
}


// Load bestof.json
// apc: 5ms; memcache: 7ms; file: 12ms;
if($_SERVER['QUERY_STRING'] == 'nocache') { $nocache = 1; }
if((!$bestof = $cache->get('bestof.json')) || $nocache) {
  if((!$bestof = $cache->get('bestof.json')) || $nocache) {
    $bestof = json_decode(file_get_contents('bestof.json'), true);
    $cache->set('bestof.json', $bestof);
  }
}


// Find track
foreach($bestof as $year) {
  foreach($year as $track) {
    if($track['id'] == $trackid) {
      $path = html_entity_decode(urldecode($track['file']));

      // Fixups
      switch($trackid) {
        case 19839:
          $path = "/locker/music/Amadou & Mariam - 2003 - Dimanche à Bamako/10 - La Paix.mp3";
          break;
        case 27589:
          // Unicode é (good) vs é (bad)
          $path = "/locker/music/Yann Tiersen - 2001 - Amelie OST (Le Fabuleux Destin d'Amélie Poulain)/02 - Yann Tiersen - Les jours tristes.mp3";
          break;
        case 44605:
          $path = "/locker/music/Bïa - 2008 - Nocturno/Bia-11 - Madalena.mp3";
          break;
        case 44423:
          $path = "/locker/music/Janelle Monáe - 2007 - Metropolis Suite I - The Chase/Janelle Monáe - Sincerely, Jane.mp3";
          break;
        case 39773:
          $path = "/locker/music/Detektivbyrån - 2006 - Hemvägen EP/03 - Nattöppet.mp3";
          break;
        case 39781:
          $path = "/locker/music/Detektivbyrån - 2006 - Hemvägen EP/07 - Vänerhavet.mp3";
          break;
        case 43933:
          $path = "/locker/music/CéU - 2007 - CéU/14 CéU - Bobagem.mp3";
          break;
      }

      if($debug) {
        print $path . "\n";
        exit;
      }

      if(is_file($file = $path)) {
        $s = stat($file);
        header('Content-type: audio/mpeg');
        header('Content-length: ' . $s['size']);
        header('Content-Disposition: inline;filename="'.$track['id'].'.mp3"');
        header("Content-Transfer-Encoding: chunked"); 
        if (isset($_SERVER['HTTP_RANGE'])) {
          rangeDownload($file);
	} else {
          readfile($file);
        }
      }
      exit();
    }
  }
}


// http://www.techstruggles.com/mp3-streaming-for-apple-iphone-with-php-readfile-file_get_contents-fail/
// http://www.thomthom.net/blog/2007/09/php-resumable-download-server/
// see also: https://stackoverflow.com/questions/11340276/make-mp3-seekable-php
function rangeDownload($file) {

	$fp = @fopen($file, 'rb');

	$size   = filesize($file); // File size
	$length = $size;           // Content length
	$start  = 0;               // Start byte
	$end    = $size - 1;       // End byte
	// Now that we've gotten so far without errors we send the accept range header
	/* At the moment we only support single ranges.
	 * Multiple ranges requires some more work to ensure it works correctly
	 * and comply with the spesifications: http://www.w3.org/Protocols/rfc2616/rfc2616-sec19.html#sec19.2
	 *
	 * Multirange support annouces itself with:
	 * header('Accept-Ranges: bytes');
	 *
	 * Multirange content must be sent with multipart/byteranges mediatype,
	 * (mediatype = mimetype)
	 * as well as a boundry header to indicate the various chunks of data.
	 */
	header("Accept-Ranges: 0-$length");
	// header('Accept-Ranges: bytes');
	// multipart/byteranges
	// http://www.w3.org/Protocols/rfc2616/rfc2616-sec19.html#sec19.2
	if (isset($_SERVER['HTTP_RANGE'])) {

		$c_start = $start;
		$c_end   = $end;
		// Extract the range string
		list(, $range) = explode('=', $_SERVER['HTTP_RANGE'], 2);
		// Make sure the client hasn't sent us a multibyte range
		if (strpos($range, ',') !== false) {

			// (?) Shoud this be issued here, or should the first
			// range be used? Or should the header be ignored and
			// we output the whole content?
			header('HTTP/1.1 416 Requested Range Not Satisfiable');
			header("Content-Range: bytes $start-$end/$size");
			// (?) Echo some info to the client?
			exit;
		}
		// If the range starts with an '-' we start from the beginning
		// If not, we forward the file pointer
		// And make sure to get the end byte if spesified
		if ($range0 == '-') {

			// The n-number of the last bytes is requested
			$c_start = $size - substr($range, 1);
		}
		else {

			$range  = explode('-', $range);
			$c_start = $range[0];
			$c_end   = (isset($range[1]) && is_numeric($range[1])) ? $range[1] : $size;
		}
		/* Check the range and make sure it's treated according to the specs.
		 * http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
		 */
		// End bytes can not be larger than $end.
		$c_end = ($c_end > $end) ? $end : $c_end;
		// Validate the requested range and return an error if it's not correct.
		if ($c_start > $c_end || $c_start > $size - 1 || $c_end >= $size) {

			header('HTTP/1.1 416 Requested Range Not Satisfiable');
			header("Content-Range: bytes $start-$end/$size");
			// (?) Echo some info to the client?
			exit;
		}
		$start  = $c_start;
		$end    = $c_end;
		$length = $end - $start + 1; // Calculate new content length
		fseek($fp, $start);
		header('HTTP/1.1 206 Partial Content');
	}
	// Notify the client the byte range we'll be outputting
	header("Content-Range: bytes $start-$end/$size");
	header("Content-Length: $length");

	// Start buffered download
	$buffer = 1024 * 8;
	while(!feof($fp) && ($p = ftell($fp)) <= $end) {

		if ($p + $buffer > $end) {

			// In case we're only outputtin a chunk, make sure we don't
			// read past the length
			$buffer = $end - $p + 1;
		}
		set_time_limit(0); // Reset time limit for big files
		echo fread($fp, $buffer);
		flush(); // Free up memory. Otherwise large files will trigger PHP's memory limit.
	}

	fclose($fp);

}
?>
