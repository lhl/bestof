#!/usr/bin/env python

import glob, json, sys


total_artists = {}
total_places = 0
for jsonfile in glob.glob('2*.json'):
  print
  print jsonfile
  print '---'

  tracks = json.load(open(jsonfile, 'r'))

  # Songs
  num_songs = len(tracks)

  # Artists
  artists = {}
  for track in tracks:
    try:
      artists[track['artist']] += 1
    except:
      try:
        artists[track['artist']] = 1
      except:
        print track

  num_artists = len(artists)

  # Unique Artists
  unique = 0
  for artist in artists:
    if artist in total_artists:
      total_artists[artist] += artists[artist]
    else:
      total_artists[artist] = artists[artist]
      unique += 1

  # ratios
  if unique < 40:
    places = 3
  elif unique < 80:
    places = 5

  total_places += places

  song_place = float(num_songs)/places
  artist_place = float(num_artists)/places
  unique_place = float(unique)/places

  # Print
  print "# of songs:          ", num_songs
  print "# of artists:        ", num_artists
  print "# of unique artists: ", unique
  print "# of list places:    ", places
  print "song/place ratio:     %2.1f" % song_place
  print "artist/place ratio:   %2.1f" % artist_place
  print "unique/place ratio:   %2.1f" % unique_place

  # of places
  # songs/playlist place
  # songs/artist

print
print "Total Places:        ", total_places
