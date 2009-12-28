<?php
header('Content-type: audio/mpeg');

// Discourage abuse...
if($_SERVER['HTTP_REFERER']) exit; 
if(strpos($_SERVER["HTTP_USER_AGENT"], 'Mozilla/') === FALSE) exit;

/* DEBUG
$f = fopen('users', 'a');
fwrite($f, 'Browser: ' . $_SERVER["HTTP_USER_AGENT"] . "\n");
fwrite($f, 'Referer: ' . $_SERVER["'HTTP_REFERER'"] . "\n\n");
fclose($f);
*/

// Check simple play limiter
// Just to keep the server from melting down...
$hits_per_second = 4;
$cache = new Memcache;
$cache->connect('localhost', 11211) or die;
$hits = $cache->get('bestof:ratelimiter');
if(!$hits) { $hits = 0; }
if($hits < $hits_per_second) {
  $hits++;
  $cache->set('bestof:ratelimiter', $hits, 0, 1);
} else {
  // More than hits_per_second!
  header('Content-type: text/plain');
  print "Over the limit!";
  exit;
}




// Do we have a trackid?
$url = explode('/', $_SERVER['REQUEST_URI'], 4);
if(!$trackid = preg_replace('/[^0-9]/', '', $url[3])) {
  exit;
}


// Load bestof.json
// apc: 5ms; memcache: 7ms; file: 12ms;
if(! $bestof = apc_fetch('bestof.json')) {
  $cache = new Memcache;
  $cache->connect('localhost', 11211) or die ("Could not connect to Memcache");
  if(!$bestof = $cache->get('bestof.json')) {
    $bestof = json_decode(file_get_contents('bestof.json'), true);

    // apc
    apc_store('bestof.json', $bestof);

    // memcache
    $cache->set('bestof.json', $bestof);
  }
}


// Find track
foreach($bestof as $year) {
  foreach($year as $track) {
    if($track['id'] == $trackid) {
      $path = urldecode($track['file']);
      if(is_file($file = $path)) {
        $s = stat($file);
        header('Content-length: ' . $s['size']);
        readfile($file);
      }
      exit();
    }
  }
}
