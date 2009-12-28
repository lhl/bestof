<!--
<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" width="400" height="500" id="mp3player" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" > 
  <param name="movie" value="/music/mp3player.swf?config=http://randomfoo.net/music/config.xml&file=http://randomfoo.net/bestof/list" /> 
  <param name="wmode" value="transparent" /> 
  <embed src="/music/mp3player.swf?config=http://randomfoo.net/music/config.xml&file=http://randomfoo.net/bestof/list" wmode="transparent" width="400" height="500" name ="mp3player" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" /> 
</object> 

<br />
-->


<object type="application/x-shockwave-flash" width="500" height="400"
data="http://randomfoo.net/bestof/player/xspf_player/xspf_player.swf?playlist_url=http://randomfoo.net/bestof/list">
<param name="movie" 
value="http://randomfoo.net/bestof/player/xspf_player/xspf_player.swf?playlist_url=http://randomfoo.net/bestof/list" />
</object>

<pre>

Code available: <a href="http://github.com/lhl/bestof/">http://github.com/lhl/bestof/</a>


Playlist Generation Algorithm:
* 4 tracks per year
* No repeated artists
* Per-year shuffle based on artist (not songs)
* TODO: (probably not) allow weighting?


Stats:

2000.json
---
# of songs:           54
# of artists:         38
# of unique artists:  38
# of list places:     3
song/place ratio:     18.0
artist/place ratio:   12.7
unique/place ratio:   12.7

2001.json
---
# of songs:           57
# of artists:         40
# of unique artists:  36
# of list places:     3
song/place ratio:     19.0
artist/place ratio:   13.3
unique/place ratio:   12.0

2002.json
---
# of songs:           92
# of artists:         60
# of unique artists:  50
# of list places:     5
song/place ratio:     18.4
artist/place ratio:   12.0
unique/place ratio:   10.0

2003.json
---
# of songs:           133
# of artists:         69
# of unique artists:  53
# of list places:     5
song/place ratio:     26.6
artist/place ratio:   13.8
unique/place ratio:   10.6

2004.json
---
# of songs:           107
# of artists:         70
# of unique artists:  39
# of list places:     3
song/place ratio:     35.7
artist/place ratio:   23.3
unique/place ratio:   13.0

2005.json
---
# of songs:           137
# of artists:         71
# of unique artists:  44
# of list places:     5
song/place ratio:     27.4
artist/place ratio:   14.2
unique/place ratio:   8.8

2006.json
---
# of songs:           139
# of artists:         92
# of unique artists:  63
# of list places:     5
song/place ratio:     27.8
artist/place ratio:   18.4
unique/place ratio:   12.6

2007.json
---
# of songs:           174
# of artists:         120
# of unique artists:  79
# of list places:     5
song/place ratio:     34.8
artist/place ratio:   24.0
unique/place ratio:   15.8

2008.json
---
# of songs:           153
# of artists:         109
# of unique artists:  70
# of list places:     5
song/place ratio:     30.6
artist/place ratio:   21.8
unique/place ratio:   14.0

2009.json
---
# of songs:           104
# of artists:         76
# of unique artists:  49
# of list places:     5
song/place ratio:     20.8
artist/place ratio:   15.2
unique/place ratio:   9.8

Total Artists:        521
Total Tracks:         1150
Total Places:         44

</pre>
