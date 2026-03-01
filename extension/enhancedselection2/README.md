Enhanced selection datatype
---------------------------

The Enhanced selection type was initially based on the standard eZ Publish
selection (`ezselection`) datatype. It's original intent was to provide the
same functionality as the standard datatype, but store an identifier instead
of an ID in the contentobject attribute.

Later on, it was extended to provide more functionality, particularly on the
class level.

## What does it do?

The end result is exactly the same as the standard selection datatype. It
provides a dropdown list of the given options (multiselect is also supported).

The real enhancements are behind the scenes for most users, but they should make
the life of the developer/site admin a bit easier.


## Then what IS different?

There are two areas that are affected by the enhancements.

The easiest one is on the content object level. The standard selection datatype
stores the ID of the option you chose during the edit of an object. While this can
be ok for some, it was not what we wanted. If you change the options of the selection
datatype, the ID can point to the wrong option. The change we did is right there: it
no longer stores an ID, but the identifier of the option you picked. This enables the
datatype to track changes in the options as long as identifiers don't change. Where
does the identifier come from? Read on, and you'll find out :-)

The second area with enhancements is the content class edit. Several functionalities have been
added here in comparison to the standard selection datatype.

First of all, each option you add consists of two fields: the human readable form of
the option and the identifier. If you do not specify an identifier, the datatype will
generate one for you using the same mechanism as eZ Publish (class identifier, attribute
identifier, ...).

Furthermore, there are up and down buttons next to each row (= option). This way, you can
easily change the order of the options. The options in the final dropdown list will be 
displayed in the same order as you can see in the content class edit.

The datatype also supports information collection.

## I'm still not sure how it works

Well, why don't you try it then?

Install the extension, create a test class, add the datatype and play with it.

## How do I install it?

Please read [installation instructions](INSTALL.md)

## Version history

* v1.0:
    - Original SCK-CEN extension

* v1.1:
    - Added Solr indexing handler together with `fromString`/`toString` methods in datatype

* v2.0:
    - Rewrite extension to store the options in the database table instead of serialized array

* v2.0.1
    - Bugfix in migrate script

* v2.0.2
    - Add database schema file

## Disclaimer & copyright

Enhanced selection extension for eZ Publish 4.x
Copyright (C) 2003-2008  SCK-CEN (Belgian Nuclear Research Centre)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

This extension has been developed as part of projects inside the
Belgian Nuclear Research Centre (http://www.sckcen.be).

The extension is tailored to fit our needs, and is shared with the community as is.

YMMV!
