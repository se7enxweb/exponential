FAQ
===

# CUSTOM DATATYPES

The following custom datatypes are also supported by this extension:

* [smileobjectrelationlistdatatype](https://github.com/arbito82/smileobjectrelationlistdatatype)

* [owenhancedselection](https://github.com/Open-Wide/OWEnhancedSelection)

* [ezbirthday](https://github.com/netgen/birthday)


# PLATFORM SPECIFIC NOTES

This solution supports content export in UTF8 by default.

If you wish to export content using another format, please create an export format output handler and use it via ini settings.

Alternatively if you do not wish to create an export format output handler than you can convert the export file yourself manually via some other means.


## UTF-8 BOM output format handler

There was a problem with opening files (via double click) in Microsoft Excel on Windows.

Therefore the following modification was made within the bccieExportFormatOutputHandlerUtf8Bom export format output handler:

    -echo( $outputStringInput );
    +$output = "\xEF\xBB\xBF" . $outputStringInput;
    +echo $output;

More information on this issue can be found here: [http://projects.ez.no/cie/forum/general/special_characters_problem](http://projects.ez.no/cie/forum/general/special_characters_problem)


## UTF-16LE output format handler

There was a problem with opening files (via double click) in Microsoft Excel on MacOS X.

Therefore the following modification was made within the bccieExportFormatOutputHandlerUtf16Le export format output handler:

    -echo( $outputStringInput );
    +$output = chr( 255 ) . chr( 254 ) . mb_convert_encoding( $outputStringInput, 'UTF-16LE', 'UTF-8' );
    +echo $output;

More information on this issue can be found here: [http://stackoverflow.com/questions/4348802/how-can-i-output-a-utf-8-csv-in-php-that-excel-will-read-properly](http://stackoverflow.com/questions/4348802/how-can-i-output-a-utf-8-csv-in-php-that-excel-will-read-properly)

## Windows-1251 aka cp1252 output format handler

Some users desired export in a windows specific output format so the bccieExportFormatOutputHandlerCP1252 was created.

More information on this charset can be found here: [http://en.wikipedia.org/wiki/Windows-1252](http://en.wikipedia.org/wiki/Windows-1252), [http://www.cp1252.com](http://www.cp1252.com)
