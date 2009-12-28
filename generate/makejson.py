#!/usr/bin/env python

import json, re, sys
from BeautifulSoup import BeautifulStoneSoup

try:
  filename = sys.argv[1]
  year = filename[0:4]
except:
  filename = '2000.xml'
  year = '2000'

f = open('xml/' + filename, 'r')
s = BeautifulStoneSoup(f)

tracks = []

dicts = s.findAll('dict')
for d in dicts:
  if d.key.string == 'Track ID':

    keys = d.findAll('key')


    if len(keys) > 1:

      track = { 'year' : year }
      for k in keys:
        
        if k.string == 'Track ID':
          track['id'] = k.nextSibling.string
        elif k.string == 'Name':
          track['title'] = k.nextSibling.string
        elif k.string == 'Artist':
          track['artist'] = k.nextSibling.string

          # FOR COUNTING
          # print track['artist'].upper()

        elif k.string == 'Album':
          track['album'] = k.nextSibling.string
        elif k.string == 'Total Time':
          track['time'] = k.nextSibling.string
        elif k.string == 'Location':
          if re.search('/LAME/', k.nextSibling.string):
            track['file'] = re.sub('file://localhost/Volumes/2TB/Music/LAME', '/locker/music/LAME', k.nextSibling.string)
          else:
            track['file'] = re.sub('file://localhost/Volumes/2TB/Music/locker', '/locker/music', k.nextSibling.string)
          
      tracks.append(track)

f = open('json/' + year + '.json', 'w')
json.dump(tracks, f)
