#!/usr/bin/env python

import glob, json, sys


bestof = {}

for jsonfile in glob.glob('2*.json'):
  year = jsonfile[0:4]
  bestof[year] = json.load(open(jsonfile, 'r'))

json.dump(bestof, open('bestof.json', 'w'))
