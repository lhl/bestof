<html>
<head>
  <title>Songs from the 2000s</title>
</head>
<body>
<!--
<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" width="400" height="500" id="mp3player" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" > 
  <param name="movie" value="/music/mp3player.swf?config=http://randomfoo.net/music/config.xml&file=http://randomfoo.net/bestof/list" /> 
  <param name="wmode" value="transparent" /> 
  <embed src="/music/mp3player.swf?config=http://randomfoo.net/music/config.xml&file=http://randomfoo.net/bestof/list" wmode="transparent" width="400" height="500" name ="mp3player" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" /> 
</object> 

<br />
-->

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
<object type="application/x-shockwave-flash" width="650" height="590" data="/bestof/player/xspf_player/xspf_player.swf?playlist_url=https://randomfoo.net/bestof/list">
<param name="movie" value="/bestof/player/xspf_player/xspf_player.swf?playlist_url=https://randomfoo.net/bestof/list" />
</object>


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
