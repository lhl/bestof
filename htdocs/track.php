<?php
header('Content-type: audio/mpeg');

// Discourage abuse...
if($_SERVER['HTTP_REFERER'] && strpos($_SERVER['HTTP_REFERER'], 'https://randomfoo.net/') !== 0) $exit++; 
if(strpos($_SERVER['HTTP_USER_AGENT'], 'Mozilla/') === FALSE) $exit++;
if(!$_SERVER['HTTP_REFERER'] && strpos($_SERVER['HTTP_USER_AGENT'], 'Gecko/')) $exit--;

// DEBUG
if($_SERVER['HTTP_USER_AGENT'] == 'bestof-debug') {
  $debug = 1;
}
$debug = 1;

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
  $hits++;
  $cache->set('bestof.ratelimiter', $hits, 0, $limit_time);
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
      }

      if($debug) {
        print $path . "\n";
        exit;
      }

      if(is_file($file = $path)) {
        $s = stat($file);
        header('Content-length: ' . $s['size']);
        readfile($file);
      }
      exit();
    }
  }
}
