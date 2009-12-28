<?php
header('Content-type: audio/mpeg');

// Discourage abuse...
if(strpos($_SERVER['HTTP_REFERER'], 'http://randomfoo.net/') !== 0) $exit++; 
if(strpos($_SERVER["HTTP_USER_AGENT"], 'Mozilla/') === FALSE) $exit++;
if(!$_SERVER['HTTP_REFERER'] && strpos($_SERVER["HTTP_USER_AGENT"], 'Gecko/')) $exit--;

// DEBUG
if($_SERVER["HTTP_USER_AGENT"] == 'bestof-debug') {
  $debug = 1;
}

if($exit && !$debug) {
  /* DEBUG */
  $f = fopen('users', 'a');
  fwrite($f, date('r') . "\n");
  fwrite($f, 'Browser: ' . $_SERVER["HTTP_USER_AGENT"] . "\n");
  fwrite($f, 'Referer: ' . $_SERVER["HTTP_REFERER"] . "\n\n");
  fclose($f);
  /* */
  exit();
}

// Check simple play limiter
// Just to keep the server from melting down...
$hits_per_second = 5;
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

  $f = fopen('limit', 'a');
  fwrite($f, date('r') . "\n");
  fclose($f);

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
      $path = html_entity_decode(urldecode($track['file']));

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
