<pre>
<?php
$cache = new Memcached;
$cache->addServer('localhost', 11211) or die ("Could not connect");


/*** Song Rate ***/
$hits = $cache->get('bestof.ratelimiter');
print "Song Rate: $hits\n\n";


/*** Reading Logs ***/
print "<b>Referers</b>\n";

$log = '/www/randomfoo.net/logs/access.log';
$fp = fopen ($log, "r");
$size = 1000000;
$pos = sprintf("%u", filesize($log));

// Deal w/ PHP_INT_MAX
$pos = $pos - $size;
fseek($fp, $pos);

while (!feof ($fp)) {
  $line = fgets($fp);
  $chunks = explode('"', $line);

  if(strpos($chunks[1], '/bestof')) {
    $bits = explode('[', $chunks[0]);
    $lastdate = substr($bits[1], 0, -7);

    $referer = preg_replace('/\-/', '', $chunks[3]);
    if($referer && strpos($referer, 'http://randomfoo.net/') === FALSE) {
      $r[$referer]['lastdate'] = $lastdate;
      $r[$referer]['count'] += 1;
    }
  }
}
fclose($fp);

if (!$r) {
  print 'no recent referers';
} else {
  $r = array_reverse($r);
  foreach ($r as $k => $v) {
    $date_parts = explode(':', $v[lastdate], 2);
    $date = strtotime(str_replace('/', ' ', $date_parts[0]) . ' ' . $date_parts[1]);
    $date = date('Y-m-d H:i:s', $date);
    $count = sprintf('%4d', $v['count']);
    print "$date : $count : $k\n";
  }
}


?>
</pre>
