# Cortex Representative Image Getter

## index dot htmx

Orange Logic's Cortex uses an API endpoint called PackageExtractor to handle compound objects.  In order to get the "display image" of the compound object, aka the thumbnail, or, in Cortex's vocabulary, the Representative Image, you use this `PE` API call.  Unfortunately you can only do one at a time.

This tool accepts a list of URLs of compound objects, separated by newlines, and returns the compound object's ID, the compound object's representative image ID, and the represntative image's IIIF url.  It will display the information in table form, but it also provides a "copy to clipboard" button that will give you the data in CSV format.  (This is not fancy magic, there's just a hidden div with the CSV data that the copy button is grabbing.)

It's built in HTMX and _hyperscript because one of the few benefits of working on a basically non-existent development team is that I can write whatever I want - and HTMX is cool.  _hyperscript is also cool, but admittedly very weird.  Luckily [senpai himself](https://twitter.com/htmx_org/status/1673354812094570497) made a copy button so I used that.

## urlapi dot php and api dot php

The APIs used in this tool are available externally, tho the one used by the HTMX page is admittedly a bit custom.  However, `api.php` is fully usable for anyone; it will be available on the Newberry website at digital dot new berry dot org slash cri slash api dot php  

/cri/, seriously?  Maybe, I don't know yet. That's the acronym for Cortex Representative Image, I didn't make that up, and as a developer I'm legally bound to use acronyms where possible.

api.php accepts comma-separeted `urls`, eg: `api.php?urls=https://url.com/etc,url.com/etc`

The urls can have http, or not, +/- the tld, whatever - it just looks for `/asset-management/` and takes all capital letters and numbers that follow it until there's something that isn't one.  It should be usable to the outside world, but I haven't tested it yet.
