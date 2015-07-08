#Changelog
All notable changes will be documented in this file

## 0.7 - July 8th, 2015

- added Redis as cache. Queries are now cached for 24 hours. This serves as a base for the upcoming "frequent query refresh" functionality. Also added phpiredis for additional speed.
- fixed an undefined offset bug in the SearchHelper

## 0.6 - July 7th, 2015

- brought back Sass Reference results, now allowed to be indexed
- starting conversion to Htpl template engine
- added more information into about and FAQ
- added date range filters

## 0.5 - July 01st, 2015

- removed page flicker on load and added transition to logo (issue #11)
- added all around darker theme, removed silly rounded bootstrap corners
- optimized for smaller screens

## 0.4 - June 28th, 2015

- changed design (fixes #9)
- fixed keyword bug #10
- extracted more template fragments
- added FAQ page

## 0.3 - June 17th, 2015

- ability to search by Twitter handle (experimental)
- result information bar added, currently only contains count of total hits
- extracted some template fragments
- minor changes to examples modal

## 0.2.2 - June 16th, 2015

- some CSS fixes for cross browser support mainly

## 0.2.1 - June 16th, 2015

- fixed the "No results" message (was not displayed previously)
- fixed crash on no results
- made the author search be a little bit more creative. Instead of looking for the full author string at once, it will look for author strings containing all the words - so order of last and first name becomes irrelevant.

## 0.2 - June 15th, 2015

- added Google Analytics
- increased number of returned search results
- removed duplicate entries, post-fetch. Current limitation of Search API.
- added main image to search results, if any are available in article. If not, fallback to channel icon.
- updated README

## 0.1 - June 14th, 2015

Initial public release.