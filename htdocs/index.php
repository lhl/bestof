<!DOCTYPE html>
<html>
<head>
<title>Songs from the 2000s</title>
<script src="//ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="js/xspf_parser.js"></script>

<!-- SM2 -->
<link rel="stylesheet" type="text/css" href="js/soundmanager/demo/page-player/css/page-player.css">
<link rel="stylesheet" type="text/css" href="js/soundmanager/demo/page-player/css/optional-themes.css">
<link rel="stylesheet" type="text/css" href="js/soundmanager/demo/flashblock/flashblock.css" />
<script src="js/soundmanager/script/soundmanager2.js"></script>
<script src="js/soundmanager/demo/page-player/script/page-player.js"></script>

<style>
#playlist1 {
  font-family: monospace;
}
#playlist1 li {
  font-size: 1.2em;
  margin-bottom: 2px;
}

</style>
<script>
soundManager.setup({
  html5PollingInterval: 50
});

var PP_CONFIG = {
  autoStart: false,      // begin playing first sound when page loads
  playNext: true,        // stop after one sound, or play through list until end
  useThrottling: false,  // try to rate-limit potentially-expensive calls (eg. dragging position around)</span>
  usePeakData: false,     // [Flash 9 only] whether or not to show peak data (left/right channel values) - nor noticable on CPU
  useWaveformData: false,// [Flash 9 only] show raw waveform data - WARNING: LIKELY VERY CPU-HEAVY
  useEQData: false,      // [Flash 9 only] show EQ (frequency spectrum) data
  useFavIcon: false     // try to apply peakData to address bar (Firefox + Opera) - performance note: appears to make Firefox 3 do some temporary, heavy disk access/swapping/garbage collection at first(?) - may be too heavy on CPU
}
</script>
</head>
<body>
<pre>
Songs from the 2000s
======================================================================================
I've gone through way too much music this decade to make any sort of definitive
"best of" (also, isn't that a bit pompous? have these critics listened to everything out
there? then how can they say what's best? just sayin...)  Instead, what I've done is 
picked out a crapload of (almost 1200) songs I liked/listened to over the decade and am
dynamically generating a 4track x 10year playlist - hopefully this makes something that 
isn't impossible to slog through but that will give a good taste of what my musical 
experience has been like (both in terms of content, and the serendipity factor).

* Read more about why at <a href="http://randomfoo.net/2009/12/28/songs-from-the-2000s">http://randomfoo.net/2009/12/28/songs-from-the-2000s</a>

* Please don't directly link yet (this I think qualifies as fair use, but in the 
  interest of making sure this stays up long for friends to check out, please be 
  discreet) - I also have a pretty strict streaming limit set to keep my server from 
  melting down.

* If you don't like the playlist, just click reload. :)

* 2017-09-20 UPDATE: I've replaced the old player with SM2 (should have HTML5 support).
  The original Flash player version is <a href="flash">available here</a>.


<?php
$limit = 50;
$cache = new Memcached;
$cache->addServer('localhost', 11211) or die ("Could not connect");
$hits = $cache->get('bestof.ratelimiter');
if($hits >= $limit) {
  print "<b style='color:red'>WARNING: SONGS MAY NOT PLAY DUE TO RATE LIMITING IN EFFECT</b>\n";
} elseif($hits >= ($limit-10)) {
  print "<b style='color:orange'>WARNING: near rate limit</b>\n";
}
?>
</pre>

<div id="sm2-container">
<!-- SM2 flash goes here -->
</div>

<ul id="playlist1" class="playlist dark">
</ul>
<script>
  $.ajax({
    url: "https://randomfoo.net/bestof/list", 
    dataType: "xml",
    async: false,
    success: function(data) {
      // console.log(data);
      var jspf = XSPF.toJSPF(data);
      var tracks = jspf.playlist.track;
      for(i=0; i < tracks.length; i++) {
        $("#playlist1").append("<li><a class='playable' href='" + tracks[i].location[0] + "'>" + tracks[i].annotation + "</a></li>");
      }
    }
  });
</script>

<pre>


Playlist Generation Algorithm (THE RULES):
* 4 tracks per year
* No repeated artists
* Per-year shuffle based on artist (not songs)
* TODO: (probably not) allow weighting?


Special shoutz to Flickr for their super-painless REST/JSON <a href="https://www.flickr.com/services/api/flickr.interestingness.getList.html">interestingness feed</a>.


Code available here: <a href="https://github.com/lhl/bestof/">https://github.com/lhl/bestof/</a>


Stats:

2000
---
# of songs:           54
# of artists:         37
# of unique artists:  37
# of list places:     4
song/place ratio:     13.5
artist/place ratio:   9.2
unique/place ratio:   9.2

2001
---
# of songs:           57
# of artists:         40
# of unique artists:  36
# of list places:     4
song/place ratio:     14.2
artist/place ratio:   10.0
unique/place ratio:   9.0

2002
---
# of songs:           92
# of artists:         60
# of unique artists:  50
# of list places:     4
song/place ratio:     23.0
artist/place ratio:   15.0
unique/place ratio:   12.5

2003
---
# of songs:           133
# of artists:         69
# of unique artists:  53
# of list places:     4
song/place ratio:     33.2
artist/place ratio:   17.2
unique/place ratio:   13.2

2004
---
# of songs:           107
# of artists:         70
# of unique artists:  39
# of list places:     4
song/place ratio:     26.8
artist/place ratio:   17.5
unique/place ratio:   9.8

2005
---
# of songs:           137
# of artists:         71
# of unique artists:  44
# of list places:     4
song/place ratio:     34.2
artist/place ratio:   17.8
unique/place ratio:   11.0

2006
---
# of songs:           139
# of artists:         92
# of unique artists:  63
# of list places:     4
song/place ratio:     34.8
artist/place ratio:   23.0
unique/place ratio:   15.8

2007
---
# of songs:           174
# of artists:         120
# of unique artists:  79
# of list places:     4
song/place ratio:     43.5
artist/place ratio:   30.0
unique/place ratio:   19.8

2008
---
# of songs:           153
# of artists:         109
# of unique artists:  70
# of list places:     4
song/place ratio:     38.2
artist/place ratio:   27.2
unique/place ratio:   17.5

2009
---
# of songs:           104
# of artists:         76
# of unique artists:  49
# of list places:     4
song/place ratio:     26.0
artist/place ratio:   19.0
unique/place ratio:   12.2

Total Artists:        520
Total Tracks:         1150
Total Places:         40

</pre>
</body>
</html>
