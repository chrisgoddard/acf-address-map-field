Advanced Custom Fields: Address/Map Field
=====================

I wanted a better map field for ACF - the native Google Map field only returns a single string for the name, which makes display and schema harder.

This field uses the same search functionality for the map, but will then populate name, phone, website, email, address (line 1/2), City, State, Zip, Country, Lat/Long, and Google Map url.

Those fields can be individually edited after the initial population from Google.

Here is the field UI:

![advanced custom field address google map field](http://boom.cgoddard.com/image/2N0r3z2h2320/Screen%20Shot%202014-07-24%20at%202.08.04%20PM.png)


get_field() output looks like:

```
[name] => Space Needle
    [formatted_address] => 

										 Space Needle
										 400 Broad St
										 Seattle,
										 WA
										 98109
										 United States
										

    [address] => Array
        (
            [line_1] => 400 Broad St
            [line_2] => 
            [city] => Seattle
            [state] => WA
            [zip] => 98109
            [country] => United States
        )

    [info] => Array
        (
            [phone] => (206) 905-2100
            [website] => http://www.spaceneedle.com/
            [email] => 
            [google_map] => https://plus.google.com/103084617480514911872/about?hl=en-US
        )

    [position] => Array
        (
            [lat] => 47.620506
            [lng] => -122.34927700000003
        )

)

```

formatted_address is a schema markup of the address:

```
<div itemscope="" itemtype="http://schema.org/PostalAddress">
 <span itemprop="name">Space Needle</span>
 <span itemprop="streetAddress">400 Broad St</span>
 <span itemprop="addressLocality">Seattle</span>,
 <span itemprop="addressRegion">WA</span>
 <span itemprop="postalCode">98109</span>
 <span itemprop="addressCountry">United States</span>
</div>
```

WARNING: This field won't work with the_field(); - it's designed to return an array of data for a theme developer to use as they wish.
