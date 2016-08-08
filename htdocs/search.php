<?php
error_reporting(E_ALL);
header('Content-type: text/plain');


// Should I try loading an existing list?
$url = explode('/', $_SERVER['REQUEST_URI'], 4);
if(!$search = strtoupper($url[3])) {
  exit;
}

// Load bestof.json
$cache = new Memcached;
$cache->addServer('localhost', 11211) or die ("Could not connect");
if($_SERVER['QUERY_STRING'] == 'nocache') { $nocache = 1; }
if((!$bestof = $cache->get('bestof.json')) || $nocache) {
  if((!$bestof = $cache->get('bestof.json')) || $nocache) {
    $bestof = json_decode(file_get_contents('bestof.json'), true);
    $cache->set('bestof.json', $bestof);
  }
}

foreach($bestof as $year) {
  foreach($year as $track) {
    if(strpos(strtoupper($track['artist']), $search) !== FALSE || 
       strpos(strtoupper($track['title']), $search) !== FALSE ) {
      $stat = stat(html_entity_decode(urldecode($track['file'])));
      // unset($track['file']);
      // unset($track['id']);
      print_r($track);
      print_r($stat);
      print "\n";
    }
  }
}

?>
