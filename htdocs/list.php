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
if($_SERVER['QUERY_STRING'] == 'nocache') { $nocache = 1; }
if((!$bestof = apc_fetch('bestof.json')) || $nocache) {
  $cache = new Memcache;
  $cache->connect('localhost', 11211) or die ("Could not connect to Memcache");
  if((!$bestof = $cache->get('bestof.json')) || $nocache) {
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

  /*** Per-Artist Shuffle ***/
  $year_artists = array();
  foreach($bestof[$year] as $track) {
    $year_artists[strtoupper($track['artist'])][] = $track;  
  }

  shuffle($year_artists);

  while($year_artists && $slots) {
    $artist = array_pop($year_artists);
    if(!check_artist($artist[0]['artist'])) {
      shuffle($artist);
      $playlist[] = $track = array_pop($artist);
      add_artist($track['artist']);
      $slots--;
    }
  }

  /*** Per-Track Shuffling...
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
  ***/
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

    // Panda Power! (adds 5s to playlist generation)
    print "    <image>" . Panda::getPhoto() . "</image>\n";


    print "  </track>\n";
  }
  print "</trackList>\n";
  print '</playlist>';
}


/*** PANDA ***/
/*
  Hacked from http://www.flickr.com/explore/panda
  since the Pandas aren't super-well documented...
  * http://www.flickr.com/services/api/flickr.panda.getList.html
  * http://www.flickr.com/services/api/flickr.panda.getPhotos.html
*/
class Panda {
  // static $endpoint = 'http://www.flickr.com/services/rest/?method=flickr.streams.getStream&api_key=5f3f4b8e198c126160ab0033cc8ec324&stream_id=1&format=json';
  static $endpoint = 'http://www.flickr.com/services/rest/?method=flickr.panda.getPhotos&api_key=5f3f4b8e198c126160ab0033cc8ec324&per_page=200&panda_name=ling+ling&format=json';
  // static $endpoint = 'http://www.flickr.com/services/rest/?method=flickr.interestingness.getList&api_key=5f3f4b8e198c126160ab0033cc8ec324&per_page=200&format=json';
  static $photos = array();

  function askFlickr() {
    global $nocache;
    $cache = new Memcache;
    $cache->connect('localhost', 11211) or die ("Could not connect to Memcache");
    if((!$p = $cache->get('bestof.photos')) || $nocache) {
      $p = json_decode(substr(file_get_contents(self::$endpoint), 14, -1), true);
      $cache->set('bestof.photos', $p, 0, 86400);
    }
    self::$photos = $p['photos']['photo'];
    shuffle(self::$photos);
  }

  function getPhoto() {
    if(!self::$photos) { self::askFlickr(); }
    if(self::$photos) {
      $p = array_shift(self::$photos);
      $url = 'http://farm' . $p['farm'] . '.static.flickr.com/' . 
             $p['server'] . '/' . $p['id'] . '_' . $p['secret'] . '_m.jpg';
      return $url;
    }
  }

}

?>
