<?php /* #?ini charset="utf8"?

[General]
ExportableDatatypes[]
ExportableDatatypes[]=ezboolean
ExportableDatatypes[]=eztext
ExportableDatatypes[]=ezinteger
ExportableDatatypes[]=ezstring
#ExportableDatatypes[]=eztime
ExportableDatatypes[]=ezurl
ExportableDatatypes[]=ezuser
ExportableDatatypes[]=ezxmltext
#ExportableDatatypes[]=ezboolean
ExportableDatatypes[]=ezdate
#ExportableDatatypes[]=ezdatetime
ExportableDatatypes[]=ezemail
ExportableDatatypes[]=ezfloat
ExportableDatatypes[]=ezidentifier
ExportableDatatypes[]=ezenhancedobjectrelation
ExportableDatatypes[]=ezenhancedselection
ExportableDatatypes[]=ezselection
ExportableDatatypes[]=ezenum
ExportableDatatypes[]=ezcountry
ExportableDatatypes[]=ezimage
ExportableDatatypes[]=ezmedia
ExportableDatatypes[]=ezbinaryfile
ExportableDatatypes[]=ezmatrix
ExportableDatatypes[]=ezobjectrelationlist
ExportableDatatypes[]=hmregexpline
ExportableDatatypes[]=ezprice
StripURLText=true

# you can place the handler files in your extension
# just enter the full path to the handler

[ezstring]
HandlerFile=extension/xrowextract/classes/parsers/xrowezstringhandler.php
HandlerClass=XroweZStringHandler

[ezenhancedselection]
HandlerFile=extension/xrowextract/classes/parsers/xrowezstringhandler.php
HandlerClass=XroweZStringHandler

[ezinteger]
HandlerFile=extension/xrowextract/classes/parsers/xrowezintegerhandler.php
HandlerClass=XroweZIntegerHandler

[ezxmltext]
HandlerFile=extension/xrowextract/classes/parsers/xrowezxmltexthandler.php
HandlerClass=XroweZXMLTextHandler

[ezidentifier]
HandlerFile=extension/xrowextract/classes/parsers/xrowezidentifierhandler.php
HandlerClass=XroweZIdentifierHandler

[ezfloat]
HandlerFile=extension/xrowextract/classes/parsers/xrowezfloathandler.php
HandlerClass=XroweZFloatHandler

[ezemail]
HandlerFile=extension/xrowextract/classes/parsers/xrowezemailhandler.php
HandlerClass=XroweZEmailHandler

[ezurl]
HandlerFile=extension/xrowextract/classes/parsers/xrowezurlhandler.php
HandlerClass=XroweZURLHandler

[eztext]
HandlerFile=extension/xrowextract/classes/parsers/xroweztexthandler.php
HandlerClass=XroweZTextHandler

[ezenhancedobjectrelation]
HandlerFile=extension/xrowextract/classes/parsers/xrowezenhancedobjectrelationhandler.php
HandlerClass=XroweZenhancedobjectrelationHandler
#false will simply output the IDs of the related objects
OutputRelatedObjectNames=true

[ezselection]
HandlerFile=extension/xrowextract/classes/parsers/xrowezselectionhandler.php
HandlerClass=XroweZSelectionHandler

[ezenum]
HandlerFile=extension/xrowextract/classes/parsers/xrowezenumhandler.php
HandlerClass=XroweZEnumHandler

[ezuser]
HandlerFile=extension/xrowextract/classes/parsers/xrowezuserhandler.php
HandlerClass=XroweZUserHandler

[ezdate]
HandlerFile=extension/xrowextract/classes/parsers/xrowezdatehandler.php
HandlerClass=XroweZDateHandler

[ezboolean]
HandlerFile=extension/xrowextract/classes/parsers/xrowezbooleanhandler.php
HandlerClass=XroweZBooleanHandler

[ezcountry]
HandlerFile=extension/xrowextract/classes/parsers/xrowezselectionhandler.php
HandlerClass=XroweZSelectionHandler

[ezimage]
HandlerFile=extension/xrowextract/classes/parsers/xrowezimagehandler.php
HandlerClass=XroweZImageExportHandler

[ezmedia]
HandlerFile=extension/xrowextract/classes/parsers/xrowezmediahandler.php
HandlerClass=XroweZMediaExportHandler

[ezbinaryfile]
HandlerFile=extension/xrowextract/classes/parsers/xrowezbinaryfilehandler.php
HandlerClass=XroweZBinaryfileExportHandler

[ezmatrix]
HandlerFile=extension/xrowextract/classes/parsers/xrowezmatrixhandler.php
HandlerClass=XroweZMatrixExportHandler

[ezobjectrelationlist]
HandlerFile=extension/xrowextract/classes/parsers/xrowezobjectrelationlisthandler.php
HandlerClass=XroweZObjectRelationListHandler

[hmregexpline]
HandlerFile=extension/xrowextract/classes/parsers/xrowhmregexplinehandler.php
HandlerClass=XrowhmregexplineHandler

[ezprice]
HandlerFile=extension/xrowextract/classes/parsers/xrowezpricehandler.php
HandlerClass=XroweZPriceHandler

*/ ?>