<?php

// DEBUG
// error_reporting(E_ERROR);
// header('Content-type: text/plain');
header('Content-type: application/xspf+xml');


// Timer
Timer::start();


// Should I try loading an existing list?
$url = explode('/', $_SERVER['REQUEST_URI'], 4);
if($listid = $url[3]) {
  $listid = preg_replace('/[^A-Za-z0-9]/', '', $listid);

  $list = json_decode(file_get_contents('lists/' . $listid), true);
  if($list) {
    print_list($list);
  }
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


// Make List!
$artists = array();
$playlist = array();
for($year=2000; $year<2010; $year++) {
  // Slots
  if($year < 2002) {
    $slots = 4;
  } else {
    $slots = 4;
  }

  // Randomize
  shuffle($bestof[$year]);

  while($bestof[$year] && $slots) {
    $track = array_pop($bestof[$year]);

    if(!check_artist($track['artist'])) {
      $playlist[] = $track; 
      add_artist($track['artist']);
      $slots--;
    }
    
  }
}

print_list($playlist);

/*** TODO:
Save list?
***/

// DEBUG (Timing)
print "\n\n<!-- " . Timer::time() . " -->";





/*****************/
/*** FUNCTIONS ***/
/*****************/


function add_artist($artist) {
  global $artists;

  // Fixups
  switch($artist) {
    case 'Bono':
      add_artist('U2');
  }


  $artists[] = strtoupper($artist);
}

function check_artist($artist) {
  global $artists;

  if(in_array(strtoupper($artist), $artists)) {
    return TRUE;
  } else {
    return FALSE;
  }
}


function print_list($list) {
  print '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
  print '<playlist version="1" xmlns="http://xspf.org/ns/0/">' . "\n";
  print '<title>Some Songs from the 2000s</title>' . "\n";
  print '<creator>Leonard Lin</creator>' . "\n";
  print '<info>http://randomfoo.net/bestof</info>' . "\n";
  print "<trackList>\n";

  foreach($list as $track) {
    print "  <track>\n";
    print "    <annotation>{$track['year']} - {$track['artist']} - {$track['title']}</annotation>\n";
    print "    <title>{$track['title']}</title>\n";
    print "    <creator>{$track['year']} - {$track['artist']}</creator>\n";

    if($track['album'] && strpos($track['album'], 'www') === FALSE) {
      print "    <album>{$track['album']}</album>\n";
    }

    print "    <duration>{$track['time']}</duration>\n";
    print "    <location>http://randomfoo.net/bestof/track/{$track['id']}</location>\n";
    print "  </track>\n";
  }
  print "</trackList>\n";
  print '</playlist>';
}


?>
