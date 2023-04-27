# tl - Simple tracklist viewer
I tend to like listening to DJ podcasts and mix CDs, especially trance,
and sometimes deep house, techno or synthwave. If I want to look up a
tracklist, then typical websites are heavy, bloated and slow.
This is intended to be a lean, mean, quick way to scratch this itch.
The data is obtained by web-scraping and processing, usually
a combination of Curl and Python.

Ultimately, you have a hierarchy
```
tl/
tl/index.txt
tl/pt/
tl/pt/tl_pt_001.txt
```
where `index.txt` is a map from abbreviation (e.g. `pt`) to the
podcast/series name (e.g. `Pure Trance Radio`). Beyond that
it is a simple Javascript Fetch to get the contents of
the text file via a trivial PHP backend, and some Javascript
and CSS to give a simple performant frontend. It is also
intended to be touch-screen friendly as I am often on my
Lenovo t470s, so it is designed with a laptop, possibly
with touchscreen, in mind. All else is optional. If I find
myself using it from my mobile or tablet, I may add extra
CSS etc. to handle such usage.

There are also keyboard shortcuts as I am generally very
keyboard-focused rather than mouse-focused.

All in, it's about 500 lines of PHP+HTML+Javascript, and
the best reference for current keyboard bindinds is to read
the source. Currently the are
```
C-arrowleft/right     next/prev hundreds
a/d                   next/prev unit
S-a/d                 next/prev tens
s/w                   next/prev tens
S-s/w                 next/prev hundreds
```

Most of the work is in `tl.php`.
