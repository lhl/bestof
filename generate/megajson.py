#!/usr/bin/env python

import glob, json, sys


bestof = {}

for jsonfile in glob.glob('json/*.json'):
  year = jsonfile[5:9]
  bestof[year] = json.load(open(jsonfile, 'r'))

json.dump(bestof, open('bestof.json', 'w'))
